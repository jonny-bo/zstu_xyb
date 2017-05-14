<?php

namespace Biz\Order\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface OrderDao extends GeneralDaoInterface
{
    public function getBySn($orderSn);

    public function getByTargetTypeAndTargetIdAndUserId($targetType, $targetId, $userId);
}
