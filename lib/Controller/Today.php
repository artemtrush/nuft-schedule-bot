<?php

namespace Controller;

class Today extends Base
{
    public function run($message)
    {
        $date = date(self::DATE_FORMAT, strtotime('today'));

        $data = [
            'chatID'    => $message->getChat()->getId(),
            'startDate' => $date,
            'endDate'   => $date
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
