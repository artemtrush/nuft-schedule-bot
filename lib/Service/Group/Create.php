<?php

namespace Service\Group;

class Create extends \Service\Base
{
    public function validate(array $params)
    {
        /*
        $rules = [
            'FirstName'    => ['required', ['max_length' => 38]],
            'SecondName'   => ['required', ['max_length' => 38]],
            'Employment'   => ['required', ['one_of' => [0, 1, 2, 3, 4, 5, 6]]],
            'StateCode'    => ['required', ['length_equal' => 10]],
            'Phone'        => ['required', ['min_length' => 10], 'phone_number'],
            'Source'       => ['required', ['one_of' => ['salesdoubler', 'none']]],
            'CPA'          => ['required', ['variable_object' => [
                'name', [
                    'salesdoubler' => [
                        'name' => ['eq' => 'salesdoubler'],
                        'clickId' => ['required', 'string'],
                    ],
                    'none' => [
                        'name' => ['eq' => 'none'],
                    ],
                ]
            ]]],
            'Confirmation' => ['required', ['one_of' => [1, 0]]],
        ];

        if (isset($params['SecondName']) && !preg_match('/^([а-яА-ЯЁёіІєЄїЇґҐ-])+$/u', $params['SecondName'])) {
            throw new \Service\X([
                'Fields' => ['SecondName'],
                'Type' => 'FORMAT_ERROR',
                'Message' => 'Wrong SecondName'
            ]);
        }

        if (isset($params['FirstName']) && !preg_match('/^([а-яА-ЯЁёіІєЄїЇґҐ-])+$/u', $params['FirstName'])) {
            throw new \Service\X([
                'Fields' => ['FirstName'],
                'Type' => 'FORMAT_ERROR',
                'Message' => 'Wrong FirstName'
            ]);
        }

        if (isset($params['Phone']) && !preg_match('/^[0](39|50|6[3,6-8]|9[1-9])\d{7}$/u', $params['Phone'])) {
            throw new \Service\X([
                'Fields' => ['Phone'],
                'Type' => 'FORMAT_ERROR',
                'Message' => 'Wrong Phone'
            ]);
        }

        return \Service\Validator::validate($params, $rules);
        */
    }

    public function execute(array $params)
    {
        /*
        try {
            $this->pdo = \Engine\Engine::getConnection('main');
        } catch (\PDOException $e) {
            $this->log()->error($e->getMessage());
            return ['Status' => 0];
        }

        $params['Employment'] = self::EMPLOYMENTS_MAP[ $params['Employment'] ];
        $isDuplicate = $this->isDuplicate($params);
        $id = $this->saveParams($params);

        try {
            $response = $this->sendRequestToBanks($id, $params, $isDuplicate);
        } catch (\Throwable $e) {
            $this->log()->error($e->getMessage());

            return [
                'Status' => 0,
                'Banks' => [
                    'Alfa'          => 'ERROR',
                    'Monobank'      => 'ERROR',
                    'CreditDnepr'   => 'ERROR',
                    'PrivatBank'    => 'ERROR',
                    'ShvidkoGroshi' => 'ERROR',
                    'CashPoint'     => 'ERROR',
                    'Ecredit'       => 'ERROR',
                    'PUMB'          => 'ERROR',
                    'Finline'       => 'ERROR'
                ]
            ];
        }

        if (!empty($response['Error']['Fields'])) {
            $this->dropRequest($id);
        } else {
            $this->updReqRes(
                json_encode($params),
                ($response ?? []),
                $id
            );
        }

        return $response + [
            'Status'    => 1,
            'BidId'     => $id,
        ];
        */
    }

    /*
    private function sendRequestToBanks($id, $params, $isDuplicate)
    {
        $banks = [];

        $resAlpha = $this->sendToAlphaBank($id, $params);
        if ($resAlpha['Status'] === 1 || !empty($resAlpha['Error']['Fields'])) {
            return $resAlpha;
        }

        $resAdmitad = $this->sendToAdmitad($id, $params);
        $resFinline = $this->sendToFinline($id, $params);

        $banks['Alfa'] = $resAlpha['Error']['Type'];
        $banks['Finline'] = (int)$resFinline['Status'] ? 'SUCCESS' : 'ERROR';
        foreach ($resAdmitad as $bankName => $bankRes) {
            $banks[ $bankName ] = (int)$bankRes['Status'] ? 'SUCCESS' : 'ERROR';
        }

        $BID = 'BID [' . $id . '] ';
        $this->log()->info($BID . 'BANKS STATUSES: ' . json_encode($banks));

        if (!$isDuplicate) {
            $this->log()->info($BID . 'IS DUPLICATE: FALSE');
            $this->notifyCPA($params, $id, 'card');
        } else {
            $this->log()->info($BID . 'IS DUPLICATE: TRUE');
        }

        return [
            'Status'      => 1,
            'Banks'       => $banks,
            'IsDuplicate' => (int)$isDuplicate,
        ];
    }

    private function sendToAlphaBank($id, $params)
    {
        $alfa = new \Banks\Card\Alfa(
            $this->config()['banks']['alfa']['card']['requestUrl'],
            $this->config(),
            $this->log()
        );

        return $alfa->sendRequest($id, $params);
    }

    private function sendToAdmitad($id, $params)
    {
        $admitad = new \Banks\Card\Admitad(
            $this->config()['banks']['admitad']['api']['requestUrl'],
            $this->config(),
            $this->log()
        );

        return $admitad->sendRequest($id, $params);
    }

    private function sendToFinline($id, $params)
    {
        $finline = new \Banks\Card\Finline(
            $this->config()['banks']['finline']['api']['requestUrl'],
            $this->config(),
            $this->log()
        );

        return $finline->sendRequest($id, $params);
    }

    private function saveParams(array $params)
    {
        $query = "
            INSERT INTO `requests_card` (`first_name`, `second_name`, `phone`, `state_code`, `employment`, `cpa`)
            VALUES (:first_name, :second_name, :phone, :state_code, :employment, :cpa)
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':first_name',  $params['FirstName'],  \PDO::PARAM_STR);
        $stmt->bindValue(':second_name', $params['SecondName'], \PDO::PARAM_STR);
        $stmt->bindValue(':phone',       $params['Phone'],      \PDO::PARAM_STR);
        $stmt->bindValue(':state_code',  $params['StateCode'],  \PDO::PARAM_STR);
        $stmt->bindValue(':employment',  $params['Employment'], \PDO::PARAM_STR);
        $stmt->bindValue(':cpa',         $params['Source'],     \PDO::PARAM_STR);

        $stmt->execute();

        return $this->pdo->lastInsertId();
    }

    private function updReqRes(string $req, array $res, int $id)
    {
        $query = "
            UPDATE `requests_card`
            SET `request` = :request, `response` = :response, `request_id` = :request_id, `approved` = :approved
            WHERE `id`=:id
        ";
        $stmt = $this->pdo->prepare($query);

        $response   = json_encode($res);
        $request_id = $res['ResponseId'] ?? 0;
        $approved   = $res['Status'] ?? 0;

        $stmt->bindParam(':request',    $req,        \PDO::PARAM_STR);
        $stmt->bindParam(':response',   $response,   \PDO::PARAM_STR);
        $stmt->bindParam(':request_id', $request_id, \PDO::PARAM_STR);
        $stmt->bindParam(':approved',   $approved,   \PDO::PARAM_INT);
        $stmt->bindParam(':id',         $id,         \PDO::PARAM_INT);

        return $stmt->execute();
    }

    private function isDuplicate(array $params) : bool
    {
        $query = "
            SELECT count(*) FROM `requests_card`
            WHERE `state_code` = :state_code AND `phone` = :phone
            AND `add_time` > NOW() - INTERVAL 1 MONTH AND approved > 0
        ";

        $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':state_code', $params['StateCode'], \PDO::PARAM_STR);
        $stmt->bindParam(':phone',      $params['Phone'],     \PDO::PARAM_STR);
        $stmt->execute();

        return (bool)$stmt->fetchColumn();
    }

    private function dropRequest(int $id) : void
    {
        $query = "DELETE FROM `requests_card` WHERE `id` = :id";

        $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

        $stmt->execute();
    }
    */
}
