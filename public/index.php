<?php
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
$rootPath = dirname(__DIR__);

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {
    require_once $rootPath . '/vendor/autoload.php';
    $splObjectStorage = new SplObjectStorage();
    /**
     * Load .env configurations
     */
    Dotenv\Dotenv::create($rootPath)->load();

    $application = new \App\Application($rootPath);
    $application->run();
} catch (\Exception $e) {
/*      echo $e->getMessage() . '<br>';
      echo '<pre>' . $e->getTraceAsString() . '</pre>';*/
}
