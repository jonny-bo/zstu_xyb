<?php

namespace AppBundle\Controller;

use AppBundle\Security\CurrentUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BaseController extends Controller
{
    protected $biz;
    const DEFAULT_PAGE_COUNT = 10;
    const START = 0;
    const LIMIT = 10;

    public function login($user, $request)
    {
        $currentUser = new CurrentUser($user);

        $token = new UsernamePasswordToken($currentUser, null, 'main', $currentUser->getRoles());
        $this->get('security.token_storage')->setToken($token);

        $event = new InteractiveLoginEvent($request, $token);
        $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->biz = $this->container->get('biz');
    }

    protected function setFlashMessage($level, $message)
    {
        $this->get('session')->getFlashBag()->add($level, $message);
    }

    protected function createJsonResponse($data)
    {
        return new JsonResponse($data);
    }

    protected function getOrderBy($conditions, $default = array())
    {
        $orderbys = array();
        $orderByStr = isset($conditions['orderby']) ? $conditions['orderby'] : '';
        if ($orderByStr) {
            $orderbyTemp = explode(',', trim($orderByStr, ','));
            foreach ($orderbyTemp as $orderby) {
                list($field, $indicator) = explode(' ', $orderby);
                $orderbys[$field] = strtoupper($indicator);
            }
            return $orderbys;
        } else {
            return $default;
        }
    }
}
