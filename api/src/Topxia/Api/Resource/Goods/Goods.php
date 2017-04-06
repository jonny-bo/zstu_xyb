<?php

namespace Topxia\Api\Resource\Goods;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\ArrayToolkit;
use Biz\Common\FileToolkit;

class Goods extends BaseResource
{
    protected $requireFiles = array(
        'title', 'category_id', 'body', 'price'
    );
    public function search(Request $request)
    {
        $conditions = $request->query->all();

        $start   = $request->query->get('start', 0);
        $limit   = $request->query->get('limit', 10);
        $conditions['status'] = isset($conditions['status']) ? $conditions['status'] : 1;
        if (isset($conditions['title'])) {
            $conditions['title'] = '%'.$conditions['title'].'%';
        }

        $ordeyBy = $this->getOrderBy($conditions, array('updated_time' => 'DESC'));
        $goods = $this->getGoodsService()->searchGoods($conditions, $ordeyBy, $start, $limit);
        $total = $this->getGoodsService()->searchGoodsCount($conditions);

        return $this->wrap($this->multiSimplify($goods), $total);
    }

    public function get($goodsId)
    {
        $goods = $this->checkGoods($goodsId);

        return $this->filter($goods);
    }

    public function post(Request $request)
    {
        $goods = $request->request->all();
        $goods['files'] = $request->files->get('files', array());

        if (!ArrayToolkit::requireds($goods, $this->requireFiles)) {
            throw new InvalidArgumentException('缺少必填字段');
        }
        foreach ($goods['files'] as $file) {
            if (!FileToolkit::isImageFile($file)) {
                throw new \RuntimeException('您上传的不是图片文件，请重新上传。');
            }

            if (FileToolkit::getMaxFilesize() <= $file->getClientSize()) {
                throw new \RuntimeException('您上传的图片超过限制，请重新上传。');
            }
        }

        $this->getGoodsService()->createGoods($goods);

        return array('success' => 'true');
    }

    public function publish($goodsId)
    {
        $this->checkGoods($goodsId);

        $this->getGoodsService()->publishGoods($goodsId);

        return array('success' => 'true');
    }

    public function cancel($goodsId)
    {
        $this->checkGoods($goodsId);

        $this->getGoodsService()->cancelGoods($goodsId);

        return array('success' => 'true');
    }

    public function delete($goodsId)
    {
        $this->checkGoods($goodsId);
        $this->getGoodsService()->deleteGoods($goodsId);

        return array('success' => 'true');
    }

    protected function checkGoods($goodsId)
    {
        $goods = $this->getGoodsService()->getGoods($goodsId);

        if (empty($goods)) {
            return $this->error('404', '请求内容不存在');
        }

        return $goods;
    }

    public function filter($res)
    {
        $res['imgs'] = json_decode($res['imgs'], true);
        foreach ($res['imgs'] as $key => $fileId) {
            $file   = $this->getFileService()->getFile($fileId);
            $res['imgs'][$key] = $this->getFileUrl($file['uri']);
        }
        $res['publisher'] = $this->callSimplify('User/User', $this->getUserService()->getUser($res['publisher_id']));
        $res['category'] = $this->getCategoryService()->getCategory($res['category_id'])['name'];

        $res['updated_time'] = date('c', $res['updated_time']);
        $res['created_time'] = date('c', $res['created_time']);

        unset($res['publisher_id']);
        unset($res['category_id']);

        return $res;
    }

    public function simplify($res)
    {
        $res = $this->filter($res);
        unset($res['body']);

        return $res;
    }

    protected function getGoodsService()
    {
        return $this->biz->service('Goods:GoodsService');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getCategoryService()
    {
        return $this->biz->service('Category:CategoryService');
    }

    protected function getFileService()
    {
        return $this->biz->service('File:FileService');
    }
}
