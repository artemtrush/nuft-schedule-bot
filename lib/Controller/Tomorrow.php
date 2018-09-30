<?php

namespace Controller;

class Tomorrow extends Base
{
    public function run($message)
    {
        $date = date(self::DATE_FORMAT, strtotime('+1 day'));

        $data = [
            'chatID'    => $message->getChat()->getId(),
            'startDate' => $date,
            'endDate'   => $date
        ];

        return $this->action('Service\Schedule\Show')->run($data);
    }
}
