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

    public function publishAction($id)
    {
        $goods     = $this->getGoodsService()->publishGoods($id);
        $category = $this->getCategoryService()->getCategory($goods['category_id']);
        $user     = $this->getUserService()->getUser($goods['publisher_id']);

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
            $category = $this->getCategoryService()->createCategory($request->request->all());
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
            $category = $this->getCategoryService()->updateCategory($id, $request->request->all());
            return $this->redirect($this->generateUrl('admin_goods_category'));
        }

        return $this->render('AppBundle:admin/goods:category-modal.html.twig', array(
            'category' => $category
        ));
    }

    public function deleteCategoryAction($id)
    {
        $this->getCategoryService()->deleteCategory($id);

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
