<?php

namespace Service;

abstract class Base
{
    private $log;
    private $config;
    private $error;

    public function __construct($attrs)
    {
        $this->log    = $attrs['log'] ?? null;
        $this->config = $attrs['config'] ?? null;
        $this->error  = $attrs['error'] ?? null;
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
                if (!isset($data['time']) || $data['time'] < time() - (7 * 24 * 60 * 60)) {
                    unlink($filename);
                } else if (isset($data['value'])) {
                    return $data['value'];
                }
            }
        }

        return null;
    }

    protected function setCache(string $key, mixed $value) {
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
