<?php

namespace Service;

abstract class Base
{
    private $log;
    private $config;

    public function __construct($attrs)
    {
        $this->log = $attrs['log'] ?? null;
        $this->config = $attrs['config'] ?? null;
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

    protected function log()
    {
        return $this->log;
    }

    protected function config()
    {
        return $this->config;
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
