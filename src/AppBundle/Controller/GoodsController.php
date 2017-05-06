<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Controller\BaseController;
use AppBundle\Common\Paginator;
use Biz\Common\ArrayToolkit;

class GoodsController extends BaseController
{
    public function indexAction(Request $request)
    {
        $conditions = $request->query->all();
        $conditions['status'] = 2;
        $orderBy    = $this->getOrderBy($conditions, array('created_time' => 'DESC'));
        $goodsCount = $this->getGoodsService()->searchGoodsCount($conditions);
        $paginator  = new Paginator($request, $goodsCount, parent::DEFAULT_PAGE_COUNT);

        $goodses = $this->getGoodsService()->searchGoods(
            $conditions,
            $orderBy,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $categorys = $this->getCategoryService()->findCategoryByGroupCode('goods');
        $categorys = array_column($categorys, 'name', 'id');

        if ($request->isXmlHttpRequest()) {
            $page = $request->query->get('page');
            $currentPage = $paginator->getCurrentPage();
            if ($page <= $currentPage) {
                return $this->render('AppBundle:goods:goods-list.html.twig', array(
                    'goodses'     => $goodses,
                    'goodsCount'  => $goodsCount,
                ));
            } else {
                return new Response('');
            }
        }

        return $this->render('AppBundle:goods:index.html.twig', array(
            'goodses'     => $goodses,
            'paginator'   => $paginator,
            'goodsCount'  => $goodsCount,
            'categorys'   => $categorys
        ));
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getCategoryService()
    {
        return $this->biz->service('Category:CategoryService');
    }

    protected function getGoodsService()
    {
        return $this->biz->service('Goods:GoodsService');
    }

    protected function getCategoryGroupService()
    {
        return $this->biz->service('Category:CategoryGroupService');
    }
}
