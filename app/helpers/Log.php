<?php
namespace Catalyst\Helpers;

use Phalcon\DI\FactoryDefault as PhalconDI;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;

class Log
{
    const LEVEL_ERROR = Logger::ERROR;
    const LEVEL_CRITICAL = Logger::CRITICAL;
    const LEVEL_DEBUG = Logger::DEBUG;
    const LEVEL_INFO = Logger::INFO;

    /**
     * Writes messages into log file
     * 
     * @param array $data array of messages
     * @param integer $level leve of error message
     * 
     * @return void
     */
    public static function add($data, $level = Logger::ERROR)
    {
        $config = PhalconDI::getDefault()->get('config');
        $logger = new FileAdapter($config->log->dir . DS . $config->log->file);

        $logger->log(json_encode($data), $level);
    }
}
