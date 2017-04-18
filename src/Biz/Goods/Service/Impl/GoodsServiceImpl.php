<?php

namespace Biz\Goods\Service\Impl;

use Biz\Common\BaseService;
use Biz\Goods\Service\GoodsService;
use Biz\Common\ArrayToolkit;
use Biz\Common\FileToolkit;
use Biz\Common\SimpleValidator;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;

class GoodsServiceImpl extends BaseService implements GoodsService
{
    protected $requireFiles = array(
        'title', 'category_id', 'body', 'price'
    );

    public function getGoods($goodsId)
    {
        return $this->getGoodsDao()->get($goodsId);
    }

    public function searchGoods($conditions, $orderBy, $start, $limit)
    {
        $conditions['title'] = isset($conditions['title']) ? '%'.$conditions['title'].'%' : '';
        return $this->getGoodsDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchGoodsCount($conditions)
    {
        return $this->getGoodsDao()->count($conditions);
    }

    public function createGoods($fields)
    {
        if (!ArrayToolkit::requireds($fields, $this->requireFiles)) {
            throw new InvalidArgumentException('缺少必填字段');
        }

        $this->checkFields($fields);

        $fileIds = array();
        foreach ($fields['files'] as $file) {
            $res = $this->getFileService()->uploadFile('goods', $file);
            array_push($fileIds, $res['id']);
        }

        $fields['imgs'] = json_encode($fileIds);
        $fields['publisher_id'] = $this->getCurrentUser()['id'];
        $fields['status']       = 0;

        unset($fields['files']);

        return $this->getGoodsDao()->create($fields);
    }

    public function updateGoods($goodsId, $fields)
    {
        $goods = $this->beforAction($goodsId);
        $this->checkFields($fields);

        if ($goods['status'] == 1) {
            throw new RuntimeException('已发布物品不能进行修改');
        }

        return $this->getGoodsDao()->update($goodsId, $fields);
    }

    public function deleteGoods($goodsId)
    {
        $goods = $this->beforAction($goodsId);
        if ($goods['status'] == 1) {
            throw new RuntimeException('该物品已经发布，不能删除');
        }
        $res = $this->getGoodsDao()->delete($goodsId);

        $fileIds = json_decode($goods['imgs'], true);
        foreach ($fileIds as $fileId) {
            $this->getFileService()->deleteFile($fileId);
        }
        return $res;
    }

    public function publishGoods($goodsId)
    {
        $goods = $this->beforAction($goodsId);

        if ($goods['status'] == 1) {
            throw new RuntimeException('该物品已经发布，不要重复发布');
        }

        return $this->getGoodsDao()->update($goodsId, array('status' => 1));
    }

    public function cancelGoods($goodsId)
    {
        $goods = $this->beforAction($goodsId);

        if ($goods['status'] != 1) {
            throw new RuntimeException('未发布的物品不能取消发布');
        }
        return $this->getGoodsDao()->update($goodsId, array('status' => 2));
    }

    public function getGoodsPost($goodsPostId)
    {
        return $this->getGoodsPostDao()->get($goodsPostId);
    }

    public function searchGoodsPosts($conditions, $orderBy, $start, $limit)
    {
        return $this->getGoodsPostDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchGoodsPostsCount($conditions)
    {
        return $this->getGoodsPostDao()->count($conditions);
    }

    public function createGoodsPost($goodsId, $fields)
    {
        if (!ArrayToolkit::requireds($fields, array('content'))) {
            throw new InvalidArgumentException('缺少必填字段');
        }
        $goods = $this->getGoods($goodsId);

        if (!$goods) {
            throw new ResourceNotFoundException('旧货', $goodsId);
        }

        $fields['old_goods_id'] = $goodsId;

        if (empty($fields['content'])) {
            throw new RuntimeException('评论内容不能为空!');
        }

        $user = $this->getCurrentUser();

        if (!$user->isLogin()) {
            throw new AccessDeniedException('未登录用户不能评论!');
        }

        $fields['from_user_id'] = $user['id'];

        $post = $this->getGoodsPostDao()->create($fields);

        $this->getGoodsDao()->wave(array($goodsId), array('post_num' => 1));

        return $post;
    }

    public function deleteGoodsPost($goodsPostId)
    {
        $post = $this->getGoodsPostDao()->get($goodsPostId);
        $res  = $this->getGoodsPostDao()->delete($goodsPostId);
        $this->getGoodsDao()->wave(array($post['old_goods_id']), array('post_num' => -1));

        return $res;
    }

    public function goodsLike($goodsId)
    {
        $user = $this->getCurrentUser();

        $goodsLike = $this->getGoodsLikeDao()->getByUserIdAndGoodsId($user['id'], $goodsId);

        if ($goodsLike) {
            $goodsLike = $this->getGoodsLikeDao()->delete($goodsLike['id']);
            $this->getGoodsDao()->wave(array($goodsId), array('ups_num' => -1));
            return $goodsLike;
        }

        $goodsLike = $this->getGoodsLikeDao()->create(array(
            'user_id'      => $user['id'],
            'old_goods_id' => $goodsId,
            'created_time' => time()
        ));

        $this->getGoodsDao()->wave(array($goodsId), array('ups_num' => 1));

        return $goodsLike;
    }

    protected function beforAction($goodsId)
    {
        $goods = $this->getGoods($goodsId);

        if (empty($goods)) {
            throw new ResourceNotFoundException('旧货', $goodsId);
        }
        $user = $this->getCurrentUser();

        if (!$user->isLogin() || ($user['id'] != $goods['publisher_id'])) {
            throw new RuntimeException("非法用户操作");
        }

        return $goods;
    }

    protected function checkFields($fields)
    {
        if (isset($fields['price']) && !SimpleValidator::float($fields['price'])) {
            throw new InvalidArgumentException("字段不合法");
        }
        if (isset($fields['files']) && !empty($fields['files'])) {
            if (count($fields['files']) >= 10) {
                throw new \RuntimeException('上传数量超过限制。');
            }
            foreach ($fields['files'] as $file) {
                if (!FileToolkit::isImageFile($file)) {
                    throw new \RuntimeException('您上传的不是图片文件，请重新上传。');
                }

                if (FileToolkit::getMaxFilesize() <= $file->getClientSize()) {
                    throw new \RuntimeException('您上传的图片超过限制，请重新上传。');
                }
            }
        }
    }

    protected function getGoodsDao()
    {
        return $this->biz->dao('Goods:GoodsDao');
    }

    protected function getFileService()
    {
        return $this->biz->service('File:FileService');
    }

    protected function getFileGroupService()
    {
        return $this->biz->service('File:FileGroupService');
    }

    protected function getGoodsPostDao()
    {
        return $this->biz->dao('Goods:GoodsPostDao');
    }

    protected function getGoodsLikeDao()
    {
        return $this->biz->dao('Goods:GoodsLikeDao');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
