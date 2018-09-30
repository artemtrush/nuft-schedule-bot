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

        try {
            $result = $this->action('Service\Schedule\Show')->run($data);
        } catch (\Throwable $e) {
            $this->log()->error($e->getMessage());
            return $this->error();
        }

        return $result;
    }
}
