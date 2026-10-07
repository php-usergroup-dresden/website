<?php

declare(strict_types=1);

namespace Phpugdd\Website;

/** Renders events as an iCalendar feed (RFC 5545) so people can subscribe to the meetup dates. */
final readonly class Calendar
{
    private const DURATION = 'PT3H';

    public function __construct(
        private string $name,
        private string $baseUrl,
    ) {
    }

    /** @param list<Event> $events */
    public function render(array $events, \DateTimeImmutable $now): string
    {
        $host = (string) parse_url($this->baseUrl, PHP_URL_HOST);
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            "PRODID:-//$host//events//DE",
            'CALSCALE:GREGORIAN',
            'X-WR-CALNAME:' . $this->escape($this->name),
        ];

        foreach ($events as $event) {
            $start = $event->start()->setTimezone(new \DateTimeZone('UTC'));
            $lines = [
                ...$lines,
                'BEGIN:VEVENT',
                "UID:{$event->date}@$host",
                'DTSTAMP:' . $now->setTimezone(new \DateTimeZone('UTC'))->format('Ymd\THis\Z'),
                'DTSTART:' . $start->format('Ymd\THis\Z'),
                'DURATION:' . self::DURATION,
                'SUMMARY:' . $this->escape($event->title),
                'URL:' . ($event->url !== '' ? $event->url : "{$this->baseUrl}/events.html#{$event->date}"),
                ...($event->location !== '' ? ['LOCATION:' . $this->escape($event->location)] : []),
                'END:VEVENT',
            ];
        }
        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", array_map($this->fold(...), $lines)) . "\r\n";
    }

    private function escape(string $text): string
    {
        return str_replace(['\\', ';', ',', "\n"], ['\\\\', '\;', '\,', '\n'], $text);
    }

    /** Lines longer than 75 octets must be folded; continuation lines start with a space. */
    private function fold(string $line): string
    {
        $parts = [];
        while (strlen($line) > 75) {
            $cut = 75;
            while ($cut > 0 && (ord($line[$cut]) & 0xC0) === 0x80) {
                $cut--;
            }
            $parts[] = substr($line, 0, $cut);
            $line = ' ' . substr($line, $cut);
        }
        $parts[] = $line;

        return implode("\r\n", $parts);
    }
}
