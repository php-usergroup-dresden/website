<?php

declare(strict_types=1);

namespace Phpugdd\Website;

final class Validate
{
    /**
     * @param array<string, mixed> $data
     * @param list<string> $keys
     */
    public static function required(array $data, array $keys, string $context): void
    {
        foreach ($keys as $key) {
            if (!is_string($data[$key] ?? null) || trim($data[$key]) === '') {
                throw new InvalidContent(sprintf('%s: Pflichtfeld "%s" fehlt oder ist leer.', $context, $key));
            }
        }
    }

    public static function date(string $value, string $context): void
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if ($date === false || $date->format('Y-m-d') !== $value) {
            throw new InvalidContent(sprintf('%s: "%s" ist kein Datum im Format YYYY-MM-DD.', $context, $value));
        }
    }

    /** @param array<string, mixed> $data */
    public static function optionalTime(array $data, string $context): void
    {
        $time = $data['time'] ?? '';
        if ($time !== '' && !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', (string) $time)) {
            throw new InvalidContent(sprintf('%s.time: "%s" ist keine Uhrzeit im Format HH:MM.', $context, $time));
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return list<array{title: string, url: string}>
     */
    public static function links(array $data, string $context): array
    {
        $links = [];
        foreach ($data['links'] ?? [] as $index => $link) {
            self::required($link, ['title', 'url'], "$context.links[$index]");
            $links[] = ['title' => $link['title'], 'url' => $link['url']];
        }

        return $links;
    }
}
