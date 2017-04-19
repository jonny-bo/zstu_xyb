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

    public function publishGoods($goodsId);

    public function cancelGoods($goodsId);

    public function getGoodsPost($goodsPostId);

    public function searchGoodsPosts($conditions, $orderBy, $start, $limit);

    public function searchGoodsPostsCount($conditions);

    public function createGoodsPost($goodsId, $fields);

    public function deleteGoodsPost($goodsPostId);

    public function like($goodsId);

    public function cancelLike($goodsId);

    public function hit($goodsId);
}
