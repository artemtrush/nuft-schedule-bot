<?php

namespace Controller;

class Help extends Base
{
    public function run($message)
    {
        $help =
            '/start - начать работу с ботом' . PHP_EOL .
            '/help  - открыть справку' . PHP_EOL .
            '/group - установить группу, для которой будет совершаться поиск расписания' . PHP_EOL .
            '     Формат: /group  [специализация]-[курс]-[группа]' . PHP_EOL .
            '     Пример: /group  КН-4-5' . PHP_EOL .
            '/today - получить расписание на сегодня' . PHP_EOL .
            '/tomorrow - получить расписание на завтра' . PHP_EOL .
            '/current_week - получить расписание на текущую неделю' . PHP_EOL .
            '/next_week - получить расписание на следующую неделю' . PHP_EOL .
            '/date - получить расписание на конкретную дату.' . PHP_EOL .
            '     Формат: /date  день.месяц.год  день.месяц.год' . PHP_EOL .
            '     Пример: /date  01.09.2018  05.09.2018' . PHP_EOL
        ;

        return $help;
    }
}
