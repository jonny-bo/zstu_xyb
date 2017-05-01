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
        $orderBy    = $this->getOrderBy($conditions);
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

    public function threadAction(Request $request)
    {
        $conditions = $request->query->all();
        $orderBy    = $this->getOrderBy($conditions);
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

        return $this->render('AppBundle::admin/group/group-table-tr.html.twig', array(
            'group' => $group,
            'user'  => $user
        ));
    }

    public function closeAction($id)
    {
        $group = $this->getGroupService()->closeGroup($id);
        $user  = $this->getUserService()->getUser($group['owner_id']);

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
