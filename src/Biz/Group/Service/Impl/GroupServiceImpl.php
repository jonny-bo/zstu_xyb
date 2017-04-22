<?php

namespace Biz\Group\Service\Impl;

use Biz\Common\BaseService;
use Biz\Group\Service\GroupService;
use Biz\Common\ArrayToolkit;
use Biz\Common\SimpleValidator;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;

class GroupServiceImpl extends BaseService implements GroupService
{
    public function getGroup($groupId)
    {
        return $this->getGroupDao()->get($groupId);
    }

    public function searchGroups($conditions, $orderBy, $start, $limit)
    {
        return $this->getGroupDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchGroupsCount($conditions)
    {
        return $this->getGroupDao()->count($conditions);
    }

    public function createGroup($fields)
    {
        return $this->getGroupDao()->create($fields);
    }

    public function updateGroup($groupId, $fields)
    {
        if (isset($fields['about'])) {
            $fields['about']=$this->purifyHtml($fields['about']);
        }
        return $this->getGroupDao()->update($groupId, $fields);
    }

    public function deleteGroup($groupId)
    {
        return $this->getGroupDao()->delete($groupId);
    }

    public function closeGroup($groupId)
    {
        return $this->updateGroup($groupId, array(
            'status' => 'close',
        ));
    }

    public function openGroup($groupId)
    {
        return $this->updateGroup($groupId, array(
            'status' => 'open',
        ));
    }

    public function changeGroupImg($groupId, $field, $data)
    {
        if (!in_array($field, array('logo', 'backgroundLogo'))) {
            throw new InvalidArgumentException('更新的字段错误！');
        }

        $group=$this->getGroup($groupId);
        if (empty($group)) {
            throw new ResourceNotFoundException('小组', $groupId);
        }

        $fileIds = ArrayToolkit::column($data, 'id');
        $files = $this->getFileService()->findFilesByIds($fileIds);

        $files = ArrayToolkit::index($files, "id");
        $fileIds = ArrayToolkit::index($data, "type");

        $fields = array(
            $field => $files[$fileIds[$field]["id"]]["uri"],
        );

        $oldAvatars = array(
            $field => $group[$field] ? $group[$field] : null,
        );

        $fileService = $this->getFileService();
        array_map(function ($oldAvatar) use ($fileService) {
            if (!empty($oldAvatar)) {
                $fileService->deleteFileByUri($oldAvatar);
            }
        }, $oldAvatars);

        return $this->getGroupDao()->updateGroup($groupId, $fields);
    }

    public function joinGroup($groupId, $userId)
    {
        $group = $this->getGroup($groupId);
        if (empty($group)) {
            throw new ResourceNotFoundException('小组', $groupId);
        }
        $user = $this->UserService()->getUser($userId);

        if (empty($user)) {
            throw new ResourceNotFoundException('用户', $userId);
        }
        // if($this->isMember($groupId, $user['id'])){
        //     throw $this->createServiceException($this->getKernel()->trans('您已加入小组！！'));
        // }

        $member = array(
            'groupId' => $groupId,
            'userId' => $user['id']
        );

        return $this->getGroupMemberDao()->create($member);
    }

    public function exitGroup($groupId, $userId)
    {
        $group = $this->getGroup($groupId);
        if (empty($group)) {
            throw new ResourceNotFoundException('小组', $groupId);
        }
        $user = $this->UserService()->getUser($userId);

        if (empty($user)) {
            throw new ResourceNotFoundException('用户', $userId);
        }
        $member = $this->getGroupMemberDao()->getByGroupIdAndUserId($groupId, $user['id']);

        if (empty($member)) {
            throw new RuntimeException('您不是该小组成员，退出失败！');
        }

        return $this->getGroupMemberDao()->delete($member['id']);
    }

    protected function getGroupDao()
    {
        return $this->biz->dao('Group:GroupDao');
    }

    protected function getGroupMemberDao()
    {
        return $this->biz->dao('Group:GroupMemberDao');
    }
}
