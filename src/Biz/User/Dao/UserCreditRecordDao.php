<?php

namespace Biz\User\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface UserCreditRecordDao extends GeneralDaoInterface
{
    public function findByUserId($userId);
}
