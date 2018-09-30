<?php

namespace Service;

abstract class Base
{
    private $log;
    private $config;

    public function __construct($attrs)
    {
        $this->log = $attrs['log'] ?? null;
        $this->config = $attrs['config'] ?? null;
    }

    protected function log()
    {
        return $this->log;
    }

    protected function config()
    {
        return $this->config;
    }

    public function run(array $params = [])
    {
        try {
            $validated = $this->validate($params);
            $result = $this->execute($validated);

            return $result;
        } catch (\Exception $e) {
            $this->log()->error('Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
