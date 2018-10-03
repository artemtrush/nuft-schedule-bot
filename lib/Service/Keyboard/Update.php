<?php

namespace Service\Keyboard;

class Update extends \Service\Base
{
    public function execute(array $params)
    {
        if (!$this->pdo()) {
            return $this->error();
        }

        $user = $this->getUser($params['chatID']);
        if (empty($user['groupID'])) {
            return $this->userGroupNotFoundError();
        }
//@TODOT
        $this->setUserKeyboard($params['chatID'], $params['keyboard']);
        return '' . $user['subTime'];
    }

    private function setUserKeyboard(int $chatID, bool $keyboard) {
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
