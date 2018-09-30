<?php

namespace Controller;

class Next_week extends Base
{
    public function run($message)
    {
        $startDate = date(self::DATE_FORMAT, strtotime('Monday next week'));
        $endDate   = date(self::DATE_FORMAT, strtotime('Friday next week'));

        $data = [
            'chatID'    => $message->getChat()->getId(),
            'startDate' => $startDate,
            'endDate'   => $endDate
        ];

        return $this->action('Service\Schedule\Show')->run($data);
    }
}
