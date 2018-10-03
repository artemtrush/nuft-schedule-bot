<?php

namespace Service\Schedule;

class Show extends \Service\Base
{
    public function execute(array $params)
    {
        if (!$this->pdo()) {
            return $this->error();
        }

        $user = $this->getUser($params['chatID']);
        if (empty($user['groupName']) || empty($user['groupID'])) {
            return $this->userGroupNotFoundError();
        }

        $cacheKey = 'ServiceScheduleShow__' . $user['groupID'] . '_' . $params['startDate'] . '_' . $params['endDate'];
        $cache = $this->getCache($cacheKey);
        if ($cache) {
            return $cache;
        }

        $data = [
            'group' => iconv('UTF-8', 'Windows-1251', $user['groupName']),
            'sdate' => $params['startDate'],
            'edate' => $params['endDate'],
            'faculty' => 0,
            'teacher' => '',
            'n' => 700
        ];
        $url = $this->config()['timetable']['url'];

        $response = $this->sendPostRequest($data, $url);
        $scheduleData = $this->treatResponse($response);
        $schedule = $this->generateSchedule($scheduleData);

        if (!$schedule) {
            return 'Я не нашел расписания на указанную дату. Можно спокойно отдыхать ' .
                $this->createEmoji('\xF0\x9F\x98\x8C');
        }

        $this->setCache($cacheKey, $schedule);
        return $schedule;
    }

    private function treatResponse(string $response) {
        $document = iconv('Windows-1251', 'UTF-8', $response);
        $text = preg_replace('/\s+/', ' ', $document);
        $data = [];

        $rowPattern = '/<div class="col-md-6">(.+?)<\/table><\/div>/';
        preg_match_all($rowPattern, $text, $rows);
        foreach ($rows[0] as $row) {
            $datePattern = '/<h4>(.+?) <small>(.+?)<\/small><\/h4>/';
            preg_match($datePattern, $row, $dateInfo);

            $lessonsInfo = [];
            $lessonPattern = '/<tr>(.+?)<\/tr>/';
            preg_match_all($lessonPattern, $row, $lessons);
            foreach ($lessons[0] as $lesson) {
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
                    'date'    => $dateInfo[1],
                    'day'     => $dateInfo[2],
                    'lessons' => $lessonsInfo
                ];
            }
        }

        return $data;
    }

    private function generateSchedule(array $data) {
        $schedule = '';

        foreach ($data as $dayData) {
            $schedule .= '<b>' . $dayData['day'] . ' ' . $dayData['date'] . '</b>' . PHP_EOL;

            foreach ($dayData['lessons'] as $lesson) {
                $schedule .= $this->createEmoji('\x3' . $lesson['number'] . '\xE2\x83\xA3') . ' ' .
                             $lesson['stime'] . '-' . $lesson['etime'] . ': ' .
                             $lesson['description'] . PHP_EOL;
            }
            $schedule .= PHP_EOL;
        }

        $schedule = str_replace('<br>', ', ', $schedule);
        $schedule = strip_tags($schedule, '<b>');

        return $schedule;
    }
}
