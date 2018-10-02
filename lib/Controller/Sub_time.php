<?php

namespace Controller;

class Sub_time extends Base
{
    public function run($message)
    {
        $correctFormat = preg_match(
            '/^\/sub_time\s+([0-2]\d:[0-6]\d)$/',
            trim($message->getText()),
            $matches
        );

        if (!$correctFormat) {
            return 'Неверный формат времени. Пример правильного формата можно посмотреть в /help';
        }

        $data = [
            'chatID'  => $message->getChat()->getId(),
            'subTime' => $matches[1],
        ];

        try {
            $result = $this->action('Service\Subscription\Create')->run($data);
        } catch (\Throwable $e) {
            $this->log()->error($e->getMessage());
            return $this->error();
        }

        return $result;
    }
}
