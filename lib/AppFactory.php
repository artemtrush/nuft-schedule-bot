<?php
//@REMOVE?
// namespace

class AppFactory
{
    public static function create($config)
    {
        try {
        // $container = new \Slim\Container;

        // $container['config'] = $config;
        // $container['log'] = function () use ($config) {
        //     $log = new \Monolog\Logger($config['monolog']['name']);
        //     $handler = new \Monolog\Handler\StreamHandler($config['monolog']['filepath']);
        //     $log->pushHandler($handler);
        //     return $log;
        // };

            $bot = new \TelegramBot\Api\Client($config['telegram']['token']);

            $bot->command('start', function ($message) use ($bot) {
                $answer = 'Добро пожаловать!';
                $bot->sendMessage($message->getChat()->getId(), $answer);
            });

            $bot->command('help', function ($message) use ($bot) {
                $answer = 'Команды:
            /help - вывод справки';
                $bot->sendMessage($message->getChat()->getId(), $answer);
            });

            $bot->command('test', function ($message) use ($bot) {


                $answer = '1:';
                $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(array(array("one", "two", "three")), true);
                $bot->sendMessage($message->getChat()->getId(), $answer, null, false, null, $keyboard);

                $answer = '2:';
                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                    [
                        [
                            ['text' => 'link', 'url' => 'https://core.telegram.org']
                        ]
                    ]
                );
                $bot->sendMessage($message->getChat()->getId(), $answer, null, false, null, $keyboard);
            });

        } catch (\TelegramBot\Api\Exception $e) {
            $e->getMessage();
        }

        return $bot;
    }
}