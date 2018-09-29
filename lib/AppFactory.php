<?php
//@REMOVE?
// namespace

class AppFactory
{
    public static function create($config)
    {
        // $container = new \Slim\Container;

        // $container['config'] = $config;
        // $container['log'] = function () use ($config) {
        //     $log = new \Monolog\Logger($config['monolog']['name']);
        //     $handler = new \Monolog\Handler\StreamHandler($config['monolog']['filepath']);
        //     $log->pushHandler($handler);
        //     return $log;
        // };


        $token = '696268889:AAGMD4oB75zg6kLA9ym6lLCb4j3WQPkzaeM';
        $bot = new \TelegramBot\Api\Client($token);

        $bot->command('start', function ($message) use ($bot) {
            $answer = 'Добро пожаловать!';
            $bot->sendMessage($message->getChat()->getId(), $answer);
        });

        $bot->command('help', function ($message) use ($bot) {
            $answer = 'Команды:
        /help - вывод справки';
            $bot->sendMessage($message->getChat()->getId(), $answer);
        });

        // $app = new Slim\App($container);
        // $app->post('/order/', [new Controller\Order($app), 'create']);

        return $bot;
    }
}
