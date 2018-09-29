<?php

ini_set('default_socket_timeout', 600);
date_default_timezone_set('Europe/Kiev');

require_once __DIR__ . '/../vendor/autoload.php';
$appConf = include_once __DIR__ . '/../etc/app-conf.php';

// Engine\Engine::setConnection('db', $appConf['dbConfig']);
$app = AppFactory::create($appConf);
$app->run();
