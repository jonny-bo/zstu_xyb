<?php
namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\BaseController;
use AppBundle\Common\Paginator;

class UserController extends BaseController
{
    public function indexAction(Request $request)
    {
        $conditions = $request->query->all();
        $orderBy    = $this->getOrderBy($conditions);
        $userCount  = $this->getUserService()->searchUsersCount($conditions);
        $paginator  = new Paginator($request, $userCount, parent::DEFAULT_PAGE_COUNT);

        $users = $this->getUserService()->searchUsers(
            $conditions,
            $orderBy,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        return $this->render('AppBundle:admin/user:index.html.twig', array(
            'users'     => $users,
            'userCount' => $userCount,
            'paginator' => $paginator
        ));
    }

    public function showAction($id)
    {
        $user = $this->getUserService()->getUser($id);

        return $this->render('AppBundle:admin/user:show-modal.html.twig', array(
            'user'     => $user
        ));
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
