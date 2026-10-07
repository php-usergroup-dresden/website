<?php

declare(strict_types=1);

/*
 * Dependency-free test runner: every tests/*Test.php returns an array of "description" => callable.
 * A test fails by throwing; assertions live in tests/assert.php.
 *
 *   php tests/run.php            # all tests
 *   php tests/run.php Markdown   # only tests whose file or description contains "Markdown"
 */

require __DIR__ . '/../src/autoload.php';
require __DIR__ . '/assert.php';

$filter = $argv[1] ?? '';
$failures = 0;
$count = 0;

foreach (glob(__DIR__ . '/*Test.php') as $file) {
    foreach (require $file as $name => $test) {
        $label = basename($file, '.php') . ': ' . $name;
        if ($filter !== '' && !str_contains($label, $filter)) {
            continue;
        }
        $count++;
        try {
            $test();
            echo "  ✔ $label\n";
        } catch (Throwable $e) {
            $failures++;
            echo "  ✘ $label\n    {$e->getMessage()}\n";
        }
    }
}

printf("\n%d Tests, %d fehlgeschlagen\n", $count, $failures);
exit($failures === 0 && $count > 0 ? 0 : 1);
