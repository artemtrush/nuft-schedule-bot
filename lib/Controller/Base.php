<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;

abstract class Base
{
    protected $app;

    public function __construct(\Slim\App $app)
    {
        $this->app = $app;

        return $this;
    }

    public function __call($method, $arguments)
    {
        $app = $this->app;

        if (method_exists($app, $method)) {
            return call_user_func_array(array(&$app, $method), $arguments);
        }
    }

    public function log()
    {
        return $this->app->getContainer()['log'];
    }

    public function config()
    {
        return $this->app->getContainer()['config'];
    }

    public function run(callable $cb, ResponseInterface $response)
    {
        try {
            $result = call_user_func($cb);
        } catch (\Service\X $e) {
            $result = $e->getError();

            $this->log()->debug('Service Exception: ' . ($result['Error']['Message'] ?: $result['Error']['Type']));
        }

        $response = $response->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode($result));

        return $response;
    }

    public function renderJSON($data, $type = 'application/json')
    {
        return json_encode($data);
    }

    public function action($class)
    {
        return new $class([
            'log'    => $this->log(),
            'config' => $this->config()
        ]);
    }
}
