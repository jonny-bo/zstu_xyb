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

    public function createAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $fields = $request->request->all();
            $this->getUserService->register($fields);

            return $this->redirect($this->generateUrl('admin_user'));
        }
        return $this->render('AppBundle:admin/user:create-modal.html.twig');
    }

    public function checkUsernameAction(Request $request)
    {
        $username = $request->request->get('username');

        $res = $this->getUserService()->isUsernameAvaliable($username);

        return $this->createJsonResponse(array('success' => $res));
    }

    public function checkEmailAction(Request $request)
    {
        $email = $request->request->get('email');

        $res = $this->getUserService()->isEmailAvaliable($email);

        return $this->createJsonResponse(array('success' => $res));
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
