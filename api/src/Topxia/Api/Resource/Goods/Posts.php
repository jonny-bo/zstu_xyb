<?php

namespace Topxia\Api\Resource\Goods;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;

class Posts extends BaseResource
{
    public function post(Request $request, $goodsId)
    {
        $this->checkGoods($goodsId);
        $fields = $request->request->all();
        $post = $this->getGoodsService()->createGoodsPost($goodsId, $fields);

        return $this->filter($post);
    }

    public function get(Request $request, $goodsId)
    {
        $conditions = $request->query->all();

        $start = $request->query->get('start', 0);
        $limit = $request->query->get('limit', 10);

        $this->checkGoods($goodsId);

        $conditions['old_goods_id'] = $goodsId;

        $posts = $this->getGoodsService()->searchGoodsPosts(
            $conditions,
            array('created_time' => 'DESC'),
            $start,
            $limit
        );

        $total = $this->getGoodsService()->searchGoodsPostsCount($conditions);

        return $this->wrap($this->multiFilter($posts), $total);
    }

    protected function checkGoods($goodsId)
    {
        $goods = $this->getGoodsService()->getGoods($goodsId);

        if (empty($goods)) {
            throw new ResourceNotFoundException('goods', '请求内容不存在');
        }

        return $goods;
    }

    public function filter($res)
    {
        $res['from_user'] = $this->callSimplify('User/User', $this->getUserService()->getUser($res['from_user_id']));
        // $res['to_user'] = !empty($res['to_user_id']) ? $this->callSimplify('User/User', $this->getUserService->getUser($res['to_user_id'])) : '';
        $res['created_time'] = date('c', $res['created_time']);

        unset($res['old_goods_id']);
        unset($res['to_user_id']);
        unset($res['from_user_id']);

        return $res;
    }

    public function simplify($res)
    {
        return $this->filter($res);
    }

    protected function getGoodsService()
    {
        return $this->biz->service('Goods:GoodsService');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
