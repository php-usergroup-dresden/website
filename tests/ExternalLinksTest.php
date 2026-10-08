<?php

declare(strict_types=1);

use Phpugdd\Website\ExternalLinks;

$links = new ExternalLinks('phpug-dresden.org');

return [
    'links to other hosts open in a new window' => static fn () => assertSame(
        '<a class="button" href="https://www.meetup.com/x/" target="_blank" rel="noopener">Platz sichern</a>',
        $links->openInNewWindow('<a class="button" href="https://www.meetup.com/x/">Platz sichern</a>'),
    ),
    'internal, relative and mail links stay in the same window' => static function () use ($links): void {
        $html = '<a href="/events.html">E</a> <a href="https://www.phpug-dresden.org/talks.html">T</a> <a href="mailto:info@phpug-dresden.org">M</a>';

        assertSame($html, $links->openInNewWindow($html));
    },
    'an existing target is respected' => static function () use ($links): void {
        $html = '<a href="https://example.org" target="_self">X</a>';

        assertSame($html, $links->openInNewWindow($html));
    },
];
