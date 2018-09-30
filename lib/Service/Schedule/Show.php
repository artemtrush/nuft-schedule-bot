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
        $scheduleData = $this->treatResponse($response);

        return $this->generateSchedule($scheduleData);
    }

    public function treatResponse(string $response) {
        $document = iconv('Windows-1251', 'UTF-8', $response);
        $text = preg_replace('/\s+/', ' ', $document);
        $data = [];

        $rowPattern = '/<div class=\"col-md-6\">(.+?)<\/div>/';
        preg_match($rowPattern, $text, $rows);
        foreach ($rows as $row) {
            $datePattern = '/<h4>(.+?) <small>(.+?)<\/small><\/h4>/';
            preg_match($datePattern, $row, $dateInfo);

            $lessonsInfo = [];
            $lessonPattern = '/<div class=\"row\"><tr>(.+?)<\/tr><\/div>/';
            preg_match($lessonPattern, $row, $lessons);
            foreach ($lessons as $lesson) {
                $infoPattern = '/<td>(.+?)<\/td><td>(.+?)<br>(.+?)<\/td><td>(.+?)<\/td>/';
                if (preg_match($infoPattern, $lesson, $info)) {
                    $lessonsInfo[] = [
                        'number'      => $info[1],
                        'stime'       => $info[2],
                        'etime'       => $info[3],
                        'description' => $info[4]
                    ];
                }
            }

            if (!empty($dateInfo) && !empty($lessonsInfo)) {
                $data[] = [
                    'date'    => $dateInfo[0],
                    'day'     => $dateInfo[1],
                    'lessons' => $lessonsInfo
                ];
            }
        }

        return $data;
    }

    public function generateSchedule(array $data) {
        $schedule = '';
        $numbers = ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];

        foreach ($data as $dayData) {
            $schedule .= $dayData['day'] . ' ' . $dayData['time'] . PHP_EOL;

            foreach ($dayData['lessons'] as $lesson) {
                $schedule .= ':' . $numbers[ $lesson['number'] ] . ': ' .
                             $lesson['stime'] . '-' . $lesson['etime'] . ': ' .
                             $lesson['description'] . PHP_EOL;
            }
            $schedule .= PHP_EOL;
        }

        if (!$schedule) {
            return 'Не найдено';
        }

        return $schedule;
    }
}
