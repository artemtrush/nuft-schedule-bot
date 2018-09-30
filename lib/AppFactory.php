<?php

class AppFactory
{
    public static function create($config)
    {
        $bot = new \TelegramBot\Api\Client($config['telegram']['token']);

        $log = function () use ($config) {
            $log = new \Monolog\Logger($config['monolog']['name']);
            $handler = new \Monolog\Handler\StreamHandler($config['monolog']['filepath']);
            $log->pushHandler($handler);
            return $log;
        };

        $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
            ['/today', '/tomorrow'],
            ['/help', '/current_week', '/next_week']
        ]);

        $commands = ['start', 'help', 'group', 'today', 'tomorrow', 'current_week', 'next_week', 'date'];
        foreach ($commands as $command) {
            $controller = 'Controller\\' . ucfirst($command);
            $handler = new $controller([
                'log'    => $log(),
                'config' => $config
            ]);

            $bot->command($command, function ($message) use ($bot, $handler, $keyboard) {
                $answer = $handler->run($message);
                $bot->sendMessage($message->getChat()->getId(), $answer, 'html', false, null, $keyboard);
            });
        }

        return $bot;
    }
}