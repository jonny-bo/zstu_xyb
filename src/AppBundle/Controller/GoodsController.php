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
        if (isset($conditions['children_id'])) {
            $conditions['category_id'] = $conditions['children_id'];
        }
        $orderBy    = $this->getOrderBy($conditions, array('created_time' => 'DESC'));
        $goodsCount = $this->getGoodsService()->searchGoodsCount($conditions);
        $paginator  = new Paginator($request, $goodsCount, parent::DEFAULT_PAGE_COUNT);

        $goodses = $this->getGoodsService()->searchGoods(
            $conditions,
            $orderBy,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $categorys = $this->getCategoryService()->getCategoryStructureTree('goods');
        $categorys = ArrayToolkit::index($categorys, 'id');

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

    public function goodsAction($id)
    {
        $goods = $this->getGoodsService()->getGoods($id);
        $user  = $this->getUserService()->getUser($goods['publisher_id']);

        return $this->render('AppBundle:goods:goods.html.twig', array(
            'goods'     => $goods,
            'user'      => $user
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
