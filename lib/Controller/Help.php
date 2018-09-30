<?php

namespace Controller;

class Help extends Base
{
    public function run($message)
    {
        $text =
            'А вот полный список моих команд:' . PHP_EOL .
            '/start - текст' . PHP_EOL
            '/help  - текст' . PHP_EOL
            '/today - текст' . PHP_EOL
            '/start - текст' . PHP_EOL
            '/start - текст' . PHP_EOL
            '/start - текст'
        ;

        return $text;
    }
}
