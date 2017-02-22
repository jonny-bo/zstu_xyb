<?php

namespace Biz\Common;

use Codeages\Biz\Framework\Service\Exception\InvalidArgumentException;

class Validate
{
    public static function ipValidate($IP)
    {
        $match = "/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/";
        if (!preg_match($match, $IP)) {
            throw new InvalidArgumentException('IP格式不正确', 1208);
        }
    }
}
