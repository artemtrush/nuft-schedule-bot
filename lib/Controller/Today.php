<?php

namespace Controller;

class Today extends Base
{
    public function run($message)
    {
        $date = date(self::DATE_FORMAT);

        $data = [
            'chatID'    => $message->getChat()->getId(),
            'startDate' => $date,
            'endDate'   => $date
        ];

        return $this->action('Service\Schedule\Show')->run($data);
    }
}
