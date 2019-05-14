<?php

use Phalcon\DI\FactoryDefault as PhalconDI;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;

/**
 * Place routes here
 */
$app->post('/prospects/invite/generate', [new Catalyst\Resources\InviteGenerate(), 'actionPost']);
$app->post('/prospects/invite/validate', [new Catalyst\Resources\InviteValidate(), 'actionPost']);
$app->get('/prospects/invite/list', [new Catalyst\Resources\InviteList(), 'actionGet']);
$app->post('/prospects/invite/void', [new Catalyst\Resources\InviteVoid(), 'actionPost']);

/**
 * Invalid resource handler
 */
$app->notFound(function () use ($app) {
    throw Catalyst\Exceptions\Http404::resourceNotFound();
});

/**
 * Error handler
 */
$app->error(
    function ($exception) use ($app) {
        $code = $exception->getCode();
        $message = $exception->getMessage();
        $content = !empty($app->response->getContent()) ? $app->response->getContent() : $exception->getMessage();

        if (!$exception instanceof Catalyst\Exceptions\Http) {
            $code = 500;
            $message = 'Application failure';
        }

        $trace = json_encode($exception->getTraceAsString());

        $logLevel = $code == 500 ? Logger::CRITICAL : Logger::ERROR;

        $config = PhalconDI::getDefault()->get('config');
        $logger = new FileAdapter($config->log->dir . DS . $config->log->file);

        $logger->log(
            $exception->getMessage() . ' ' . $trace,
            $logLevel
        );

        $app->response->setStatusCode($code, $message)
            ->setContent($content)
            ->sendHeaders();

        return $app->response;
    }
);
