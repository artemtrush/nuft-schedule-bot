<?php

namespace Controller;

class Help extends Base
{
    public function run($message)
    {
        $text =
            'А вот полный список моих команд:
            /start - текст
            /help  - текст
            /today - текст
            /start - текст
            /start - текст
            /start - текст'
        ;

        return $text;
    }
}
