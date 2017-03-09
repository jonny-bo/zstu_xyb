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

    /**
     * @expectedException Biz\Common\Exception\ResourceNotFoundException
     * @expectedExceptionCode 0
     */
    public function testOrderExpressWithNotExpress()
    {
        $this->getExpressService()->orderExpress(11);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function testOrderExpressIsOrder()
    {
        $expressText = array(
            'title'  => 'test',
            'detail' => 'detail',
            'offer'  => 3,
            'status' => 1
        );

        $express = $this->getExpressService()->createExpress($expressText);
    
        $this->assertEquals($express['status'], 1);

        $this->getExpressService()->orderExpress($express['id']);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function testOrderExpressNotOwner()
    {
        $expressText = array(
            'title'  => 'test',
            'detail' => 'detail',
            'offer'  => 3
        );

        $express = $this->getExpressService()->createExpress($expressText);
    
        $this->assertEquals($express['status'], 0);

        $this->getExpressService()->orderExpress($express['id']);
    }

    public function confirmMyExpressTest()
    {
        $expressText = array(
            'title'  => 'test',
            'detail' => 'detail',
            'offer'  => 3
        );
        $user1 = $this->createUser('test_user1');
        $currentUser1 = new CurrentUser($user1);
        self::$biz['user'] = $currentUser1;

        $express = $this->getExpressService()->createExpress($expressText);

        $this->assertEquals($express['status'], 0);

        $user2 = $this->createUser('test_user2');
        $currentUser2 = new CurrentUser($user2);
        self::$biz['user'] = $currentUser2;

        $express = $this->getExpressService()->orderExpress($express['id']);

        $this->assertEquals($express['status'], 1);

        $express = $this->getExpressService()->confirmMyReceiveExpress($express['id']);

        $this->assertEquals($express['status'], 2);

        self::$biz['user'] = $currentUser1;

        $express = $this->getExpressService()->confirmMyPublishExpress($express['id']);
        $this->assertEquals($express['status'], 3);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function confirmMyExpressTestWithOneUser()
    {
        $expressText = array(
            'title'  => 'test',
            'detail' => 'detail',
            'offer'  => 3
        );
        $user1 = $this->createUser('test_user1');
        $currentUser1 = new CurrentUser($user1);
        self::$biz['user'] = $currentUser1;

        $express = $this->getExpressService()->createExpress($expressText);

        $this->assertEquals($express['status'], 0);

        $user2 = $this->createUser('test_user2');
        $currentUser2 = new CurrentUser($user2);
        self::$biz['user'] = $currentUser2;

        $express = $this->getExpressService()->orderExpress($express['id']);

        $this->assertEquals($express['status'], 1);

        self::$biz['user'] = $currentUser1;

        $express = $this->getExpressService()->confirmMyReceiveExpress($express['id']);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function confirmMyExpressTestWithOtherUser()
    {
        $expressText = array(
            'title'  => 'test',
            'detail' => 'detail',
            'offer'  => 3
        );
        $user1 = $this->createUser('test_user1');
        $currentUser1 = new CurrentUser($user1);
        self::$biz['user'] = $currentUser1;

        $express = $this->getExpressService()->createExpress($expressText);

        $this->assertEquals($express['status'], 0);

        $user2 = $this->createUser('test_user2');
        $currentUser2 = new CurrentUser($user2);
        self::$biz['user'] = $currentUser2;

        $express = $this->getExpressService()->orderExpress($express['id']);

        $this->assertEquals($express['status'], 1);

        $express = $this->getExpressService()->confirmMyReceiveExpress($express['id']);

        $this->assertEquals($express['status'], 2);

        $express = $this->getExpressService()->confirmMyPublishExpress($express['id']);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function confirmMyExpressTestWithStatus()
    {
        $expressText = array(
            'title'  => 'test',
            'detail' => 'detail',
            'offer'  => 3
        );
        $user1 = $this->createUser('test_user1');
        $currentUser1 = new CurrentUser($user1);
        self::$biz['user'] = $currentUser1;

        $express = $this->getExpressService()->createExpress($expressText);

        $this->assertEquals($express['status'], 0);

        $user2 = $this->createUser('test_user2');
        $currentUser2 = new CurrentUser($user2);
        self::$biz['user'] = $currentUser2;

        $this->assertEquals($express['status'], 0);

        $express = $this->getExpressService()->confirmMyReceiveExpress($express['id']);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function confirmMyExpressTestWithOtherStatus()
    {
        $expressText = array(
            'title'  => 'test',
            'detail' => 'detail',
            'offer'  => 3
        );
        $user1 = $this->createUser('test_user1');
        $currentUser1 = new CurrentUser($user1);
        self::$biz['user'] = $currentUser1;

        $express = $this->getExpressService()->createExpress($expressText);

        $this->assertEquals($express['status'], 0);

        $user2 = $this->createUser('test_user2');
        $currentUser2 = new CurrentUser($user2);
        self::$biz['user'] = $currentUser2;

        $this->assertEquals($express['status'], 0);

        self::$biz['user'] = $currentUser1;

        $express = $this->getExpressService()->confirmMyReceiveExpress($express['id']);
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
