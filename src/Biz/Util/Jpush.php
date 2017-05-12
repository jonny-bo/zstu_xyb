<?php

namespace Biz\Util;

use JPush\Client;

class Jpush
{
    protected $appKey = 'e9bf5c98051d928343ddc96e';

    protected $masterSecret = '28c6404bb742c6fac1cb4a30';

    private static $client;

    private function __construct()
    {
    }

    public static function getClient()
    {
        if (!self::$client) {
            self::$client = new Client($this->appKey, $this->masterSecret);
        }
        return self::$client;
    }
}
