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
        $orderBy    = $this->getOrderBy($conditions, array('created_time' => 'DESC'));
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

    public function publishAction($id)
    {
        $goods     = $this->getGoodsService()->publishGoods($id);
        $category = $this->getCategoryService()->getCategory($goods['category_id']);
        $user     = $this->getUserService()->getUser($goods['publisher_id']);

        $this->getLogService()->info('goods', $goods['id'], 'publish_goods', "发布旧货交易{$goods['title']}(#{$goods['id']})");

        return $this->render('AppBundle::admin/goods/table-tr.html.twig', array(
            'goods'    => $goods,
            'category' => $category['name'],
            'user'     => $user
        ));
    }

    public function closeAction($id)
    {
        $goods     = $this->getGoodsService()->cancelGoods($id);
        $category = $this->getCategoryService()->getCategory($goods['category_id']);
        $user     = $this->getUserService()->getUser($goods['publisher_id']);

        $this->getLogService()->info('goods', $goods['id'], 'publish_goods', "关闭旧货交易{$goods['title']}(#{$goods['id']})");

        return $this->render('AppBundle::admin/goods/table-tr.html.twig', array(
            'goods'    => $goods,
            'category' => $category['name'],
            'user'     => $user
        ));
    }

    public function categoryAction()
    {
        $group = $this->getCategoryGroupService()->getCategoryGroupByCode('goods');
        $categories = $this->getCategoryService()->getCategoryStructureTree('goods');

        return $this->render('AppBundle:admin/goods:categrys.html.twig', array(
            'categories' => $categories,
            'group'      => $group
        ));
    }

    public function createCategoryAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $fields = $request->request->all();
            unset($fields['file']);
            $category = $this->getCategoryService()->createCategory($fields);

            $this->getLogService()->info('category', $category['id'], 'create_category', "创建分类{$category['name']}(#{$category['id']})", array(), $category);

            return $this->redirect($this->generateUrl('admin_goods_category'));
        }

        $category = array(
            'id'          => 0,
            'name'        => '',
            'code'        => '',
            'description' => '',
            'group_id'     => (int) $request->query->get('groupId'),
            'parent_id'    => (int) $request->query->get('parentId', 0),
            'weight'      => 0,
            'icon'        => ''
        );

        return $this->render('AppBundle:admin/goods:category-modal.html.twig', array(
            'category' => $category
        ));
    }

    public function editCategoryAction(Request $request, $id)
    {
        $category = $this->getCategoryService()->getCategory($id);

        if ($request->getMethod() == 'POST') {
            $oldData = $category;
            $fields = $request->request->all();
            unset($fields['file']);
            $category = $this->getCategoryService()->updateCategory($id, $fields);

            $this->getLogService()->info('category', $category['id'], 'edit_category', "编辑分类{$category['name']}(#{$category['id']})", $oldData, $category);

            return $this->redirect($this->generateUrl('admin_goods_category'));
        }

        return $this->render('AppBundle:admin/goods:category-modal.html.twig', array(
            'category' => $category
        ));
    }

    public function deleteCategoryAction($id)
    {
        $category = $this->getCategoryService()->getCategory($id);
        $this->getCategoryService()->deleteCategory($id);

        $this->getLogService()->warning('category', $category['id'], 'delete_category', "删除分类{$category['name']}(#{$category['id']})");

        return $this->redirect($this->generateUrl('admin_goods_category'));
    }

    public function checkcodeAction(Request $request)
    {
        $code    = $request->query->get('code');
        $exclude = $request->query->get('exclude');

        if ($exclude && $exclude == $code) {
            return $this->createJsonResponse(true);
        }

        $category = $this->getCategoryService()->getCategoryByCode($code);

        if (empty($category)) {
            return $this->createJsonResponse(true);
        }

        return $this->createJsonResponse(false);
    }

    public function ordersAction(Request $request)
    {
        $conditions = $request->query->all();
        $orderBy    = $this->getOrderBy($conditions, array('createdTime' => 'DESC'));
        $conditions['target_type'] = 'goods';

        $ordersCount  = $this->getOrderService()->searchOrderCount($conditions);
        $paginator  = new Paginator($request, $ordersCount, parent::DEFAULT_PAGE_COUNT);

        $orders = $this->getOrderService()->searchOrder(
            $conditions,
            $orderBy,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $userIds = ArrayToolkit::column($orders, 'user_id');

        $users   = $this->getUserService()->findUsersByIds($userIds);
        $users   = ArrayToolkit::index($users, 'id');

        return $this->render('AppBundle:admin/goods:orders.html.twig', array(
            'orders'        => $orders,
            'ordersCount'   => $ordersCount,
            'users'         => $users,
            'paginator'     => $paginator
        ));
    }

    public function showOrderAction($orderId)
    {
        $order = $this->getOrderService()->getOrder($orderId);
        $user = $this->getUserService()->getUser($order['user_id']);
        $orderLogs = $this->getOrderService()->findOrderLogsByorderId($orderId);

        return $this->render('AppBundle:admin/goods:order-show-modal.html.twig', array(
            'order'   => $order,
            'user'     => $user,
            'orderLogs' => $orderLogs
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

    protected function getOrderService()
    {
        return $this->biz->service('Order:OrderService');
    }
}
