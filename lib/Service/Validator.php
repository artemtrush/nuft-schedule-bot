<?php

namespace Service;

class Validator
{
    public static function validate($data, $livr)
    {
        \Validator\LIVR::registerDefaultRules([
            'json' => function () {
                return function ($got) {
                    json_decode($got);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        return 'NOT_JSON';
                    }
                };
            },
            'array' => function () {
                return function ($got) {
                    if (!is_array($got)) {
                        return 'NOT_ARRAY';
                    }
                };
            },
            'phone_number' => function () {
                return function ($got) {
                    if (!is_string($got)) {
                        return 'WRONG_FORMAT';
                    }

                    if (!$got) {
                        return;
                    }

                    switch (strlen($got)) {
                        case 13:
                        case 12:
                            $rule = '/^(\+380|380)[2-9]\d{8}$/';
                            break;
                        case 10:
                            $rule = '/^0[2-9]\d{8}$/';
                            break;
                        default:
                            return 'WRONG_FORMAT';
                    }

                    if (!preg_match($rule, $got)) {
                        return 'WRONG_FORMAT';
                    }
                };
            }
        ]);

        $validator = new \Validator\LIVR($livr);

        $validated = $validator->validate($data);
        $errors    = $validator->getErrors();

        if ($errors) {
            throw new X(['Type' => 'FORMAT_ERROR', 'Fields' => $errors]);
        }

        return $validated;
    }
}
