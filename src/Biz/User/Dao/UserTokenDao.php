<?php

namespace Biz\User\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface UserTokenDao extends GeneralDaoInterface
{
    public function getByToken($token);

    public function findByUserIdAndType($userId, $type);
}
