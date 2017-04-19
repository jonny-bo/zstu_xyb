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
use Biz\Common\FileToolkit;

class Goods extends BaseResource
{
    protected $requireFiles = array(
        'title', 'imgs', 'category_id', 'body', 'price'
    );
    public function search(Request $request)
    {
        $conditions = $request->query->all();

        $start   = $request->query->get('start', 0);
        $limit   = $request->query->get('limit', 10);
        $conditions['status'] = isset($conditions['status']) ? $conditions['status'] : 1;

        $ordeyBy = $this->getOrderBy($conditions, array('updated_time' => 'DESC'));
        $goods = $this->getGoodsService()->searchGoods($conditions, $ordeyBy, $start, $limit);
        $total = $this->getGoodsService()->searchGoodsCount($conditions);

        return $this->wrap($this->multiSimplify($goods), $total);
    }

    public function get($goodsId)
    {
        $this->checkGoods($goodsId);
        $goods = $this->getGoodsService()->hit($goodsId);
        return $this->filter($goods);
    }

    public function post(Request $request)
    {
        $goods = $request->request->all();

        if (!ArrayToolkit::requireds($goods, $this->requireFiles)) {
            throw new InvalidArgumentException('缺少必填字段');
        }

        $this->getGoodsService()->createGoods($goods);

        return array('success' => 'true');
    }

    public function publish($goodsId)
    {
        $this->checkGoods($goodsId);

        $this->getGoodsService()->publishGoods($goodsId);

        return array('success' => 'true');
    }

    public function cancel($goodsId)
    {
        $this->checkGoods($goodsId);

        $this->getGoodsService()->cancelGoods($goodsId);

        return array('success' => 'true');
    }

    public function delete($goodsId)
    {
        $this->checkGoods($goodsId);
        $this->getGoodsService()->deleteGoods($goodsId);

        return array('success' => 'true');
    }

    public function like($goodsId)
    {
        $this->checkGoods($goodsId);
        $this->getGoodsService()->like($goodsId);

        return array('success' => 'true');
    }

    public function cancelLike($goodsId)
    {
        $this->checkGoods($goodsId);
        $this->getGoodsService()->cancelLike($goodsId);

        return array('success' => 'true');
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
        $res['imgs'] = json_decode($res['imgs'], true);
        foreach ($res['imgs'] as $key => $uri) {
            $res['imgs'][$key] = $this->getFileUrl($uri);
        }
        $res['publisher'] = $this->callSimplify('User/User', $this->getUserService()->getUser($res['publisher_id']));
        $res['category'] = $this->getCategoryService()->getCategory($res['category_id'])['name'];

        $res['updated_time'] = date('c', $res['updated_time']);
        $res['created_time'] = date('c', $res['created_time']);

        unset($res['publisher_id']);
        unset($res['category_id']);

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

    protected function getCategoryService()
    {
        return $this->biz->service('Category:CategoryService');
    }

    protected function getFileService()
    {
        return $this->biz->service('File:FileService');
    }
}
