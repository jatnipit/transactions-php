<?php

declare(strict_types=1);

use App\App;
use App\Config;
use App\Controllers\HomeController;
use App\Models\TransactionModel;
use App\Services\TransactionService;
use App\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

define('STORAGE_PATH', __DIR__ . '/../storage');
define('VIEW_PATH', __DIR__ . '/../views');

// Setup Dependencies
$config = new Config($_ENV);
$router = new Router();

// Initialize App first
$app = new App(
    $router,
    ['uri' => $_SERVER['REQUEST_URI'], 'method' => $_SERVER['REQUEST_METHOD']],
    $config
);

// Now create other dependencies
$transactionModel = new TransactionModel();
$transactionService = new TransactionService($transactionModel);
$homeController = new HomeController();

$router
    ->get('/', [$homeController, 'index'])
    ->post('/upload', [$homeController, 'upload'])
    ->get('/transactions', [$homeController, 'transactions']);

$app->run();
