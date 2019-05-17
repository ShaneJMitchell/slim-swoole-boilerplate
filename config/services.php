<?php
/**
 * Each service we want ready needs to be loaded into the app container here.
 */
$container = $app->getContainer();

// Setup Database connections (if any)
// TODO: This will need to be updated to accommodate multiple db connections if needed, and perhaps use MYSQLi instead of PDO if needed - Or we can hook in an ORM as well if needed
$container['db'] = function () {
    $db = require __DIR__ . '/../config/database.php';
    $pdo = new PDO($db['connection'] . ':host=' . $db['host'] . ';port=' . $db['port'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;
};

// Setup logging using Monolog with various log levels implemented.
$container['logger'] = function() {
    return new \Monolog\Logger('slim-test', [
        new \Monolog\Handler\StreamHandler(__DIR__ . '/../logs/critical.log', \Monolog\Logger::CRITICAL, false),
        new \Monolog\Handler\StreamHandler(__DIR__ . '/../logs/error.log', \Monolog\Logger::ERROR, false),
        new \Monolog\Handler\StreamHandler(__DIR__ . '/../logs/warning.log', \Monolog\Logger::WARNING, false),
        new \Monolog\Handler\StreamHandler(__DIR__ . '/../logs/info.log', \Monolog\Logger::INFO, false),
        // Uncomment the below to also log to stdout (for containerized infrastructures like Docker and/or Kubernetes)
//        new \Monolog\Handler\StreamHandler("php://stdout", \Monolog\Logger::INFO)
    ]);
};

// Set each available controller into the container for the router
// TODO: abstract this to automatically create an instance for each controller in the App\Controllers namespace
$container[\App\Controllers\BaseController::class] = function ($container) {
    return new \App\Controllers\BaseController($container->get('logger'));
};
