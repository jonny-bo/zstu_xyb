<?php

namespace Biz\Util;

use JPush\Client;

class Jpush
{
    const APP_KEY       = 'e9bf5c98051d928343ddc96e';
    const MASTER_SECET  = '28c6404bb742c6fac1cb4a30';

    private static $client;

    private function __construct()
    {
    }

    public static function getClient()
    {
        if (!self::$client) {
            self::$client = new Client(self::APP_KEY, self::MASTER_SECET);
        }
        return self::$client;
    }
}
