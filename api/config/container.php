<?php

$resources = array(
    'User/User',
    'User/Login',
    'User/Users',
    'Express/Expresses',
    'Express/Express'
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
