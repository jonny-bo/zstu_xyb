<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
    public function indexAction(Request $request)
    {
        $groups = $this->getCategoryGroupService()->getCategoryGroup(1);

        var_dump($groups);exit();
        return $this->render('default/index.html.twig', array());
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getCategoryGroupService()
    {
        return $this->biz->service('Category:CategoryGroupService');
    }
}
