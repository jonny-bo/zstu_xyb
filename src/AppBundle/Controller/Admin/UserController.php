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
        $orderBy    = $this->getOrderBy($conditions, array('created_time' => 'DESC'));
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
            $fields['created_ip'] = $request->getClientIp();

            $user = $this->getUserService()->register($fields);

            $this->getLogService()->info('user', $user['id'], 'register_user', "添加用户{$user['nickname']}(#{$user['id']})", array(), $user);

            $this->setFlashMessage('success', '添加用户成功');

            return $this->redirect($this->generateUrl('admin_user'));
        }
        return $this->render('AppBundle:admin/user:create-modal.html.twig');
    }

    public function checkUsernameAction(Request $request)
    {
        $username = $request->request->get('username');

        $res = $this->getUserService()->isUsernameAvaliable($username);

        return $this->createJsonResponse($res);
    }

    public function checkEmailAction(Request $request)
    {
        $email = $request->request->get('email');

        $res = $this->getUserService()->isEmailAvaliable($email);

        return $this->createJsonResponse($res);
    }

    public function rolesAction(Request $request, $id)
    {
        $user        = $this->getUserService()->getUser($id);

        if ($request->getMethod() == 'POST') {
            $roles = $request->request->get('roles');

            $this->getUserService()->changeUserRoles($user['id'], $roles);

            $user = $this->getUserService()->getUser($id);

            $this->getLogService()->info('user', $user['id'], 'change_role', "设置用户{$user['nickname']}(#{$user['id']})的角色为：".implode(',', $roles));

            return $this->render('AppBundle::admin/user/table-tr.html.twig', array(
                'user'    => $user
            ));
        }

        return $this->render('AppBundle:admin/user:roles-modal.html.twig', array(
            'user' => $user
        ));
    }

    public function lockAction($id)
    {
        $this->getUserService()->lockUser($id);
        $this->kickUserLogout($id);
        $user = $this->getUserService()->getUser($id);
        $this->getLogService()->info('user', $user['id'], 'lock_user', "封禁用户{$user['nickname']}(#{$user['id']})");
        return $this->render('AppBundle::admin/user/table-tr.html.twig', array(
            'user'    => $user,
        ));
    }

    public function unlockAction($id)
    {
        $this->getUserService()->unlockUser($id);
        $user = $this->getUserService()->getUser($id);
        $this->getLogService()->info('user', $user['id'], 'unlock_user', "解禁禁用户{$user['nickname']}(#{$user['id']})");
        return $this->render('AppBundle::admin/user/table-tr.html.twig', array(
            'user'    => $user,
        ));
    }

    protected function kickUserLogout($userId)
    {
        $tokens = $this->getUserService()->findTokensByUserIdAndType($userId, 'mobile_login');
        if (!empty($tokens)) {
            foreach ($tokens as $token) {
                $this->getUserService()->deleteToken($token['type'], $token['token']);
            }
        }
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
