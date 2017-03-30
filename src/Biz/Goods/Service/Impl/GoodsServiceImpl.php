<?php

namespace Biz\Goods\Service\Impl;

use Biz\Common\BaseService;
use Biz\Goods\Service\GoodsService;

class GoodsServiceImpl extends BaseService implements GoodsService
{
    public function getGoods($goodsId)
    {
        return $this->getGoodsDao()->get($goodsId);
    }

    public function searchGoodss($conditions, $orderBy, $start, $limit)
    {
        return $this->getGoodsDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchGoodssCount($conditions)
    {
        return $this->getGoodsDao()->count($conditions);
    }

    public function createGoods($fields)
    {
        return $this->getGoodsDao()->create($fields);
    }

    public function updateGoods($goodsId, $fields)
    {
        return $this->getGoodsDao()->update($goodsId, $fields);
    }

    public function deleteGoods($goodsId)
    {
        return $this->getGoodsDao()->delete($goodsId);
    }

    protected function getGoodsDao()
    {
        return $this->biz->dao('Goods:GoodsDao');
    }
}
