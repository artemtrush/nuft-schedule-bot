<?php

namespace Service\Subscription;

class Update extends \Service\Base
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
// @TODOT
        $this->setUserSubscription($params['chatID'], $params['subscription']);
        return '';
    }

    private function setUserSubscription(int $chatID, bool $subscription) {
        $query = "
            UPDATE `users`
            SET `sub` = :sub
            WHERE `chatID` = :chatID
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':sub',    $subscription, \PDO::PARAM_BOOL);
        $stmt->bindParam(':chatID', $chatID,       \PDO::PARAM_INT);
        $stmt->execute();
    }
}
