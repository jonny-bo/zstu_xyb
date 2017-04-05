<?php

function _u($uri)
{
    return '/api'.$uri;
}

$app->post(_u('/users/login'), 'res.User/User:login');
$app->get(_u('/users/{userId}'), 'res.User/User:get');
$app->get(_u('/users'), 'res.User/User:search');
$app->post(_u('/users/register'), 'res.User/User:register');  // 用户注册
$app->post(_u('/users/avatar'), 'res.User/User:setAvatar');

$app->get(_u('/my/expresses'), 'res.Express/MyExpress:get');
$app->post(_u('/my/publish_expresses/{expressId}'), 'res.Express/MyExpress:publishedConfirm');
$app->post(_u('/my/recive_expresses/{expressId}'), 'res.Express/MyExpress:recivedConfirm');

$app->get(_u('/expresses/{expressId}'), 'res.Express/Express:get');
$app->post(_u('/expresses/{expressId}'), 'res.Express/Express:update');
$app->post(_u('/expresses/{expressId}/order'), 'res.Express/Express:order');
$app->post(_u('/expresses/{expressId}/cancel'), 'res.Express/Express:cancel');
$app->get(_u('/expresses'), 'res.Express/Express:search');
$app->post(_u('/expresses'), 'res.Express/Express:create');

$app->get(_u('/goods/categorys'), 'res.Goods/Category:all');
$app->get(_u('/goods'), 'res.Goods/Goods:search');
$app->get(_u('/goods/{goodsId}'), 'res.Goods/Goods:get');
$app->post(_u('/goods'), 'res.Goods/Goods:post');
$app->post(_u('/goods/{goodsId}'), 'res.Goods/Goods:delete');
$app->post(_u('/goods/{goodsId}/publish'), 'res.Goods/Goods:publish');
$app->post(_u('/goods/{goodsId}/cancel'), 'res.Goods/Goods:cancel');
