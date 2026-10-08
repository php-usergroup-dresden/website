<?php

declare(strict_types=1);

namespace Phpugdd\Website;

final readonly class Talk
{
    /** @param list<array{title: string, url: string}> $links */
    public function __construct(
        public string $event,
        public string $title,
        public string $speaker = '',
        public string $time = '',
        public string $speakerUrl = '',
        public string $format = '',
        public string $language = '',
        public string $abstract = '',
        public array $links = [],
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data, string $context): self
    {
        Validate::required($data, ['event', 'title'], $context);
        Validate::date((string) $data['event'], "$context.event");
        Validate::optionalTime($data, $context);

        return new self(
            event: $data['event'],
            title: $data['title'],
            speaker: $data['speaker'] ?? '',
            time: $data['time'] ?? '',
            speakerUrl: $data['speakerUrl'] ?? '',
            format: $data['format'] ?? '',
            language: $data['language'] ?? '',
            abstract: $data['abstract'] ?? '',
            links: Validate::links($data, $context),
        );
    }
}
