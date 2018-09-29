<?php

namespace Banks\Card;

final class Alfa extends \Banks\Base
{
    private const EMPLOYMENTS_MAP = [
        'Официально трудоустроен' => 1,
        'Работаю не официально'   => 9,
        'Работаю на СПД'          => 5,
        'В декретном отпуске'     => 2,
        'Временно не работаю'     => 3,
        'Пенсионер'               => 4,
        'Студент'                 => 10,
    ];

    private const DATA_FIELDS_MAP = [
        'first_name'   => 'FirstName',
        'last_name'    => 'SecondName',
        'phone'        => 'Phone',
        'inn'          => 'StateCode',
        'placement'    => 'Employment',
        'confirmation' => 'Confirmation',
        'url'          => 'Url',
    ];

    public function sendRequest(int $id, array $params) : array
    {
        $data = [
            'first_name'   => $params['FirstName'],
            'last_name'    => $params['SecondName'],
            'phone'        => $params['Phone'],
            'inn'          => $params['StateCode'],
            'placement'    => self::EMPLOYMENTS_MAP[ $params['Employment'] ],
            'url'          => $this->conf['banks']['alfa']['card']['referalUrl'] . '?subid=' . $id,
            'confirmation' => $params['Confirmation'],
        ];

        $this->log->info('Start alfa card request');
        $out = $this->sendHttpGetRequest($data);
        $this->log->info('End alfa card request');

        $response = json_decode($out, 1);

        return $this->treatResponse($response);
    }

    private function treatResponse(array $response) : array
    {
        if (!isset($response['status'])) {
            $response['status'] = 'default';
        }

        switch ($response['status']) {
            case 'sended sms':
                return [
                    'Status' => 1,
                    'ResponseId' => $response['sms_id'],
                    'Id' => $response['id'],
                ];
            case 'success':
                return [
                    'Status' => 0,
                    'Error' => [
                        'Message' => 'Reject',
                        'Type' => 'REJECT',
                    ]
                ];
            case 'error':
                $wrongFields = [];
                if (is_array($response['error'])) {
                    foreach ($response['error'] as $i => $field) {
                        $wrongFields[] = self::DATA_FIELDS_MAP[$i];
                    }
                }

                return [
                    'Status' => 0,
                    'Error' => [
                        'Message' => 'Double',
                        'Fields' => $wrongFields,
                        'Type' => 'DUPLICATE',
                    ]
                ];
            default:
                return [
                    'Status' => 0,
                    'Error' => [
                        'Message' => 'Unknown response code',
                        'Type' => 'SERVER_ERROR',
                    ]
                ];
        }
    }

    public function sendConfirm(string $requestId, array $params) : array
    {
        $data['sms_id']   = $requestId;
        $data['sms_code'] = $params['Code'];
        $data['id']       = $params['Id'];

        $this->log->info('Start alfa confirm request');
        $out = $this->sendHttpGetRequest($data);
        $this->log->info('End alfa confirm request');

        $response = json_decode($out, 1);

        return $this->treatConfirmResponse($response);
    }

    private function treatConfirmResponse(array $response) : array
    {
        if (!isset($response['status'])) {
            $response['status'] = 'default';
        }

        switch ($response['status']) {
            case 'success':
                $status = (int)$response['id'] ? 1 : 0;
                return [
                    'Status' => $status
                ];
            case 'error':
                return [
                    'Status' => 0,
                    'Error' => [
                        'Type' => 'WRONGCODE'
                    ]
                ];
            default:
                return [
                    'Status' => 0,
                    'Error' => [
                        'Message' => 'Unknown response code',
                        'Type' => 'SERVER_ERROR',
                    ]
                ];
        }
    }
}
