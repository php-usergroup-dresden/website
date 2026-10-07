<?php

declare(strict_types=1);

namespace Phpugdd\Website;

final readonly class Content
{
    /**
     * @param array<string, mixed> $site
     * @param list<Event> $events newest first
     * @param list<array<string, string>> $sponsors
     * @param list<array<string, string>> $team
     * @param list<array<string, string>> $partners
     */
    public function __construct(
        public array $site,
        public array $events,
        public array $sponsors,
        public array $team,
        public array $partners,
    ) {
    }

    public static function fromDirectory(string $dir): self
    {
        $site = self::readJson("$dir/site.json");
        Validate::required($site, ['name', 'baseUrl'], 'site.json');

        return new self(
            site: $site,
            events: self::eventsWithTalks(self::readJson("$dir/events.json"), self::readJson("$dir/talks.json")),
            sponsors: self::entries(self::readJson("$dir/sponsors.json"), ['name', 'url', 'logo'], 'sponsors.json'),
            team: self::entries(self::readJson("$dir/team.json"), ['name', 'image'], 'team.json'),
            partners: self::partners(self::readJson("$dir/partners.json")),
        );
    }

    /**
     * @param list<array<string, mixed>> $eventData
     * @param list<array<string, mixed>> $talkData
     * @return list<Event>
     */
    public static function eventsWithTalks(array $eventData, array $talkData): array
    {
        $talksByEvent = [];
        foreach ($talkData as $index => $data) {
            $talk = Talk::fromArray($data, "talks.json[$index]");
            $talksByEvent[$talk->event][] = $talk;
        }

        $events = [];
        foreach ($eventData as $index => $data) {
            $event = Event::fromArray($data, "events.json[$index]");
            if (isset($events[$event->date])) {
                throw new InvalidContent(sprintf('events.json[%d]: Es gibt bereits ein Event am %s.', $index, $event->date));
            }
            $events[$event->date] = $event->withTalks($talksByEvent[$event->date] ?? []);
            unset($talksByEvent[$event->date]);
        }

        if ($talksByEvent !== []) {
            throw new InvalidContent(sprintf(
                'talks.json: Talks verweisen auf unbekannte Events: %s',
                implode(', ', array_keys($talksByEvent)),
            ));
        }

        krsort($events);

        return array_values($events);
    }

    /** @return list<Event> */
    public function upcomingEvents(\DateTimeImmutable $today): array
    {
        return array_reverse(array_values(array_filter($this->events, static fn (Event $e): bool => $e->isUpcoming($today))));
    }

    /** @return list<Event> */
    public function pastEvents(\DateTimeImmutable $today): array
    {
        return array_values(array_filter($this->events, static fn (Event $e): bool => !$e->isUpcoming($today)));
    }

    /**
     * @param list<array<string, mixed>> $list
     * @param list<string> $required
     * @return list<array<string, string>>
     */
    private static function entries(array $list, array $required, string $file): array
    {
        foreach ($list as $index => $entry) {
            Validate::required($entry, $required, "{$file}[$index]");
        }

        return $list;
    }

    /**
     * @param list<array<string, mixed>> $list
     * @return list<array<string, string>>
     */
    private static function partners(array $list): array
    {
        $partners = self::entries($list, ['name', 'url', 'logo', 'kind'], 'partners.json');
        foreach ($partners as $index => $partner) {
            if (!in_array($partner['kind'], ['community', 'cooperation'], true)) {
                throw new InvalidContent(sprintf('partners.json[%d].kind: "community" oder "cooperation" erwartet.', $index));
            }
        }

        return $partners;
    }

    /** @return array<mixed> */
    private static function readJson(string $file): array
    {
        if (!is_file($file)) {
            throw new InvalidContent(sprintf('Datei %s fehlt.', $file));
        }
        try {
            return json_decode((string) file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidContent(sprintf('%s: ungültiges JSON (%s).', basename($file), $e->getMessage()), 0, $e);
        }
    }
}
