<?php
$root = __DIR__;
while (!file_exists($root . '/vendor/autoload.php')) {
    $parent = dirname($root);
    if ($parent === $root) {
        throw new RuntimeException('Unable to locate vendor/autoload.php above ' . __DIR__);
    }
    $root = $parent;
}

require_once $root . '/vendor/autoload.php';
require_once $root . '/dev/tests/unit/framework/bootstrap.php';

// Test the sources next to this file, not the copy composer autoloads from vendor/.
$moduleDir = dirname(__DIR__);
spl_autoload_register(function (string $class) use ($moduleDir): void {
    $prefix = 'Swissup\\SearchMysqlLegacy\\';
    if (str_starts_with($class, $prefix)) {
        $file = $moduleDir . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
}, true, true);
