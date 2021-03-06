<?php

namespace Biz\Group\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Group\Dao\GroupMemberDao;

class GroupMemberDaoImpl extends GeneralDaoImpl implements GroupMemberDao
{
    protected $table = 'groups_member';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array('created_time'),
            'conditions' => array(
                'group_id = :group_id'
            ),
        );
    }

    public function getByGroupIdAndUserId($groupId, $userId)
    {
        return $this->getByFields(array(
            'group_id' => $groupId,
            'user_id'      => $userId
        ));
    }

    public function findByUserId($userId)
    {
        return $this->findByFields(array(
            'user_id' => $userId
        ));
    }
}
