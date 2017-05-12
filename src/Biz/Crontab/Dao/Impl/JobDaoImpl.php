<?php

namespace Biz\Crontab\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Crontab\Dao\JobDao;

class JobDaoImpl extends GeneralDaoImpl implements JobDao
{
    protected $table = 'crontab_job';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array('job_params' => 'json'),
            'orderbys' => array('created_time', 'next_excuted_time'),
            'conditions' => array(
                'name LIKE :name',
                'cycle = :cycle',
                'job_class = :job_class',
                'executing = :executing',
                'next_excuted_time <= :nextExcutedTime',
                'next_excuted_time <= :nextExcutedEndTime',
                'next_excuted_time >= :nextExcutedStartTime',
                'user_id = :user_id'
            ),
        );
    }

    public function findByNameAndTargetTypeAndTargetId($jobName, $targetType, $targetId)
    {
        return $this->findByFields(array(
            'job_name' => $jobName,
            'target_type' => $targetType,
            'target_id' => $targetId
        ));
    }

    public function findByTargetTypeAndTargetId($targetType, $targetId)
    {
        return $this->findByFields(array(
            'target_type' => $targetType,
            'target_id' => $targetId
        ));
    }

    public function deleteByTargetIdAndTargetType($targetId, $targetType)
    {
        return $this->db()->delete($this->table, array('target_id' => $targetId, 'target_type' => $targetType));
    }
}
