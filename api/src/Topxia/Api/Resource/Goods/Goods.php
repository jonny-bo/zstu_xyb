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
use Biz\Common\ArrayToolkit;

class Goods extends BaseResource
{
    public function search(Request $request)
    {
        $conditions = $request->query->all();

        $start   = $request->query->get('start', 0);
        $limit   = $request->query->get('limit', 10);
        $ordeyBy = array('updated_time' => 'DESC');

        $goods = $this->getGoodsService()->searchGoods($conditions, $ordeyBy, $start, $limit);
        $total = $this->getGoodsService()->searchGoodsCount($conditions);

        return $this->wrap($this->multiFilter($goods), $total);
    }

    public function filter($res)
    {
        $res['thumb'] = $this->getFileUrl($res['thumb']);
        $res['publisher'] = $this->callSimplify('User/User', $this->getUserService()->getUser($res['publisher_id']));
        $res['category'] = $this->callSimplify('Goods/Category', $this->getCategoryService()->getCategory($res['category_id']));

        $res['updated_time'] = date('c', $res['updated_time']);
        $res['created_time'] = date('c', $res['created_time']);

        unset($res['publisher_id']);
        unset($res['category_id']);

        return $res;
    }

    public function simplify($res)
    {
        return $res;
    }

    protected function getGoodsService()
    {
        return $this->biz->service('Goods:GoodsService');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getCategoryService()
    {
        return $this->biz->service('Category:CategoryService');
    }
}
