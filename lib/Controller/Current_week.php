<?php

namespace Controller;

class Current_week extends Base
{
    public function run($message)
    {
        $startDate = date(self::DATE_FORMAT, strtotime('Monday this week'));
        $endDate   = date(self::DATE_FORMAT, strtotime('Friday this week'));

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
