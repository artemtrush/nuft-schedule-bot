<?php

class AppFactory
{
    public static function create($config)
    {
        $bot = new \TelegramBot\Api\Client($config['telegram']['token']);
        if (!$bot->getRawBody()) {
            exit;
        }

        $log = function () use ($config) {
            $log = new \Monolog\Logger($config['monolog']['name']);
            $handler = new \Monolog\Handler\StreamHandler($config['monolog']['filepath']);
            $log->pushHandler($handler);
            return $log;
        };

        $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
            ['/today', '/tomorrow'],
            ['/current_week', '/next_week']
        ], false, true, false);

        $commands = [
            'start',
            'help',
            'group',
            'today',
            'tomorrow',
            'current_week',
            'next_week',
            'date',
            'sub',
            'sub_time'
        ];

        foreach ($commands as $command) {
            $controller = 'Controller\\' . ucfirst($command);
            $handler = new $controller([
                'log'    => $log(),
                'config' => $config
            ]);

            try {
                $bot->command($command, function ($message) use ($bot, $handler, $keyboard) {
                    $answer = $handler->run($message);
                    $bot->sendMessage($message->getChat()->getId(), $answer, 'html', false, null, $keyboard);
                });
            } catch (\TelegramBot\Api\Exception $e) {
                $log()->error($e->getMessage());
            }
        }

        return $bot;
    }
}