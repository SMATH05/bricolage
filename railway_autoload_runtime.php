<?php

if (true === (require_once __DIR__ . '/autoload.php') || empty($_SERVER['SCRIPT_FILENAME'])) {
    return;
}

$app = require $_SERVER['SCRIPT_FILENAME'];

if (!is_object($app)) {
    throw new TypeError(sprintf('The file "%s" must return a PHP object, got "%s".', $_SERVER['SCRIPT_FILENAME'], get_debug_type($app)));
}

$runtime = $_SERVER['APP_RUNTIME'] ?? 'Symfony\\Component\\Runtime\\SymfonyRuntime';
$runtime = new $runtime($_SERVER);

[$app, $args] = $runtime
    ->getResolver($app)
    ->resolve();

$app = $app(...$args);

exit(
    $runtime
        ->getRunner($app)
        ->run()
);
