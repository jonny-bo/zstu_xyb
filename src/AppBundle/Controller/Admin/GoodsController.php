<?php
namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\BaseController;
use AppBundle\Common\Paginator;
use Biz\Common\ArrayToolkit;

class GoodsController extends BaseController
{
    public function indexAction(Request $request)
    {
        $conditions = $request->query->all();
        $orderBy    = $this->getOrderBy($conditions);
        $goodsCount = $this->getGoodsService()->searchGoodsCount($conditions);
        $paginator  = new Paginator($request, $goodsCount, parent::DEFAULT_PAGE_COUNT);

        $goodses = $this->getGoodsService()->searchGoods(
            $conditions,
            $orderBy,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $userIds   = ArrayToolkit::column($goodses, 'publisher_id');
        $users     = $this->getUserService()->findUsersByIds($userIds);
        $users     = ArrayToolkit::index($users, 'id');
        $categorys = $this->getCategoryService()->findCategoryByGroupCode('goods');
        $categorys = array_column($categorys, 'name', 'id');

        return $this->render('AppBundle:admin/goods:index.html.twig', array(
            'goodses'     => $goodses,
            'goodsCount'  => $goodsCount,
            'users'       => $users,
            'paginator'   => $paginator,
            'categorys'   => $categorys
        ));
    }

    public function showAction($id)
    {
        $goods      = $this->getGoodsService()->getGoods($id);
        $publisher  = $this->getUserService()->getUser($goods['publisher_id']);

        return $this->render('AppBundle:admin/goods:show-modal.html.twig', array(
            'goods'       => $goods,
            'publisher'   => $publisher
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
}
