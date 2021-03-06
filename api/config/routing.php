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
$app->post(_u('/users/pay_pass'), 'res.User/User:setPayPass');
$app->post(_u('/users/set_tag'), 'res.User/User:setTag');
$app->post(_u('/users/approval'), 'res.User/User:approval');

$app->get(_u('/my/credit'), 'res.User/User:credit');
$app->get(_u('/my/expresses'), 'res.Express/MyExpress:get');
$app->get(_u('/my/publish_expresses/{expressId}'), 'res.Express/MyExpress:myPublish');
$app->post(_u('/my/publish_expresses/{expressId}/auth'), 'res.Express/MyExpress:auth');
$app->post(_u('/my/publish_expresses/{expressId}'), 'res.Express/MyExpress:publishedConfirm');
$app->post(_u('/my/recive_expresses/{expressId}'), 'res.Express/MyExpress:recivedConfirm');

$app->get(_u('/expresses/{expressId}'), 'res.Express/Express:get');
$app->post(_u('/expresses/{expressId}'), 'res.Express/Express:update');
$app->post(_u('/expresses/{expressId}/apply'), 'res.Express/Express:apply');
$app->post(_u('/expresses/{expressId}/order'), 'res.Express/Express:order');
$app->post(_u('/expresses/{expressId}/cancel'), 'res.Express/Express:cancel');
$app->get(_u('/expresses'), 'res.Express/Express:search');
$app->post(_u('/expresses'), 'res.Express/Express:create');
$app->post(_u('/expresses/{expressId}/review'), 'res.Express/Express:review');

$app->get(_u('/goods/categorys'), 'res.Goods/Category:all');
$app->get(_u('/goods'), 'res.Goods/Goods:search');
$app->get(_u('/goods/{goodsId}'), 'res.Goods/Goods:get');
$app->post(_u('/goods'), 'res.Goods/Goods:post');
$app->post(_u('/goods/{goodsId}/delete'), 'res.Goods/Goods:delete');
$app->post(_u('/goods/{goodsId}/publish'), 'res.Goods/Goods:publish');
$app->post(_u('/goods/{goodsId}/cancel'), 'res.Goods/Goods:cancel');
// $app->post(_u('/goods/{goodsId}/update'), 'res.Goods/Goods:update');
$app->post(_u('/goods/{goodsId}/posts'), 'res.Goods/Posts:post');
$app->get(_u('/goods/{goodsId}/posts'), 'res.Goods/Posts:get');
$app->post(_u('/goods/{goodsId}/like'), 'res.Goods/Goods:like');
$app->post(_u('/goods/{goodsId}/cancelLike'), 'res.Goods/Goods:cancelLike');

$app->post(_u('/file/upload'), 'res.File/File:upload');

$app->get(_u('/group'), 'res.Group/Group:search');
$app->post(_u('/group'), 'res.Group/Group:post');
$app->get(_u('/group/{groupId}'), 'res.Group/Group:get');
$app->get(_u('/group/{groupId}/member'), 'res.Group/Member:search');
$app->post(_u('/group/{groupId}/join'), 'res.Group/Member:join');
$app->post(_u('/group/{groupId}/quit'), 'res.Group/Member:quit');
$app->post(_u('/group/{groupId}'), 'res.Group/Group:update');
// $app->post(_u('/group/{groupId}/delete'), 'res.Group/Group:delete');
// $app->post(_u('/group/{groupId}/open'), 'res.Group/Group:open');
// $app->post(_u('/group/{groupId}/close'), 'res.Group/Group:close');
$app->get(_u('/threads'), 'res.Thread/Thread:search');
$app->get(_u('/threads/{threadId}'), 'res.Thread/Thread:get');
$app->post(_u('/threads'), 'res.Thread/Thread:post');
$app->post(_u('/threads/{threadId}/post'), 'res.Thread/Posts:post');
$app->get(_u('/threads/{threadId}/post'), 'res.Thread/Posts:get');

$app->post(_u('/collections'), 'res.Collection/Collection:post');
$app->post(_u('/collections/cancel'), 'res.Collection/Collection:cancel');
$app->get(_u('/collections'), 'res.Collection/Collection:get');
