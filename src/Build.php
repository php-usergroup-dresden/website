<?php

declare(strict_types=1);

namespace Phpugdd\Website;

final readonly class Build
{
    public function __construct(
        private string $rootDir,
        private string $outputDir,
        private \DateTimeImmutable $today,
    ) {
    }

    /** @return list<string> written page paths */
    public function run(): array
    {
        $content = Content::fromDirectory("{$this->rootDir}/data");
        $markdown = new Markdown();
        $renderer = new Renderer("{$this->rootDir}/templates", $markdown, [
            'site' => $content->site,
            'today' => $this->today,
            'assetVersion' => substr((string) md5_file("{$this->rootDir}/static/css/site.css"), 0, 8),
        ]);

        $this->resetOutputDir();
        $this->copyDirectory("{$this->rootDir}/static", $this->outputDir);

        $pages = $this->generatedPages($content);
        foreach ($content->site['pages'] ?? [] as $index => $page) {
            Validate::required($page, ['path', 'source'], "site.json.pages[$index]");
            $body = $this->readContent($page['source']);
            $pages[$page['path']] = ['page', ['title' => $this->titleOf($body, $page['path']), 'body' => $body, 'description' => $page['description'] ?? null]];
        }

        foreach ($pages as $path => [$template, $vars]) {
            $vars = ['description' => null, ...$vars, 'path' => $path];
            $html = $renderer->render('layout', [...$vars, 'main' => $renderer->render($template, $vars)]);
            $this->write($path, $html);
        }

        $calendar = new Calendar($content->site['name'], $content->site['baseUrl']);
        $this->write('/events.ics', $calendar->render($content->events, new \DateTimeImmutable('now')));
        $this->write('/sitemap.xml', $this->sitemap($content->site['baseUrl'], array_diff(array_keys($pages), ['/404.html'])));

        return array_keys($pages);
    }

    /** @return array<string, array{string, array<string, mixed>}> path => [template, variables] */
    private function generatedPages(Content $content): array
    {
        $upcoming = $content->upcomingEvents($this->today);
        $past = $content->pastEvents($this->today);

        return [
            '/index.html' => ['home', [
                'title' => '',
                'upcoming' => $upcoming,
                'recent' => array_slice($past, 0, 3),
                'team' => $content->team,
                'sponsors' => $content->sponsors,
                'partners' => $content->partners,
            ]],
            '/events.html' => ['events', [
                'title' => 'Events',
                'description' => 'Meetups, Stammtische und Developer Days der PHP USERGROUP DRESDEN.',
                'upcoming' => $upcoming,
                'past' => $past,
            ]],
            '/talks.html' => ['talks', [
                'title' => 'Talks',
                'description' => 'Alle Vorträge, die bei der PHP USERGROUP DRESDEN gehalten wurden.',
                'events' => array_values(array_filter($content->events, static fn (Event $e): bool => $e->talks !== [])),
            ]],
            '/sponsoring.html' => ['sponsoring', [
                'title' => 'Sponsoring',
                'description' => 'Unterstütze die PHP USERGROUP DRESDEN als Sponsor.',
                'intro' => $this->readContent('sponsoring.md'),
                'sponsors' => $content->sponsors,
                'partners' => $content->partners,
            ]],
            '/404.html' => ['404', ['title' => 'Seite nicht gefunden']],
        ];
    }

    private function readContent(string $source): string
    {
        $file = "{$this->rootDir}/content/$source";
        if (!is_file($file)) {
            throw new InvalidContent(sprintf('Inhaltsdatei content/%s fehlt.', $source));
        }

        return (string) file_get_contents($file);
    }

    private function titleOf(string $markdown, string $path): string
    {
        if (!preg_match('/^#\s+(.+)$/m', $markdown, $m)) {
            throw new InvalidContent(sprintf('Seite %s braucht eine Überschrift "# Titel".', $path));
        }

        return trim($m[1]);
    }

    /** @param list<string> $paths */
    private function sitemap(string $baseUrl, array $paths): string
    {
        $urls = array_map(
            static fn (string $path): string => sprintf('<url><loc>%s</loc></url>', htmlspecialchars($baseUrl . ($path === '/index.html' ? '/' : $path))),
            $paths,
        );

        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . implode('', $urls) . "</urlset>\n";
    }

    private function write(string $path, string $contents): void
    {
        $file = $this->outputDir . $path;
        if (!is_dir(dirname($file)) && !mkdir(dirname($file), 0o755, true)) {
            throw new \RuntimeException(sprintf('Verzeichnis für %s konnte nicht angelegt werden.', $file));
        }
        file_put_contents($file, $contents);
    }

    private function resetOutputDir(): void
    {
        if (is_dir($this->outputDir)) {
            $items = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->outputDir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST,
            );
            foreach ($items as $item) {
                $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
            }
        }
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0o755, true);
        }
    }

    private function copyDirectory(string $from, string $to): void
    {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($from, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
        );
        foreach ($items as $item) {
            $target = $to . substr($item->getPathname(), strlen($from));
            if (str_contains(substr($item->getPathname(), strlen($from)), '/.')) {
                continue;
            }
            if ($item->isDir()) {
                is_dir($target) || mkdir($target, 0o755, true);
                continue;
            }
            copy($item->getPathname(), $target);
        }
    }
}
