<?php

namespace Biz\Goods\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface GoodsLikeDao extends GeneralDaoInterface
{
    public function getByUserIdAndGoodsId($userId, $goodsId);
}
