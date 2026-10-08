<?php

declare(strict_types=1);

namespace Phpugdd\Website;

use DateTimeImmutable;

final readonly class Event
{
    private const DEFAULT_START = '19:00';

    /**
     * @param list<array{time: string, title: string}> $agenda
     * @param list<array{title: string, url: string}> $links
     * @param list<Talk> $talks
     */
    public function __construct(
        public string $date,
        public string $title,
        public string $time = '',
        public string $location = '',
        public string $map = '',
        public string $url = '',
        public string $image = '',
        public string $description = '',
        public array $agenda = [],
        public array $links = [],
        public array $talks = [],
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data, string $context): self
    {
        Validate::required($data, ['date', 'title'], $context);
        Validate::date((string) $data['date'], "$context.date");
        Validate::optionalTime($data, $context);

        $agenda = [];
        foreach ($data['agenda'] ?? [] as $index => $item) {
            Validate::required($item, ['title'], "$context.agenda[$index]");
            Validate::optionalTime($item, "$context.agenda[$index]");
            $agenda[] = ['time' => $item['time'] ?? '', 'title' => $item['title']];
        }

        return new self(
            date: $data['date'],
            title: $data['title'],
            time: $data['time'] ?? '',
            location: $data['location'] ?? '',
            map: $data['map'] ?? '',
            url: $data['url'] ?? '',
            image: $data['image'] ?? '',
            description: $data['description'] ?? '',
            agenda: $agenda,
            links: Validate::links($data, $context),
        );
    }

    /** @param list<Talk> $talks */
    public function withTalks(array $talks): self
    {
        return new self(
            $this->date,
            $this->title,
            $this->time,
            $this->location,
            $this->map,
            $this->url,
            $this->image,
            $this->description,
            $this->agenda,
            $this->links,
            $talks,
        );
    }

    public function start(): DateTimeImmutable
    {
        return new DateTimeImmutable(
            sprintf('%s %s', $this->date, $this->time !== '' ? $this->time : self::DEFAULT_START),
            new \DateTimeZone('Europe/Berlin'),
        );
    }

    public function year(): string
    {
        return substr($this->date, 0, 4);
    }

    public function isUpcoming(DateTimeImmutable $today): bool
    {
        return $this->date >= $today->format('Y-m-d');
    }

    /**
     * Agenda items and talks merged in chronological order; entries without a time keep their position at the end.
     *
     * @return list<array{time: string, title: string}|Talk>
     */
    public function program(): array
    {
        $program = [...$this->agenda, ...$this->talks];
        $timeOf = static fn (array|Talk $entry): string => $entry instanceof Talk ? $entry->time : $entry['time'];
        usort($program, static fn ($a, $b): int => ($timeOf($a) ?: '99:99') <=> ($timeOf($b) ?: '99:99'));

        return $program;
    }
}
