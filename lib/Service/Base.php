<?php

namespace Service;

abstract class Base
{
    private $pdo;
    private $log;
    private $config;
    private $error;

    public function __construct($attrs)
    {
        $this->log    = $attrs['log'] ?? null;
        $this->config = $attrs['config'] ?? null;
        $this->error  = $attrs['error'] ?? null;
        try {
            $this->pdo = \Engine\Engine::getConnection('db');
        } catch (\PDOException $e) {
            $this->log()->error($e->getMessage());
            $this->pdo = null;
        }
    }

    protected function getUser(int $chatID) {
        $query = "
            SELECT `users`.*, `groups`.`name` AS 'groupName'
            FROM `users`
            INNER JOIN `groups` ON `users`.`groupID` = `groups`.`id`
            WHERE `users`.`chatID` = :chatID
        ";

        $stmt = $this->pdo()->prepare($query);
        $stmt->bindParam(':chatID', $chatID, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    protected function sendPostRequest($data, $url) : string
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>  http_build_query($data),
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            $this->log()->error( 'Curl error: ' . $error );
        }

        return $response;
    }

    protected function createEmoji(string $emojiUtf8Byte) {
        $pattern = '@\\\x([0-9a-fA-F]{2})@x';

        return preg_replace_callback(
            $pattern,
            function ($captures) {
                return chr(hexdec($captures[1]));
            },
            $emojiUtf8Byte
        );
    }

    protected function getCache(string $key) {
        if ($key) {
            $filename = $this->config()['cache']['dir'] . $key;
            if (file_exists($filename)) {
                $data = unserialize(file_get_contents($filename));
                if (isset($data['value'])) {
                    return $data['value'];
                }
            }
        }

        return null;
    }

    protected function setCache(string $key, $value) {
        $data = serialize([
            'time'  => time(),
            'value' => $value
        ]);

        $dir = $this->config()['cache']['dir'];
        if (file_exists($dir)) {
            file_put_contents($dir . $key, $data);
            return true;
        } else {
            $this->log()->error('Cache directory not exists');
        }

        return false;
    }

    protected function pdo()
    {
        return $this->pdo;
    }

    protected function log()
    {
        return $this->log;
    }

    protected function config()
    {
        return $this->config;
    }

    protected function error()
    {
        return $this->error;
    }

    protected function userGroupNotFoundError()
    {
        //@TODOT
        return 'Перед тем как , нужно отправить мне название группы /group';
    }

    public function validate(array $params) {
        return $params;
    }

    public function run(array $params = [])
    {
        try {
            $validated = $this->validate($params);
            $result = $this->execute($validated);

            return $result;
        } catch (\Exception $e) {
            $this->log()->error('Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
