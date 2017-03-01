<?php

namespace Tests\Biz\Express;

use Biz\Common\BaseTestCase;
use AppBundle\Security\CurrentUser;

class ExpressServiceTest extends BaseTestCase
{
    public function testCreateExpress()
    {
        $expressText = array(
            'title'  => 'test',
            'detail' => 'detail',
            'offer'  => 3
        );

        $express = $this->getExpressService()->createExpress($expressText);

        $this->assertEquals($express['offer'], $expressText['offer']);
        $this->assertEquals($express['title'], $expressText['title']);
        $this->assertEquals($express['detail'], $expressText['detail']);
    }

    /**
     * @expectedException Biz\Common\Exception\InvalidArgumentException
     * @expectedExceptionCode 0
     */
    public function testCreateExpressWithMissFields()
    {
        $expressText = array(
            'title'  => 'test',
            'detail' => 'detail'
        );

        $this->getExpressService()->createExpress($expressText);
    }

    /**
     * @expectedException Biz\Common\Exception\AccessDeniedException
     * @expectedExceptionCode 0
     */
    public function testCreateExpressWithNotLogin()
    {
        $expressText = array(
            'title'  => 'test',
            'detail' => 'detail',
            'offer'  => 3
        );

        self::$biz['user'] = '';
        $this->getExpressService()->createExpress($expressText);
    }

    public function testOrderExpress()
    {
        $expressText = array(
            'title'  => 'test',
            'detail' => 'detail',
            'offer'  => 3
        );

        $express = $this->getExpressService()->createExpress($expressText);
    
        $this->assertEquals($express['status'], 0);

        $user = $this->createUser('test_user2');
        $currentUser = new CurrentUser($user);
        self::$biz['user'] = $currentUser;

        $express = $this->getExpressService()->orderExpress($express['id']);

        $this->assertEquals($express['status'], 1);
        $this->assertEquals($express['receiver_id'], $user['id']);
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
