<?php

// Robust autoload_runtime.php for Railway
if (true === (require_once __DIR__ . '/autoload.php') || empty($_SERVER['SCRIPT_FILENAME'])) {
    return;
}

$app = require $_SERVER['SCRIPT_FILENAME'];

if (!is_object($app)) {
    throw new TypeError(sprintf('The file "%s" must return a PHP object, got "%s".', $_SERVER['SCRIPT_FILENAME'], get_debug_type($app)));
}

if (is_string($_SERVER['APP_RUNTIME_OPTIONS'] ??= $_ENV['APP_RUNTIME_OPTIONS'] ?? [])) {
    $_SERVER['APP_RUNTIME_OPTIONS'] = json_decode($_SERVER['APP_RUNTIME_OPTIONS'], true, 512, JSON_THROW_ON_ERROR);
}

$_SERVER['APP_RUNTIME'] ??= $_ENV['APP_RUNTIME'] ?? 'Symfony\\Component\\Runtime\\SymfonyRuntime';
$runtimeClass = $_SERVER['APP_RUNTIME'];

$runtime = new $runtimeClass($_SERVER['APP_RUNTIME_OPTIONS'] += [
    'project_dir' => dirname(__DIR__, 1),
]);

[$app, $args] = $runtime
    ->getResolver($app)
    ->resolve();

$app = $app(...$args);

exit(
    $runtime
        ->getRunner($app)
        ->run()
);
