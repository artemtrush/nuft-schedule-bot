<?php

namespace Banks\Card;

final class Admitad extends \Banks\Base
{
    private const BANKS = [
        18990 => [
            'name' => 'Monobank',
            'fields' => ['mobile_phone', 'product_type'],
            'values' => ['product_type' => 'card'],
        ],
        17398 => [
            'name' => 'CreditDnepr',
            'fields' => ['last_name', 'first_name', 'middle_name', 'mobile_phone'],
            'values' => [],
        ],
        16126 => [
            'name' => 'PrivatBank',
            'fields' => ['first_name', 'mobile_phone', 'email', 'product_type'],
            'values' => ['product_type' => 1],
        ],
        14605 => [
            'name' => 'ShvidkoGroshi',
            'fields' => ['last_name', 'first_name', 'middle_name', 'mobile_phone'],
            'values' => [],
        ],
        17288 => [
            'name' => 'CashPoint',
            'fields' => ['last_name', 'first_name', 'middle_name', 'mobile_phone', 'inn', 'credit_sum'],
            'values' => [],
        ],
        19059 => [
            'name' => 'Ecredit',
            'fields' => ['last_name', 'first_name', 'middle_name', 'mobile_phone', 'inn', 'email'],
            'values' => [],
        ],
        16243 => [
            'name' => 'PUMB',
            'fields' => ['last_name', 'first_name', 'middle_name', 'mobile_phone', 'inn', 'product_type', 'birth_date'],
            'values' => ['product_type' => 'Кредитна картка ВСЕМОЖУ'],
        ],
    ];

    public function sendRequest(int $id, array $params) : array
    {
        $this->log->info('Send admitad(' . implode(',', array_column(self::BANKS, 'name')) . ') card requests');

        $access_token = $this->getToken();
        if (!$access_token) {
            $this->log()->error('Empty admitad access token');
            return $this->treatResponse(array());
        }

        $data = [
            'first_name'   => $params['FirstName'],
            'last_name'    => $params['SecondName'],
            'inn'          => $params['StateCode'],
            'mobile_phone' => $params['Phone'],
            'product_type' => 'card',
            'middle_name'  => 'Отчество',
            'email'        => 'email@email.com',
            'birth_date'   => '01.01.1990',
            'credit_sum'   => '1'
        ];

        $boundary = 'BOUNDARY--' . md5(time());
        $options = [
            'HTTPHEADER' => [
                'Authorization: Bearer ' . $access_token,
                'content-type: multipart/form-data; boundary=' . $boundary
            ]
        ];

        $generalResponse = [
            'errors'    => [],
            'responses' => [],
        ];

        foreach (self::BANKS as $bankID => $bank) {
            $bankData = [
                'subid'     => $id,
                'campaigns' => '[' . $bankID . ']',
            ];

            foreach ($bank['fields'] as $field) {
                if (!empty($bank['values'][ $field ])) {
                    $bankData[ $field ] = $bank['values'][ $field ];
                } else {
                    $bankData[ $field ] = $data[ $field ];
                }
            }

            $this->log->info( $bank['name'] . ' DATA: '. json_encode($bankData) );

            $multipartData = $this->getMultipartFormData($bankData, $boundary);
            $out = $this->sendHttpPostRequest($multipartData, $options);

            $this->log->info( $bank['name'] . ' RESPONCE: '.  $out );

            $response = json_decode($out, 1);

            if (!empty($response['errors'])) {
                $generalResponse['errors'] = array_merge($generalResponse['errors'], $response['errors']);
            }

            if (!empty($response['responses'])) {
                $generalResponse['responses'] = array_merge($generalResponse['responses'], $response['responses']);
            }
        }

        return $this->treatResponse($generalResponse);
    }

    private function treatResponse($response) : array
    {
        $treatment = [];
        $successBidStatuses = ['processing', 'approved'];
        $banks = array_combine(array_keys(self::BANKS), array_column(self::BANKS, 'name'));

        foreach ($banks as $bankName) {
            $treatment[ $bankName ] = ['Status' => 0];
        }

        if (isset($response['responses'])) {
            foreach ($response['responses'] as $resp) {
                if (in_array($resp['status'], $successBidStatuses)) {
                    $treatment[ $banks[ $resp['campaign_id'] ] ] = ['Status' => 1];
                }
            }
        }

        if (isset($response['errors'])) {
            foreach ($response['errors'] as $err) {
                $treatment[ $banks[ $err['campaign_id'] ] ] = ['Status' => 0];
            }
        }

        return $treatment;
    }

    private function getToken() : string
    {
        try {
            $this->pdo = \Engine\Engine::getConnection('main');
        } catch (\PDOException $e) {
            $this->log()->error($e->getMessage());
            return '';
        }

        $params = $this->getAuthParams();

        if (time() - $params['expires_in'] >= $params['token_time'] && $params['refresh_token']) {
            $params = $this->refreshToken($params['refresh_token']);
        }

        if (!$params['access_token']) {
            $params = $this->createToken();
        }

        return $params['access_token'];
    }


    private function createToken() : array
    {
        $admitadConf = $this->conf['banks']['admitad']['api'];

        $data = [
            'client_id'  => $admitadConf['client_id'],
            'scope'      => $admitadConf['scope'],
            'grant_type' => 'client_credentials',
        ];

        $options = [
            'USERPWD' => $admitadConf['client_id'] . ':' . $admitadConf['client_secret'],
            'URL'     => $admitadConf['authUrl']
        ];

        $this->log->info('Create admitad access token');
        $out = $this->sendHttpPostRequest($this->dataUrlencode($data), $options);

        $response = json_decode($out, 1);

        return $this->treatTokenResponse($response);
    }

    private function refreshToken(string $token) : array
    {
        $admitadConf = $this->conf['banks']['admitad']['api'];

        $data = [
            'client_id'     => $admitadConf['client_id'],
            'client_secret' => $admitadConf['client_secret'],
            'grant_type'    => 'refresh_token',
            'refresh_token' => $token,
        ];

        $options = [
            'URL' => $admitadConf['authUrl']
        ];

        $this->log->info('Refresh admitad access token');
        $out = $this->sendHttpPostRequest($this->dataUrlencode($data), $options);

        $response = json_decode($out, 1);

        return $this->treatTokenResponse($response);
    }

    private function treatTokenResponse($response) : array
    {
        if (!empty($response['error'])) {
            $this->log->error('Admitad auth error:' . $response['error']);
        }

        $params = [
            'access_token'  => $response['access_token']  ?? '',
            'refresh_token' => $response['refresh_token'] ?? '',
            'token_time'    => time(),
            'expires_in'    => $response['expires_in']    ?? 0,
        ];

        $this->setAuthParams($params);
        return $params;
    }

    private function getAuthParams() : array
    {
        $query = "
            SELECT * from `site_settings` WHERE `key` IN
            ('admitad_access_token', 'admitad_refresh_token', 'admitad_token_time', 'admitad_expires_in')
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $params = array();
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $params[ $row['key'] ] = $row['value'];
        }

        return [
            'access_token'  => $params['admitad_access_token']  ?? '',
            'refresh_token' => $params['admitad_refresh_token'] ?? '',
            'token_time'    => $params['admitad_token_time']    ?? 0,
            'expires_in'    => $params['admitad_expires_in']    ?? 0,
        ];
    }

    private function setAuthParams($params) : void
    {
        $sql = "
            REPLACE INTO `site_settings`
            VALUES (:key, :value)
        ";

        foreach ($params as $key => $value) {
            $key = 'admitad_' . $key;
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':key',   $key,   \PDO::PARAM_STR);
            $stmt->bindValue(':value', $value, \PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}
