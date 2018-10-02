<?php

namespace Controller;

abstract class Base
{
    protected $app;
    public const DATE_FORMAT  = 'd.m.Y';
    public const DATE_REGEXP  = '[0-3][0-9]\.[0-1][0-9]\.[1-2][0-9][0-9][0-9]';

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
            'config' => $this->config(),
            'error'  => $this->error()
        ]);
    }

    public function error()
    {
        return 'Упс! Кажется что-то пошло не так. Пожалуйста, попробуйте позже или обратитесь к разработчику.';
    }
}
