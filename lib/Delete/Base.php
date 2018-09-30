<?php

namespace Banks;

use Psr\Log\LoggerInterface;
use nusoap_client as SOAP;

abstract class Base
{
    private $authtype = 'basic';
    private $endpoint;
    private $login;
    private $password;
    protected $conf;
    protected $log;

    public function __construct(
        string $endpoint,
        array $config,
        LoggerInterface $logger,
        string $login = '',
        string $pass = ''
    ) {
        $this->endpoint = $endpoint;
        $this->conf = $config;
        $this->log = $logger;
        $this->login = $login;
        $this->password = $pass;
    }

    public function setEndpoint(string $endpoint) : void
    {
        $this->endpoint = $endpoint;
    }

    public function dataUrlencode(array $data) : string
    {
        $params = [];
        foreach ($data as $param => $value) {
            $params[] = "$param=" . urlencode($value);
        }

        return join('&', $params);
    }

    private function getPart($name, $value, $boundary)
    {
        $eol = "\r\n";
        $part = '--'. $boundary . $eol;
        $part .= 'Content-Disposition: form-data; name="' . $name . '"' . $eol . $eol;
        $part .= $value . $eol;

        return $part;
    }

    public function getMultipartFormData($data, $boundary)
    {
        $eol = "\r\n";
        $body = '';
        foreach ($data as $name => $value) {
            $body .= $this->getPart($name, $value, $boundary);
        }
        $body .= '--'. $boundary . '--';

        return $body;
    }

    protected function sendHttpGetRequest(array $data) : string
    {
        $encodedData = $this->dataUrlencode($data);
        $url = $this->endpoint . ($encodedData ? ('?' . $encodedData) : '');
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $out = curl_exec($curl);

        $this->log->info($url);
        $this->log->info('REQUEST: ' . json_encode($data));
        $this->log->info('RESPONSE: ' . $out);

        curl_close($curl);

        return $out;
    }

    protected function sendHttpPostRequest($data, $options = []) : string
    {
        $curl = curl_init();
        $url = $this->endpoint;

        if (isset($options['USERPWD'])) {
            curl_setopt($curl, CURLOPT_USERPWD, $options['USERPWD']);
        }

        if (isset($options['HTTPHEADER'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $options['HTTPHEADER']);
        }

        if (isset($options['URL'])) {
            $url = $options['URL'];
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $out = curl_exec($curl);
        curl_close($curl);

        return $out;
    }

    protected function sendSoapRequest(string $data, string $method) : array
    {
        $client = new SOAP($this->endpoint, true);
        $client->setCredentials($this->login, $this->password, $this->authtype);
        $client->soap_defencoding = 'utf-8';
        $client->decode_utf8 = false;
        $client->response_timeout = 600;

        if ($err = $client->getError()) {
            throw new \Error("Connection to webservice {$this->endpoint} failed. Error: $err");
        }

        $this->log->info('SOAP REQUEST: ' . $data);

        return $this->SOAPCall($client, $method, $data);
    }

    private function SOAPCall($client, string $method, string $params) : array
    {
        $result = $client->call($method, $params);
        $this->log->info("SOAP RESPONSE: " . json_encode($result));

        if ($client->fault || ($err = $client->getError())) {
            $this->log->info("SOAP ERROR: " . json_encode($err));
            return ['Answer' => 'ER', 'Method' => $method, 'Message' => $err, 'Result' => 'No result', 'Status' => 0];
        } elseif (isset($result['SendWebRequestDataResult']['WebRequestId']) && $result['SendWebRequestDataResult']['WebRequestId'] == 0) {
            $this->log->info("SOAP REJECT: " . json_encode($err));
            return ['Answer' => 'ER', 'Method' => $method, 'Message' => $err, 'Result' => 'No result', 'Status' => 2];
        }

        return ['Answer' => 'OK', 'Method' => $method, 'Message' => 'OK', 'Result' => $result, 'Status' => 1];
    }

    abstract public function sendRequest(int $id, array $params) : array;
}
