<?php

namespace Service\Schedule;

class Show extends \Service\Base
{
    public function validate(array $params)
    {
        return $params;
    }

    public function execute(array $params)
    {
        // try {
        //     $this->pdo = \Engine\Engine::getConnection('main');
        // } catch (\PDOException $e) {
        //     $this->log()->error($e->getMessage());
        //     return ['Status' => 0];
        // }
        $group = 'КН-3-5';

        $data = [
            'group' => iconv('UTF-8', 'Windows-1251', $group),
            'sdate' => $params['startDate'],
            'edate' => $params['endDate'],
            'faculty' => 0,
            'teacher' => '',
            'n' => 700
        ];
        $url = $this->config()['timetable']['url'];

        $response = $this->sendPostRequest($data, $url);
        $this->log()->info( iconv('Windows-1251', 'UTF-8', $response) );

        return 'good';
    }
}
