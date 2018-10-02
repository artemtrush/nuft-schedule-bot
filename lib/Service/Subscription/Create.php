<?php

namespace Service\Subscription;

class Create extends \Service\Base
{
    public function execute(array $params)
    {
        if (!$this->pdo()) {
            return $this->error();
        }

        $group = $this->getUserGroup($params['chatID']);
        if (empty($group['id'])) {
            return '';
        }
//@TODOT
        $this->setUserSubsTime($params['chatID'], $params['subTime']);
        return '';
    }

    private function setUserSubsTime(int $chatID, string $subTime) {
        $query = "
            UPDATE `users`
            SET `subTime` = :subTime
            WHERE `chatID` = :chatID
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':subTime', $subTime, \PDO::PARAM_STR);
        $stmt->bindParam(':chatID',  $chatID,  \PDO::PARAM_INT);
        $stmt->execute();
    }
}
