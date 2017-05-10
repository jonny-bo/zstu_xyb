<?php
namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\BaseController;
use AppBundle\Common\Paginator;

class SettingController extends BaseController
{
    public function siteAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $settings = $request->request->all();
            $this->getSettingService()->setting($settings);

            return $this->redirect($this->generateUrl('admin_setting_site'));
        }

        return $this->render('AppBundle:admin/system:index.html.twig');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getSettingService()
    {
        return $this->biz->service('System:SettingService');
    }
}
