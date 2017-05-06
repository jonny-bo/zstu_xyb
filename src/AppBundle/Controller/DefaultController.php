<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
    public function indexAction()
    {
        $goodses = $this->getGoodsService()->searchGoods(
            array('status' => 2),
            array('hits' => 'DESC'),
            0,
            8
        );
        $groups = $this->getGroupService()->searchGroups(
            array('status' => 'open'),
            array('post_num' => 'DESC'),
            0,
            6
        );
        $threads = $this->getThreadService()->searchThreads(
            array('status' => 'open'),
            array('post_num' => 'DESC'),
            0,
            5
        );
        return $this->render('AppBundle:default:index.html.twig', array(
            'goodses' => $goodses,
            'groups'  => $groups,
            'threads' => $threads,
        ));
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getCategoryGroupService()
    {
        return $this->biz->service('Category:CategoryGroupService');
    }

    protected function getGoodsService()
    {
        return $this->biz->service('Goods:GoodsService');
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
