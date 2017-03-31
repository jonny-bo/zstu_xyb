<?php

namespace Biz\Goods\Service;

interface GoodsService
{
    public function getGoods($goodsId);

    public function searchGoods($conditions, $orderBy, $start, $limit);

    public function searchGoodsCount($conditions);

    public function createGoods($fields);

    public function updateGoods($goodsId, $fields);

    public function deleteGoods($goodsId);
}
