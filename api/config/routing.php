<?php

function _u($uri)
{
    return '/api'.$uri;
}

/**
 * 新的路由配置方式
 */
$app->post(_u('/users/login'), 'res.User/Login:post');
$app->get(_u('/users/{userId}'), 'res.User/User:get');
$app->get(_u('/users'), 'res.User/Users:get');
$app->post(_u('/users'), 'res.User/Users:post');  // 用户注册
