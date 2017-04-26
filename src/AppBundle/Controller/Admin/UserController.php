<?php
namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\BaseController;

class UserController extends BaseController
{
    public function indexAction()
    {
        return $this->render('AppBundle:admin/user:index.html.twig', array());
    }
}
