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
        'title', 'thumb', 'category_id', 'body', 'price'
    );

    public function getGoods($goodsId)
    {
        return $this->getGoodsDao()->get($goodsId);
    }

    public function searchGoods($conditions, $orderBy, $start, $limit)
    {
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

        $fields['thumb']         = FileToolkit::moveFile(__DIR__.'/../../../../../web/files', $fields['thumb'], 'goods');
        $fields['publisher_id'] = $this->getCurrentUser()['id'];
        $fields['status']       = 0;

        return $this->getGoodsDao()->create($fields);
    }

    public function updateGoods($goodsId, $fields)
    {
        $this->beforUpdate($goodsId);

        return $this->getGoodsDao()->update($goodsId, $fields);
    }

    public function deleteGoods($goodsId)
    {
        return $this->getGoodsDao()->delete($goodsId);
    }

    public function publishGoods($goodsId)
    {
        return $this->updateGoods($goodsId, array('status' => 1));
    }

    public function cancelGoods($goodsId)
    {
        return $this->updateGoods($goodsId, array('status' => 0));
    }

    protected function beforUpdate($goodsId)
    {
        $goods = $this->getGoods($goodsId);

        if (empty($goods)) {
            throw new ResourceNotFoundException('旧货', $goodsId);
        }
        $user = $this->getCurrentUser();

        if (!$user->isLogin() || ($user['id'] != $goods['publisher_id'])) {
            throw new RuntimeException("非法用户操作");
        }

        if ($goods['status'] == 1) {
            throw new RuntimeException("已发布旧货不能操作");
        }
    }
    protected function checkFields($fields)
    {
        if (isset($fields['price']) && !SimpleValidator::float($fields['price'])) {
            throw new InvalidArgumentException("字段不合法");
        }
        if (isset($fields['thumb']) && !empty($fields['thumb'])) {
            if (!FileToolkit::isImageFile($fields['thumb'])) {
                throw new \RuntimeException('您上传的不是图片文件，请重新上传。');
            }
            if (FileToolkit::getMaxFilesize() <= $fields['thumb']->getClientSize()) {
                throw new \RuntimeException('您上传的图片超过限制，请重新上传。');
            }
        }
    }

    protected function getGoodsDao()
    {
        return $this->biz->dao('Goods:GoodsDao');
    }
}
