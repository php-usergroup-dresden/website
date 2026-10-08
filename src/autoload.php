<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefix = 'Phpugdd\\Website\\';
    if (str_starts_with($class, $prefix)) {
        require __DIR__ . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    }
});
