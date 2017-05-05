<?php

namespace Biz\Util;

use JPush\Client;

class Jpush
{
    protected $appKey = '20990a7b45465c239ba31270';

    protected $masterSecret = '27520d83f3e71642f4305bfd';

    protected static $client = '';

    public function __construct($appKey = '', $masterSecret = '')
    {
        if ($appKey && $masterSecret) {
            $this->appKey = $appKey;
            $this->masterSecret = $masterSecret;
        }

        $this->client = new Client($this->appKey, $this->masterSecret);
    }

    public static function getClient($appKey = '', $masterSecret = '')
    {
        if (self::$client == '') {
            new self($appKey, $masterSecret);
        }

        return self::$client;
    }
}
