<?php

namespace Controller;

class Start extends Base
{
    public function run($message)
    {
        $answer = 'Полный список команд:' . PHP_EOL;
        $help = $this->action('Controller\Help')->run($message);
        return $help;
    }
}
