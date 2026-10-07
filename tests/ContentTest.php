<?php

declare(strict_types=1);

use Phpugdd\Website\Content;
use Phpugdd\Website\InvalidContent;
use Phpugdd\Website\Talk;

$event = static fn (string $date, array $extra = []): array => ['date' => $date, 'title' => "Meetup $date", ...$extra];
$talk = static fn (string $event, string $time = '', string $title = 'Talk'): array => ['event' => $event, 'title' => $title, 'speaker' => 'Jane', 'time' => $time];

return [
    'events are sorted newest first and talks are attached' => static function () use ($event, $talk): void {
        $events = Content::eventsWithTalks([$event('2024-01-10'), $event('2025-03-01')], [$talk('2024-01-10')]);

        assertSame(['2025-03-01', '2024-01-10'], array_map(static fn ($e) => $e->date, $events));
        assertSame(1, count($events[1]->talks));
    },
    'talk referencing an unknown event is rejected' => static fn () => assertThrows(
        InvalidContent::class,
        'unbekannte Events: 2024-02-02',
        static fn () => Content::eventsWithTalks([$event('2024-01-10')], [$talk('2024-02-02')]),
    ),
    'duplicate event dates are rejected' => static fn () => assertThrows(
        InvalidContent::class,
        'bereits ein Event am 2024-01-10',
        static fn () => Content::eventsWithTalks([$event('2024-01-10'), $event('2024-01-10')], []),
    ),
    'missing required field names file and index' => static fn () => assertThrows(
        InvalidContent::class,
        'events.json[0]: Pflichtfeld "title"',
        static fn () => Content::eventsWithTalks([['date' => '2024-01-10']], []),
    ),
    'invalid date and time formats are rejected' => static function () use ($event): void {
        assertThrows(InvalidContent::class, 'YYYY-MM-DD', static fn () => Content::eventsWithTalks([$event('10.01.2024')], []));
        assertThrows(InvalidContent::class, 'HH:MM', static fn () => Content::eventsWithTalks([$event('2024-01-10', ['time' => '7pm'])], []));
    },
    'program merges agenda and talks chronologically' => static function () use ($event, $talk): void {
        $agenda = ['agenda' => [['time' => '18:30', 'title' => 'Doors Open'], ['time' => '21:00', 'title' => 'Socializing']]];
        [$result] = Content::eventsWithTalks([$event('2024-01-10', $agenda)], [$talk('2024-01-10', '19:45', 'Main'), $talk('2024-01-10', '19:00', 'Lightning')]);

        $titles = array_map(static fn ($entry) => $entry instanceof Talk ? $entry->title : $entry['title'], $result->program());
        assertSame(['Doors Open', 'Lightning', 'Main', 'Socializing'], $titles);
    },
    'upcoming events include today and are sorted soonest first' => static function () use ($event): void {
        $content = new Content([], Content::eventsWithTalks([$event('2024-01-01'), $event('2024-03-01'), $event('2024-02-01')], []), [], [], []);
        $today = new DateTimeImmutable('2024-02-01');

        assertSame(['2024-02-01', '2024-03-01'], array_map(static fn ($e) => $e->date, $content->upcomingEvents($today)));
        assertSame(['2024-01-01'], array_map(static fn ($e) => $e->date, $content->pastEvents($today)));
    },
];
