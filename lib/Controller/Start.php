<?php

namespace Controller;

class Start extends Base
{
    public function run($message)
    {
        $text =
            'Привет! Я могу помочь найти расписание занятий ' .
            'Национального Университета Пищевых Технологий.' . PHP_EOL .
            'Для начала нужно отправить мне название группы /group' . PHP_EOL . PHP_EOL .
            'А вот полный список моих команд:' . PHP_EOL
        ;

        $help = $this->action('Controller\Help')->run($message);

        $creator =  PHP_EOL .
            'Важно! Я все еще нахожусь в режиме тестирования, ' .
            'поэтому советую сначала сравнить найденное мной расписание с официальным.' . PHP_EOL .
            'По всем вопросам - к создателю: @artemtrush'
        ;

        return $text . $help . $creator;
    }
}
