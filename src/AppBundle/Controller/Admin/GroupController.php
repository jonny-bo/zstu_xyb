<?php
namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\BaseController;
use AppBundle\Common\Paginator;
use Biz\Common\ArrayToolkit;

class GroupController extends BaseController
{
    public function groupAction(Request $request)
    {
        $conditions = $request->query->all();
        $orderBy    = $this->getOrderBy($conditions, array('created_time' => 'DESC'));
        $groupCount = $this->getGroupService()->searchGroupsCount($conditions);
        $paginator  = new Paginator($request, $groupCount, parent::DEFAULT_PAGE_COUNT);

        $groups = $this->getGroupService()->searchGroups(
            $conditions,
            $orderBy,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $userIds   = ArrayToolkit::column($groups, 'owner_id');
        $users     = $this->getUserService()->findUsersByIds($userIds);
        $users     = ArrayToolkit::index($users, 'id');

        return $this->render('AppBundle:admin/group:index-group.html.twig', array(
            'groups'      => $groups,
            'groupCount'  => $groupCount,
            'users'       => $users,
            'paginator'   => $paginator
        ));
    }

    public function showAction($id)
    {
        $group  = $this->getGroupService()->getGroup($id);
        $user   = $this->getUserService()->getUser($group['owner_id']);
        return $this->render('AppBundle:admin/group:show-modal.html.twig', array(
            'group' => $group,
            'user'  => $user,
        ));
    }

    public function threadShow($id)
    {
        $thread = $this->getThreadService()->getThread($id);
        $group  = $this->getGroupService()->getGroup($thread['group_id']);
        $user   = $this->getUserService()->getUser($thread['user_id']);
        return $this->render('AppBundle:admin/group:thread-show-modal.html.twig', array(
            'group' => $group,
            'user'  => $user,
        ));
    }

    public function threadAction(Request $request)
    {
        $conditions = $request->query->all();
        $orderBy    = $this->getOrderBy($conditions, array('created_time' => 'DESC'));
        $threadCount = $this->getThreadService()->searchThreadsCount($conditions);
        $paginator  = new Paginator($request, $threadCount, parent::DEFAULT_PAGE_COUNT);

        $threads = $this->getThreadService()->searchThreads(
            $conditions,
            $orderBy,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $userIds   = ArrayToolkit::column($threads, 'user_id');
        $users     = $this->getUserService()->findUsersByIds($userIds);
        $users     = ArrayToolkit::index($users, 'id');
        $groupIds  = ArrayToolkit::column($threads, 'group_id');
        $groups    = $this->getGroupService()->findGroupsByIds($groupIds);
        $groups    = ArrayToolkit::index($groups, 'id');

        return $this->render('AppBundle:admin/group:index-thread.html.twig', array(
            'threads'     => $threads,
            'threadCount' => $threadCount,
            'users'       => $users,
            'groups'      => $groups,
            'paginator'   => $paginator
        ));
    }

    public function openAction($id)
    {
        $group = $this->getGroupService()->openGroup($id);
        $user  = $this->getUserService()->getUser($group['owner_id']);

        $this->getLogService()->info('group', $group['id'], 'open_group', "发布小组{$group['title']}(#{$group['id']})");

        return $this->render('AppBundle::admin/group/group-table-tr.html.twig', array(
            'group' => $group,
            'user'  => $user
        ));
    }

    public function closeAction($id)
    {
        $group = $this->getGroupService()->closeGroup($id);
        $user  = $this->getUserService()->getUser($group['owner_id']);

        $this->getLogService()->info('group', $group['id'], 'close_group', "关闭小组{$group['title']}(#{$group['id']})");

        return $this->render('AppBundle::admin/group/group-table-tr.html.twig', array(
            'group' => $group,
            'user'  => $user
        ));
    }

    public function transferAction(Request $request, $id)
    {
        if ($request->getMethod() == 'POST') {
            $data   = $request->request->all();
            $user   = $this->getUserService()->getUserByUsername($data['username']);
            $group  = $this->getGroupService()->getGroup($id);
            $member = $this->getGroupService()->getMemberByGroupIdAndUserId($id, $group['owner_id']);

            $this->getGroupService()->updateMember($member['id'], array('role'=>'member'));
            $this->getGroupService()->updateGroup($id, array('owner_id'=>$user['id']));

            $member = $this->getGroupService()->getMemberByGroupIdAndUserId($id, $user['id']);

            if ($member) {
                $this->getGroupService()->updateMember($member['id'], array('role'=>'owner'));
            } else {
                $this->getGroupService()->joinGroup($id, $user['id']);
            }

            $this->getLogService()->info('group', $group['id'], 'transfer_group', "转让小组{$group['title']}(#{$group['id']})给{$user['nickname']}");

            return $this->redirect($this->generateUrl('admin_group'));
        }

        return $this->render('AppBundle:admin/group:transfer-modal.html.twig', array(
            'id' => $id
        ));
    }

    public function checkUserAction(Request $request)
    {
        $username = $request->query->get('value');
        $result   = $this->getUserService()->getUserByUsername($username);

        if ($result) {
            $response = true;
        } else {
            $response = false;
        }

        return $this->createJsonResponse($response);
    }

    public function openThreadAction($id)
    {
        $thread = $this->getThreadService()->openThread($id);
        $user   = $this->getUserService()->getUser($thread['user_id']);
        $group  = $this->getGroupService()->getGroup($thread['group_id']);

        $this->getLogService()->info('thread', $thread['id'], 'open_thread', "发布话题{$thread['title']}(#{$thread['id']})");

        return $this->render('AppBundle::admin/group/thread-table-tr.html.twig', array(
            'thread' => $thread,
            'user'   => $user,
            'group'  => $group
        ));
    }

    public function closeThreadAction($id)
    {
        $thread = $this->getThreadService()->closeThread($id);
        $user   = $this->getUserService()->getUser($thread['user_id']);
        $group  = $this->getGroupService()->getGroup($thread['group_id']);

        $this->getLogService()->info('thread', $thread['id'], 'close_thread', "关闭话题{$thread['title']}(#{$thread['id']})");

        return $this->render('AppBundle::admin/group/thread-table-tr.html.twig', array(
            'thread' => $thread,
            'user'  => $user,
            'group'  => $group
        ));
    }

    public function deleteThreadAction($id)
    {
        $thread = $this->getThreadService()->getThread($id);
        $this->getThreadService()->deleteThread($id);

        $this->getLogService()->warning('thread', $thread['id'], 'delete_thread', "删除话题{$thread['title']}(#{$thread['id']})");

        return $this->createJsonResponse('success');
    }

    public function cancelStickAction($id)
    {
        $thread = $this->getThreadService()->updateThread($id, array('is_stick' => 0));
        $user   = $this->getUserService()->getUser($thread['user_id']);
        $group  = $this->getGroupService()->getGroup($thread['group_id']);

        return $this->render('AppBundle::admin/group/thread-table-tr.html.twig', array(
            'thread' => $thread,
            'user'   => $user,
            'group'  => $group
        ));
    }

    public function setStickAction($id)
    {
        $thread = $this->getThreadService()->updateThread($id, array('is_stick' => 1));
        $user   = $this->getUserService()->getUser($thread['user_id']);
        $group  = $this->getGroupService()->getGroup($thread['group_id']);

        return $this->render('AppBundle::admin/group/thread-table-tr.html.twig', array(
            'thread' => $thread,
            'user'   => $user,
            'group'  => $group
        ));
    }

    public function cancelEliteAction($id)
    {
        $thread = $this->getThreadService()->updateThread($id, array('is_elite' => 0));
        $user   = $this->getUserService()->getUser($thread['user_id']);
        $group  = $this->getGroupService()->getGroup($thread['group_id']);

        return $this->render('AppBundle::admin/group/thread-table-tr.html.twig', array(
            'thread' => $thread,
            'user'   => $user,
            'group'  => $group
        ));
    }

    public function setEliteAction($id)
    {
        $thread = $this->getThreadService()->updateThread($id, array('is_elite' => 1));
        $user   = $this->getUserService()->getUser($thread['user_id']);
        $group  = $this->getGroupService()->getGroup($thread['group_id']);

        return $this->render('AppBundle::admin/group/thread-table-tr.html.twig', array(
            'thread' => $thread,
            'user'   => $user,
            'group'  => $group
        ));
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getGroupService()
    {
        return $this->biz->service('Group:GroupService');
    }

    protected function getThreadService()
    {
        return $this->biz->service('Group:ThreadService');
    }
}
