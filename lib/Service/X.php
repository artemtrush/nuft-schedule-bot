<?php

namespace Service;

class X extends \Exception
{
    protected $fields;
    protected $type;
    protected $myMessage = '';

    public function __construct($attrs = [])
    {
        $this->myMessage = $attrs['Message'] ?? '';
        $this->fields = $attrs['Fields'] ?? null;
        $this->type = $attrs['Type'] ?? null;
    }

    public function getError()
    {
        return [
            'Status' => 0,
            'Error'  => [
                'Fields'  => $this->fields,
                'Type'    => $this->type,
                'Message' => $this->myMessage,
            ]
        ];
    }
}
