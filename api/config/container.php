<?php

$resources = array(
    'User/User',
    'Express/Express',
    'Express/MyExpress',
    'Goods/Category',
    'Goods/Goods',
);

foreach ($resources as $res) {
    $app["res.{$res}"] = function () use ($res, $biz) {
        $class    = "Topxia\\Api\\Resource";
        $segments = explode('/', $res);
        foreach ($segments as $seg) {
            $class .= "\\{$seg}";
        }
        return new $class($biz);
    };
}
