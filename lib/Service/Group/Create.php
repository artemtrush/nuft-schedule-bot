<?php

namespace Service\Group;

class Create extends \Service\Base
{
    public function execute(array $params)
    {
        try {
            $this->pdo = \Engine\Engine::getConnection('db');
        } catch (\PDOException $e) {
            $this->log()->error($e->getMessage());
            return $this->error();
        }

        //@TODOT
        $groupID = $this->getGroupID($params['groupName']);
        if (!$groupID) {
            return 'Группа не найдена';
        }

        $this->setChatGroup(intval($params['chatID']), $groupID);
        return 'Группа добавлена';
    }

    private function getGroupID(string $groupName) {
        $query = "
            SELECT `id` FROM `groups`
            WHERE `name` = :name
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $groupName, \PDO::PARAM_STR);
        $stmt->execute();

        return (integer)$stmt->fetchColumn();
    }

    private function setChatGroup(integer $chatID, integer $groupID) {
        $query = "
            REPLACE INTO `groupsMap`
            VALUES (:chatID, :groupID)
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':chatID',  $chatID,  \PDO::PARAM_INT);
        $stmt->bindParam(':groupID', $groupID, \PDO::PARAM_INT);
        $stmt->execute();
    }
}
