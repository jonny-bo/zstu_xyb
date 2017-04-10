<?php

namespace Tests\Biz\Goods;

use Biz\Common\BaseTestCase;
use AppBundle\Security\CurrentUser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GoodsServiceTest extends BaseTestCase
{
    public function testCreateExpress()
    {
        $goodsText = array(
            'title'  => 'test goods',
            'category_id' => 1,
            'body'  => 'ssss',
            'price' => 33,
            'files' => []
        );
        // $sourceFile = __DIR__.'/Fixtures/test.gif';
        // $testFile = __DIR__.'/Fixtures/test_test.gif';
        // copy($sourceFile, $testFile);
        // $file = new UploadedFile(
        //     $testFile,
        //     'original.gif',
        //     'image/gif',
        //     filesize($testFile),
        //     UPLOAD_ERR_OK,
        //     true
        // );

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
            'files' => []
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

    protected function getUserService()
    {
        return self::$biz->service('User:UserService');
    }

    protected function getGoodsService()
    {
        return self::$biz->service('Goods:GoodsService');
    }
}
