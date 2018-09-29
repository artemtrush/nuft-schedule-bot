<?php

namespace Controller;

class Card extends Base
{
    public function create($req, $res, $args)
    {
        $data = $req->getParsedBody() ?? [];
        $data['Confirmation'] = 1;

        return $this->run(function () use ($data) {
            return $this->action('Service\Card\Create')->run($data);
        }, $res);
    }

    public function update($req, $res, $args)
    {
        $data = $req->getParsedBody() ?? [];
        $data['MessageId'] = $args['Id'];

        return $this->run(function () use ($data) {
            return $this->action('Service\Card\Update')->run($data);
        }, $res);
    }
}
