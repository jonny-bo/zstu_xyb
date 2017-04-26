<?php
namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\BaseController;

class DefaultController extends BaseController
{
    public function indexAction()
    {
        return $this->render('AppBundle:admin/default:index.html.twig', array());
    }
}
