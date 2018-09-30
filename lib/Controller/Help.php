<?php

namespace Controller;

class Help extends Base
{
    public function run($message)
    {

        $thunderstorm = urlencode('\x37\xE2\x83\xA3   sss  \xF0\x9F\x98\x81');

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
