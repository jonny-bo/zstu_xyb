<?php

namespace Biz\Crontab\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface CrontabDao extends GeneralDaoInterface
{
    public function findByNameAndTargetTypeAndTargetId($jobName, $targetType, $targetId);

    public function findByTargetTypeAndTargetId($targetType, $targetId);

    public function deleteByTargetIdAndTargetType($targetId, $targetType);
}
