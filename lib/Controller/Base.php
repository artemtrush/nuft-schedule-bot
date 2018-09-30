<?php

namespace Controller;

abstract class Base
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function log()
    {
        return $this->app['log'];
    }

    public function config()
    {
        return $this->app['config'];
    }

    public function action($class)
    {
        return new $class([
            'log'    => $this->log(),
            'config' => $this->config()
        ]);
    }
}
