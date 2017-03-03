<?php

function _u($uri)
{
    return '/api'.$uri;
}

$app->post(_u('/users/login'), 'res.User/User:login');
$app->get(_u('/users/{userId}'), 'res.User/User:get');
$app->get(_u('/users'), 'res.User/User:search');
$app->post(_u('/users/register'), 'res.User/User:register');  // 用户注册

$app->get(_u('/my/expresses'), 'res.Express/Express:myExpresses');

$app->get(_u('/expresses/{expressId}'), 'res.Express/Express:get');
$app->post(_u('/expresses/{expressId}'), 'res.Express/Express:update');
$app->post(_u('/expresses/{expressId}/order'), 'res.Express/Express:order');
$app->post(_u('/expresses/{expressId}/cancel'), 'res.Express/Express:cancel');
$app->get(_u('/expresses'), 'res.Express/Express:search');
$app->post(_u('/expresses'), 'res.Express/Express:create');
