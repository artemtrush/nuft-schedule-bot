<?php

namespace Controller;

class Group extends Base
{
    public function run($message)
    {
        $correctFormat = preg_match(
            '/^\/group\s+(' . self::GROUP_REGEXP . ')$/',
            trim($message->getText()),
            $matches
        );

        if (!$correctFormat) {
            return 'Неверный формат группы. Пример правильного формата можно посмотреть в /help';
        }

        $data = [
            'chatID'    => $message->getChat()->getId(),
            'groupName' => $matches[1],
        ];

        return $this->action('Service\Group\Сreate')->run($data);
    }
}
