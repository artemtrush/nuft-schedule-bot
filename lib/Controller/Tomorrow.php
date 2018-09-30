<?php

namespace Controller;

class Tomorrow extends Base
{
    public function run($message)
    {
        $date = date(self::DATE_FORMAT, strtotime('tomorrow'));

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
