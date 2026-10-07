<?php

declare(strict_types=1);

use Phpugdd\Website\Calendar;
use Phpugdd\Website\Event;

$calendar = new Calendar('PHP USERGROUP DRESDEN e.V.', 'https://phpug-dresden.org');
$now = new DateTimeImmutable('2025-01-01 12:00:00', new DateTimeZone('UTC'));

return [
    'event start is converted from Berlin time to UTC' => static function () use ($calendar, $now): void {
        $ics = $calendar->render([new Event('2025-12-17', 'X-MAS', time: '18:30')], $now);

        assertContains("DTSTART:20251217T173000Z\r\n", $ics);
        assertContains("UID:2025-12-17@phpug-dresden.org\r\n", $ics);
    },
    'events without time start at 19:00 and link to the events page' => static function () use ($calendar, $now): void {
        $ics = $calendar->render([new Event('2025-07-01', 'Sommer')], $now);

        assertContains('DTSTART:20250701T170000Z', $ics);
        assertContains('URL:https://phpug-dresden.org/events.html#2025-07-01', $ics);
    },
    'special characters are escaped and long lines folded' => static function () use ($calendar, $now): void {
        $ics = $calendar->render([new Event('2025-07-01', str_repeat('Talks, Bier; ', 8))], $now);

        assertContains('SUMMARY:Talks\, Bier\; ', $ics);
        foreach (explode("\r\n", $ics) as $line) {
            assertSame(true, strlen($line) <= 75);
        }
    },
];
