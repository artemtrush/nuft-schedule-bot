<?php

namespace Controller;

class Help extends Base
{
    public function run($message)
    {

        $EmojiUtf8Byte = '\x37\xE2\x83\xA3';

        $pattern = '@\\\x([0-9a-fA-F]{2})@x';
        $emoji = preg_replace_callback(
          $pattern,
          function ($captures) {
            return chr(hexdec($captures[1]));
          },
          $EmojiUtf8Byte
        );


        $text =
            '/start - текст' . PHP_EOL .
            '/help  - текст' . PHP_EOL .
            '/today - текст' . PHP_EOL .
            '/start - текст' . PHP_EOL .
            '/start - текст' . PHP_EOL .
            '/start - ' . $emoji
        ;

        return $text;
    }
}
