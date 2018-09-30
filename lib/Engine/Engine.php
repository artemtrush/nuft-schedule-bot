<?php

namespace Engine;

use \PDO;

class Engine
{
    private static $connections = [];

    public static function setConnection(string $name, array $config)
    {
        if (isset(self::$connections[$name])) {
            return false;
        }
        $dsn = "{$config['type']}:dbname={$config['name']}";
        $username = $config['user'];
        $password = $config['password'];
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        self::$connections[$name] = new PDO($dsn, $username, $password, $opt);
        self::$connections[$name]->query("SET NAMES 'UTF8'");
    }

    public static function getConnection(string $name) : PDO
    {
        if (isset(self::$connections[$name])) {
            return self::$connections[$name];
        }
        return false;
    }
}
