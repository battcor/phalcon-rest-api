<?php

error_reporting(E_ALL);

define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . DS . 'app');
define('EXT', '.php');
define('MODROOT', BASE_PATH . DS . 'vendor' . DS);

include MODROOT . 'autoload' . EXT;

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;

try {
    $di = new FactoryDefault();

    /**
     * Shared configuration service
     */
    $di->setShared('config', function () {
        $config = new Phalcon\Config\Adapter\Ini(BASE_PATH . '/config.ini');
        return $config;
    });

    $config = $di->getConfig();

    /**
     * Database connection is created based on the parameters defined in the configuration file
     */
    $di->setShared('db', function () use ($config) {
        $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
        switch ($config->database->adapter) {
            case 'Sqlite':
                $params = [
                    'dbname' => BASE_PATH . DS . $config->Sqlite->dbname,
                ];
                break;
            case 'Mysql':
                $params = [
                    'host'     => $config->Mysql->host,
                    'port'     => $config->Mysql->port,
                    'username' => $config->Mysql->username,
                    'password' => $config->Mysql->password,
                    'dbname'   => $config->Mysql->dbname,
                ];
                break;
            default:
                break;
        }
        $connection = new $class($params);
        return $connection;
    });

    /**
     * Registering an autoloader
     */
    $loader = new \Phalcon\Loader();

    $appConfig = $config->application;

    $loader->registerDirs(
        [
            BASE_PATH . $appConfig->exceptionsDir,
            BASE_PATH . $appConfig->helpersDir,
            BASE_PATH . $appConfig->librariesDir,
            BASE_PATH . $appConfig->modelsDir,
            BASE_PATH . $appConfig->resourcesDir,
        ]
    )->registerNamespaces(
        [
            'Catalyst\Exceptions' => BASE_PATH . $appConfig->exceptionsDir,
            'Catalyst\Helpers' => BASE_PATH . $appConfig->helpersDir,
            'Catalyst\Libraries' => BASE_PATH . $appConfig->librariesDir,
            'Catalyst\Models' => BASE_PATH . $appConfig->modelsDir,
            'Catalyst\Resources' => BASE_PATH . $appConfig->resourcesDir,
        ]
    )->register();

    $app = new Micro($di);

    include APP_PATH . '/app.php';

    $app->handle();
} catch (\Exception $e) {
    $app->response->setStatusCode($e->getCode(), $e->getMessage());
    $app->response->sendHeaders();
    echo $e->getMessage();
}
