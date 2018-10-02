<?php

namespace Controller;

class Sub extends Base
{
    public function run($message)
    {
        $correctFormat = preg_match(
            '/^\/sub\s+(on|off)$/',
            trim($message->getText()),
            $matches
        );

        if (!$correctFormat) {
            return 'Неверный формат сообщения. Пример правильного формата можно посмотреть в /help';
        }

        $data = [
            'chatID'       => $message->getChat()->getId(),
            'subscription' => $matches[1] == 'on' ? true : false,
        ];

        try {
            $result = $this->action('Service\Subscription\Update')->run($data);
        } catch (\Throwable $e) {
            $this->log()->error($e->getMessage());
            return $this->error();
        }

        return $result;
    }
}
