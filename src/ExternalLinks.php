<?php

declare(strict_types=1);

namespace Phpugdd\Website;

/** Makes links to other hosts open in a new window, so visitors keep the user group page open. */
final readonly class ExternalLinks
{
    public function __construct(private string $siteHost)
    {
    }

    public function openInNewWindow(string $html): string
    {
        return (string) preg_replace_callback(
            '/<a\s[^>]*href="(https?:\/\/[^"]+)"[^>]*>/i',
            fn (array $m): string => $this->isExternal($m[1]) && !str_contains($m[0], ' target=')
                ? substr($m[0], 0, -1) . ' target="_blank" rel="noopener">'
                : $m[0],
            $html,
        );
    }

    private function isExternal(string $url): bool
    {
        $host = (string) parse_url(html_entity_decode($url), PHP_URL_HOST);

        return strcasecmp(preg_replace('/^www\./i', '', $host), preg_replace('/^www\./i', '', $this->siteHost)) !== 0;
    }
}
