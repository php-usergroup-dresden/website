<?php

declare(strict_types=1);

function assertSame(mixed $expected, mixed $actual): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(sprintf("Erwartet:\n%s\nErhalten:\n%s", var_export($expected, true), var_export($actual, true)));
    }
}

function assertContains(string $needle, string $haystack): void
{
    if (!str_contains($haystack, $needle)) {
        throw new RuntimeException(sprintf("„%s“ nicht gefunden in:\n%s", $needle, $haystack));
    }
}

function assertThrows(string $exceptionClass, string $messagePart, callable $callable): void
{
    try {
        $callable();
    } catch (Throwable $e) {
        if (!$e instanceof $exceptionClass || !str_contains($e->getMessage(), $messagePart)) {
            throw new RuntimeException(sprintf('Falsche Exception %s: %s', $e::class, $e->getMessage()), 0, $e);
        }

        return;
    }
    throw new RuntimeException("$exceptionClass wurde nicht geworfen.");
}
