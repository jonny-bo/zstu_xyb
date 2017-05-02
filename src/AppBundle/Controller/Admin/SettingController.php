<?php
namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\BaseController;
use AppBundle\Common\Paginator;

class SettingController extends BaseController
{
    public function siteAction()
    {
        return $this->render('AppBundle:admin/system:index.html.twig');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
