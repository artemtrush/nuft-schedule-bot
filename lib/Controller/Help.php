<?php

namespace Controller;

class Help extends Base
{
    public function run($message)
    {

        $thunderstorm = u'\U0001F4A8';

        $text =
            '/start - текст' . PHP_EOL .
            '/help  - текст' . PHP_EOL .
            '/today - текст' . PHP_EOL .
            '/start - текст' . PHP_EOL .
            '/start - текст' . PHP_EOL .
            '/start - ' . $thunderstorm
        ;

        return $text;
    }
}
