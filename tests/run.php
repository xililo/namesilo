<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/TestCase.php';

$files = glob(__DIR__ . '/*Test.php');
$classes = [];

foreach ($files as $file) {
    require_once $file;
    $classes[] = 'Xililo\\Namesilo\\Tests\\' . basename($file, '.php');
}

$tests = 0;

foreach ($classes as $class) {
    $instance = new $class();
    $methods = array_filter(get_class_methods($instance), static fn (string $method): bool => str_starts_with($method, 'test'));

    foreach ($methods as $method) {
        $instance->{$method}();
        $tests++;
        echo '.';
    }
}

echo PHP_EOL . sprintf('Passed %d tests.', $tests) . PHP_EOL;
