<?php


namespace App\Controllers\Helpers;


use MyCLabs\Enum\Enum;

class StatusEnum extends Enum
{
    const OK = 'OK';
    const ERROR = 'ERROR';
    const UNKNOWN = 'UNKNOWN';
    const NOT_FOUND = 'NOT_FOUND';
}