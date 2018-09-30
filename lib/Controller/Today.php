<?php

namespace Controller;

class Today extends Base
{
    public function run($message)
    {


        return $this->action('Service\Card\Create')->run($data);
    }
}
