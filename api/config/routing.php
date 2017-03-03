<?php

function _u($uri)
{
    return '/api'.$uri;
}

$app->post(_u('/users/login'), 'res.User/Login:post');
$app->get(_u('/users/{userId}'), 'res.User/User:get');
$app->get(_u('/users'), 'res.User/Users:get');
$app->post(_u('/users'), 'res.User/Users:post');  // 用户注册

$app->get(_u('/expresses/{expressId}'), 'res.Express/Express:get');
$app->post(_u('/expresses/{expressId}'), 'res.Express/Express:post');
$app->get(_u('/expresses'), 'res.Express/Expresses:get');
$app->post(_u('/expresses'), 'res.Express/Expresses:post');

$app->get(_u('/my/expresses'), 'res.Express/MyExpresses:get');
