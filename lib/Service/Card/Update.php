<?php

namespace Service\Card;

class Update extends \Service\Base
{
    public function validate(array $params)
    {
        $rules = [
            'MessageId' => ['required'],
            'Id'        => ['required', 'positive_integer'],
            'Code'      => ['required'],
            'CPA'       => ['required', ['variable_object' => [
                'name', [
                    'salesdoubler' => [
                        'name' => ['eq' => 'salesdoubler'],
                        'clickId' => ['required', 'string'],
                    ],
                    'none' => [
                        'name' => ['eq' => 'none'],
                    ],
                ]
            ]]]
        ];

        return $result = \Service\Validator::validate($params, $rules);
    }

    public function execute(array $params)
    {
        try {
            $this->pdo = \Engine\Engine::getConnection('main');
        } catch (\PDOException $e) {
            $this->log()->error($e->getMessage());
            return ['Status' => 0];
        }

        if (!$this->saveParams($params)) {
            $this->log()->error("Unable to save params in DB, params:" . json_encode($params));
        }

        $dbParams = $this->getParams($params);
        $id = $dbParams['id'] ?? 0;
        if (!$id) {
            $this->log()->error("Unable to find request in DB, params:" . json_encode($params));
        }

        try {
            $response = $this->sendSmsConfirm($params, $id, $dbParams);
        } catch (\Throwable $e) {
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

        $this->updReqRes(
            json_encode($params),
            $response,
            $params['MessageId']
        );

        return $response + [
            'Status'    => 1,
            'BidId'     => $id,
        ];
    }

    private function sendSmsConfirm($params, $id, $dbParams)
    {
        $alfa = new \Banks\Card\Alfa(
            $this->config()['banks']['alfa']['card']['confirmUrl'],
            $this->config(),
            $this->log()
        );

        $resAlfa = $alfa->sendConfirm($params['MessageId'], $params);
        if ($resAlfa['Error'] && $resAlfa['Error']['Type'] === 'WRONGCODE') {
            return $resAlfa;
        }

        $response = [
            'Status' => 1,
            'Banks' => [
                'Alfa'          => 'ERROR',
                'Monobank'      => 'SKIP',
                'CreditDnepr'   => 'SKIP',
                'PrivatBank'    => 'SKIP',
                'ShvidkoGroshi' => 'SKIP',
                'CashPoint'     => 'SKIP',
                'Ecredit'       => 'SKIP',
                'PUMB'          => 'SKIP',
                'Finline'       => 'SKIP'
            ]
        ];

        if ((int)$resAlfa['Status'] === 1) {
            $this->notifyCPA($params, $id, 'card');
            $response['Banks']['Alfa'] = 'SUCCESS';
        }

        $BID = 'BID [' . $id . '] ';
        $this->log()->info($BID . 'BANKS STATUSES: ' . json_encode($response['Banks']));
        $this->log()->info($BID . 'IS DUPLICATE: FALSE');

        return $response;
    }

    private function saveParams(array $params)
    {
        $query = "UPDATE `requests_card` SET `confirm_code` = :code WHERE `request_id` = :request_id";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':code',       $params['Code'],      \PDO::PARAM_STR);
        $stmt->bindValue(':request_id', $params['MessageId'], \PDO::PARAM_STR);

        return $stmt->execute();
    }

    private function updReqRes(string $req, array $res, string $reqId)
    {
        $query = "
            UPDATE `requests_card` SET `request` = :request, `response` = :response, `approved` = :approved
            WHERE `request_id`=:request_id
        ";
        $stmt = $this->pdo->prepare($query);

        $response = json_encode($res);
        $approved = $res['Status'] ?? 0;

        $stmt->bindParam(':request',    $req,      \PDO::PARAM_STR);
        $stmt->bindParam(':response',   $response, \PDO::PARAM_STR);
        $stmt->bindParam(':request_id', $reqId,    \PDO::PARAM_STR);
        $stmt->bindParam(':approved',   $approved, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    private function getParams(array $params)
    {
        $query = "SELECT * FROM `requests_card` WHERE `request_id` = :request_id LIMIT 1";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':request_id', $params['MessageId'], \PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
