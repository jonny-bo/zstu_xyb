<?php

namespace Tests\Biz\Express;

use Biz\Common\BaseTestCase;

class ExpressServiceTest extends BaseTestCase
{
    public function testCreateExpress()
    {
        $currentUser = self::$biz['user'];

        $expressText = array(
            'publisher_id' => $currentUser['id'],
            'title' => 'test',
            'detail'    => 'detail'
        );

        $express = $this->getExpressService()->createExpress($expressText);

        $this->assertEquals($express['publisher_id'], $expressText['publisher_id']);
        $this->assertEquals($express['title'], $expressText['title']);
        $this->assertEquals($express['detail'], $expressText['detail']);
    }

    protected function getUserService()
    {
        return self::$biz->service('User:UserService');
    }

    protected function getExpressService()
    {
        return self::$biz->service('Express:ExpressService');
    }
}
