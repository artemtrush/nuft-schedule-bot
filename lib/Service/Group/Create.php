<?php

namespace Service\Group;

class Create extends \Service\Base
{
    public function execute(array $params)
    {
        if (!$this->pdo()) {
            return $this->error();
        }

        $groupID = $this->getGroupID($params['groupName']);
        if (!$groupID) {
            return 'К сожалению, я не нашел группы с таким названием. Проверьте правильность написания.';
        }

        $this->setUserData($params['chatID'], $groupID);
        return 'Группа ' . $params['groupName'] . ' установлена по умолчанию. Теперь я могу искать расписание!';
    }

    private function getGroupID(string $groupName) {
        $query = "
            SELECT `id` FROM `groups`
            WHERE `name` = :name
        ";

        $stmt = $this->pdo()->prepare($query);
        $stmt->bindParam(':name', $groupName, \PDO::PARAM_STR);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    private function setUserData(int $chatID, int $groupID, string $username) {
        $query = $this->isUserExists($chatID) ? "
            UPDATE `users`
            SET `groupID` = :groupID, `username` = :username
            WHERE `chatID` = :chatID
        " : "
            INSERT INTO `users` (`chatID`, `groupID`, `username`)
            VALUES (:chatID, :groupID, :username)
        ";

        $stmt = $this->pdo()->prepare($query);
        $stmt->bindParam(':groupID',  $groupID,  \PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
        $stmt->bindParam(':chatID',   $chatID,   \PDO::PARAM_INT);
        $stmt->execute();
    }

    private function isUserExists(int $chatID) {
        $query = "
            SELECT `id` from `users`
            WHERE `chatID` = :chatID
        ";

        $stmt = $this->pdo()->prepare($query);
        $stmt->bindParam(':chatID', $chatID, \PDO::PARAM_INT);
        $stmt->execute();

        return (bool)$stmt->fetchColumn();
    }
}
