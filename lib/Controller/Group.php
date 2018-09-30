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

        try {
            $result = $this->action('Service\Group\Create')->run($data);
        } catch (\Throwable $e) {
            $this->log()->error($e->getMessage());
            return $this->error();
        }

        return $result;
    }
}
