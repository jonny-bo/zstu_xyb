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

    public function testDeleteExpress()
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

        $result = $this->getExpressService()->deleteExpress($express['id']);

        $this->assertEquals($result, 1);
    }

    public function testSearchExpressCount()
    {
        $expressText1 = array(
            'title'  => 'test1',
            'detail' => 'detail1',
            'offer'  => 3
        );
        $expressText2 = array(
            'title'  => 'test2',
            'detail' => 'detail2',
            'offer'  => 3
        );
        $expressText3 = array(
            'title'  => 'test3',
            'detail' => 'detail3',
            'offer'  => 3
        );

        $express1 = $this->getExpressService()->createExpress($expressText1);
        $express2 = $this->getExpressService()->createExpress($expressText2);
        $express3 = $this->getExpressService()->createExpress($expressText3);

        $this->assertEquals($express1['offer'], $expressText1['offer']);
        $this->assertEquals($express2['title'], $expressText2['title']);
        $this->assertEquals($express3['detail'], $expressText3['detail']);

        $count = $this->getExpressService()->searchExpressCount(array());

        $this->assertEquals($count, 3);
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
    
        $this->assertEquals($express['status'], 1);

        $user = $this->createUser('test_user2');
        $currentUser = new CurrentUser($user);
        self::$biz['user'] = $currentUser;

        $express = $this->getExpressService()->orderExpress($express['id'], $currentUser['id']);

        $this->assertEquals($express['status'], 2);
        $this->assertEquals($express['receiver_id'], $user['id']);
    }

    /**
     * @expectedException Biz\Common\Exception\ResourceNotFoundException
     * @expectedExceptionCode 0
     */
    public function testOrderExpressWithNotExpress()
    {
        $user = $this->createUser('test_user2');
        $this->getExpressService()->orderExpress(11, $user['id']);
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
        );

        $user = $this->createUser('test_user2');
        $express = $this->getExpressService()->createExpress($expressText);
    
        $this->assertEquals($express['status'], 1);

        $express = $this->getExpressService()->orderExpress($express['id'], $user['id']);

        $this->assertEquals($express['status'], 2);

        $this->getExpressService()->orderExpress($express['id'], $user['id']);
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
        $user = self::$biz['user'];

        $express = $this->getExpressService()->createExpress($expressText);
    
        $this->assertEquals($express['status'], 1);

        $this->getExpressService()->orderExpress($express['id'], $user['id']);
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

        $this->assertEquals($express['status'], 1);

        $user2 = $this->createUser('test_user2');
        $currentUser2 = new CurrentUser($user2);
        self::$biz['user'] = $currentUser2;

        $express = $this->getExpressService()->orderExpress($express['id'], $user2['id']);

        $this->assertEquals($express['status'], 2);

        $express = $this->getExpressService()->confirmMyReceiveExpress($express['id']);

        $this->assertEquals($express['status'], 3);

        self::$biz['user'] = $currentUser1;

        $express = $this->getExpressService()->confirmMyPublishExpress($express['id']);
        $this->assertEquals($express['status'], 4);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function testConfirmMyExpressWithOneUser()
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

        $this->assertEquals($express['status'], 1);

        $user2 = $this->createUser('test_user2');
        $currentUser2 = new CurrentUser($user2);
        self::$biz['user'] = $currentUser2;

        $express = $this->getExpressService()->orderExpress($express['id'], $user2['id']);

        $this->assertEquals($express['status'], 2);

        self::$biz['user'] = $currentUser1;

        $express = $this->getExpressService()->confirmMyReceiveExpress($express['id']);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function testConfirmMyExpressWithOtherUser()
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

        $this->assertEquals($express['status'], 1);

        $user2 = $this->createUser('test_user2');
        $currentUser2 = new CurrentUser($user2);
        self::$biz['user'] = $currentUser2;

        $express = $this->getExpressService()->orderExpress($express['id'], $user2['id']);

        $this->assertEquals($express['status'], 2);

        $express = $this->getExpressService()->confirmMyReceiveExpress($express['id']);

        $this->assertEquals($express['status'], 3);

        $express = $this->getExpressService()->confirmMyPublishExpress($express['id']);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function testConfirmMyExpressWithStatus()
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

        $this->assertEquals($express['status'], 1);

        $user2 = $this->createUser('test_user2');
        $currentUser2 = new CurrentUser($user2);
        self::$biz['user'] = $currentUser2;

        $this->assertEquals($express['status'], 1);

        $express = $this->getExpressService()->confirmMyReceiveExpress($express['id']);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function testConfirmMyExpressWithOtherStatus()
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

        $this->assertEquals($express['status'], 1);

        $user2 = $this->createUser('test_user2');
        $currentUser2 = new CurrentUser($user2);
        self::$biz['user'] = $currentUser2;

        $this->assertEquals($express['status'], 1);

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
