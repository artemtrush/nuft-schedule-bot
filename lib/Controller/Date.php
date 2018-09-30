<?php

namespace Controller;

class Date extends Base
{
    public function run($message)
    {
        $correctFormat = preg_match(
            '/^\/date\s+(' . self::DATE_REGEXP . ')\s+('. self::DATE_REGEXP .')$/',
            trim($message->getText()),
            $matches
        );

        if (!$correctFormat) {
            return 'Неверный формат даты. Пример правильного формата можно посмотреть в /help';
        }

        $data = [
            'chatID'    => $message->getChat()->getId(),
            'startDate' => $matches[1],
            'endDate'   => $matches[2]
        ];

        return $this->action('Service\Schedule\Show')->run($data);
    }
}
