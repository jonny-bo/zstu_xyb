<?php

namespace Tests\Biz\Goods;

use Biz\Common\BaseTestCase;
use AppBundle\Security\CurrentUser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Biz\Common\ArrayToolkit;
use Biz\Common\FileToolkit;

class GoodsServiceTest extends BaseTestCase
{
    public function testCreateExpress()
    {
        $goodsText = array(
            'title'  => 'test goods',
            'category_id' => 1,
            'body'  => 'ssss',
            'price' => 33,
            'imgs' => []
        );

        $goods = $this->getGoodsService()->createGoods($goodsText);

        $this->assertEquals($goods['title'], $goodsText['title']);
        $this->assertEquals($goods['category_id'], $goodsText['category_id']);
        $this->assertEquals($goods['body'], $goodsText['body']);
        $this->assertEquals($goods['price'], $goodsText['price']);
    }

    public function testGetGoods()
    {
        $goodsText = array(
            'title'  => 'test goods',
            'category_id' => 1,
            'body'  => 'ssss',
            'price' => 33,
            'imgs' => []
        );
        $goods = $this->getGoodsService()->createGoods($goodsText);

        $goods = $this->getGoodsService()->getGoods($goods['id']);

        $this->assertNotEmpty($goods);
    }

    /**
     * @expectedException Biz\Common\Exception\InvalidArgumentException
     * @expectedExceptionCode 0
     */
    public function testCreateExpressWithMissFields()
    {
        $goodsText = array(
            'title'  => 'test',
            'body' => 'detail'
        );

        $this->getGoodsService()->createGoods($goodsText);
    }

    /**
     * @expectedException Biz\Common\Exception\InvalidArgumentException
     * @expectedExceptionCode 0
     */
    public function testCreateExpressWithPrice()
    {
        $goodsText = array(
            'title'  => 'test',
            'body' => 'detail',
            'price' => 'dsa'
        );

        $this->getGoodsService()->createGoods($goodsText);
    }

    /**
     * @expectedException Biz\Common\Exception\InvalidArgumentException
     * @expectedExceptionCode 0
     */
    public function testCreateExpressWithFile()
    {
        $goodsText = array(
            'title'  => 'test',
            'body' => 'detail',
            'price' => 'dsa',
            'imgs' => array()
        );
        for ($i = 0; $i <= 10; $i++) {
            array_push($goodsText['imgs'], $i);
        }

        $this->getGoodsService()->createGoods($goodsText);
    }

    public function testSearchGoods()
    {
        $goodsText = array(
            'title'  => 'test goods',
            'category_id' => 1,
            'body'  => 'ssss',
            'price' => 33,
            'imgs' => []
        );
        $goods = $this->getGoodsService()->createGoods($goodsText);

        $goods = $this->getGoodsService()->searchGoods(array(), array(), 0, 100);

        $this->assertNotEmpty($goods);

        $goods = $this->getGoodsService()->searchGoods(array('title' => 'test'), array(), 0, 100);

        $this->assertNotEmpty($goods);
    }

    public function testSearchGoodsCount()
    {
        $goodsText1 = array(
            'title'  => 'test goods1',
            'category_id' => 1,
            'body'  => 'ssss',
            'price' => 33,
            'imgs' => []
        );

        $goodsText2 = array(
            'title'  => 'test goods2',
            'category_id' => 1,
            'body'  => 'ssss',
            'price' => 33,
            'imgs' => []
        );
        $this->getGoodsService()->createGoods($goodsText1);
        $this->getGoodsService()->createGoods($goodsText2);

        $goods = $this->getGoodsService()->searchGoodsCount(array('title' => 'test'));

        $this->assertEquals($goods, 2);

        $goods = $this->getGoodsService()->searchGoodsCount(array('title' => 'goods1'));

        $this->assertEquals($goods, 1);

        $goods = $this->getGoodsService()->searchGoodsCount(array('title' => 'goods1dsa'));

        $this->assertEquals($goods, 0);
    }

    public function testUpdateGoods()
    {
        $goodsText = array(
            'title'  => 'test goods',
            'category_id' => 1,
            'body'  => 'ssss',
            'price' => 33,
            'imgs' => []
        );
        $goods = $this->getGoodsService()->createGoods($goodsText);

        $this->getGoodsService()->cancelGoods($goods['id']);

        $newGoods = $this->getGoodsService()->updateGoods($goods['id'], array(
            'title' => '11111',
            'price' => 66
        ));

        $this->assertEquals($newGoods['title'], '11111');
        $this->assertEquals($newGoods['price'], 66);
    }

    /**
     * @expectedException Biz\Common\Exception\RuntimeException
     * @expectedExceptionCode 0
     */
    public function testUpdateGoodsWithStatus()
    {
        $goodsText = array(
            'title'  => 'test goods',
            'category_id' => 1,
            'body'  => 'ssss',
            'price' => 33,
            'imgs' => []
        );
        $goods = $this->getGoodsService()->createGoods($goodsText);
        $this->getGoodsService()->publishGoods($goods['id']);

        $this->getGoodsService()->updateGoods($goods['id'], array(
            'status' => '11111',
            'price' => 66
        ));
    }

    /**
     * @expectedException Biz\Common\Exception\ResourceNotFoundException
     * @expectedExceptionCode 0
     */
    public function testUpdateGoodsWithNotGoods()
    {
        $this->getGoodsService()->updateGoods(1, array(
            'status' => '11111',
            'price' => 66
        ));
    }

    /**
     * @expectedException Biz\Common\Exception\RuntimeException
     * @expectedExceptionCode 0
     */
    public function testUpdateGoodsWithUser()
    {
        $goodsText = array(
            'title'  => 'test goods',
            'category_id' => 1,
            'body'  => 'ssss',
            'price' => 33,
            'imgs' => []
        );
        $goods = $this->getGoodsService()->createGoods($goodsText);
        $user = $this->createUser('test_user2', array('ROLE_USER'));
        $currentUser = new CurrentUser($user);
        self::$biz['user'] = $currentUser;

        $this->getGoodsService()->updateGoods($goods['id'], array(
            'status' => 2,
            'price' => 66
        ));
    }

    public function testDeleteGoods()
    {
        $goodsText = array(
            'title'  => 'test goods',
            'category_id' => 1,
            'body'  => 'ssss',
            'price' => 33,
            'imgs' => []
        );
        $goods = $this->getGoodsService()->createGoods($goodsText);

        $this->getGoodsService()->cancelGoods($goods['id']);
        $result = $this->getGoodsService()->deleteGoods($goods['id']);

        $this->assertEquals($result, 1);
    }

    public function testPublishGoods()
    {
        $goodsText = array(
            'title'  => 'test goods',
            'category_id' => 1,
            'body'  => 'ssss',
            'price' => 33,
            'imgs' => []
        );
        $goods = $this->getGoodsService()->createGoods($goodsText);

        $this->assertEquals($goods['status'], 2);
    }

    public function testCancelGoods()
    {
        $goodsText = array(
            'title'  => 'test goods',
            'category_id' => 1,
            'body'  => 'ssss',
            'price' => 33,
            'imgs' => []
        );
        $goods = $this->getGoodsService()->createGoods($goodsText);

        $this->assertEquals($goods['status'], 2);

        $goods = $this->getGoodsService()->cancelGoods($goods['id']);

        $this->assertEquals($goods['status'], 3);
    }

    protected function getUserService()
    {
        return self::$biz->service('User:UserService');
    }

    protected function getGoodsService()
    {
        return self::$biz->service('Goods:GoodsService');
    }
}
