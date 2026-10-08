<?php

declare(strict_types=1);

namespace Phpugdd\Website;

/**
 * Renders plain PHP templates. Inside a template `$this` is the renderer, so helpers are available
 * as `$this->e()`, `$this->md()`, `$this->date()` and `$this->partial()`.
 */
final readonly class Renderer
{
    private const WEEKDAYS = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
    private const MONTHS = [
        1 => 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
        'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember',
    ];

    /** @param array<string, mixed> $globals available in every template */
    public function __construct(
        private string $templateDir,
        private Markdown $markdown,
        private array $globals = [],
    ) {
    }

    /** @param array<string, mixed> $vars */
    public function render(string $template, array $vars = []): string
    {
        $file = "{$this->templateDir}/$template.php";
        if (!is_file($file)) {
            throw new \LogicException(sprintf('Template %s nicht gefunden.', $file));
        }

        return (function (string $__file, array $__vars): string {
            extract($__vars);
            ob_start();
            try {
                include $__file;

                return (string) ob_get_contents();
            } finally {
                ob_end_clean();
            }
        })($file, [...$this->globals, ...$vars]);
    }

    /** @param array<string, mixed> $vars */
    public function partial(string $name, array $vars = []): string
    {
        return $this->render("partials/$name", $vars);
    }

    public function e(?string $text): string
    {
        return htmlspecialchars($text ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function md(string $markdown): string
    {
        return $this->markdown->toHtml($markdown);
    }

    public function mdInline(string $markdown): string
    {
        return $this->markdown->inline($this->e($markdown));
    }

    public function date(string $isoDate, bool $withWeekday = true): string
    {
        $date = new \DateTimeImmutable($isoDate);
        $formatted = sprintf('%d. %s %s', $date->format('j'), self::MONTHS[(int) $date->format('n')], $date->format('Y'));

        return $withWeekday ? self::WEEKDAYS[(int) $date->format('w')] . ', ' . $formatted : $formatted;
    }
}
