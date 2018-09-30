<?php

namespace Controller;

class Help extends Base
{
    public function run($message)
    {
        $text =
            '/start - текст' . PHP_EOL .
            '/help  - текст' . PHP_EOL .
            '/today - текст' . PHP_EOL .
            '/start - текст' . PHP_EOL .
            '/start - текст' . PHP_EOL .
            '/start - '
        ;

        return $text;
    }
}
