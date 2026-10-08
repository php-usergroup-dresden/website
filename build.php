<?php

declare(strict_types=1);

/*
 * Builds the static website into ./docs, which GitHub Actions deploys to GitHub Pages.
 *
 *   php build.php                     # build with today's date
 *   php build.php --today=2025-12-01  # pretend another date (decides "upcoming" vs. "past" events)
 */

use Phpugdd\Website\Build;
use Phpugdd\Website\InvalidContent;

require __DIR__ . '/src/autoload.php';

$options = getopt('', ['today:']);
$timezone = new DateTimeZone('Europe/Berlin');

try {
    $today = new DateTimeImmutable($options['today'] ?? 'today', $timezone);
    $pages = (new Build(__DIR__, __DIR__ . '/docs', $today))->run();
} catch (InvalidContent $e) {
    fwrite(STDERR, "Fehler in den Inhalten: {$e->getMessage()}\n");
    exit(1);
}

printf("%d Seiten nach docs/ geschrieben.\n", count($pages));
