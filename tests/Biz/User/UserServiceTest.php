<?php

namespace Tests\Biz\User;

use Biz\Common\BaseTestCase;

class UserServiceTest extends BaseTestCase
{
    public function testRegister()
    {
        $userText = array(
            'username' => 'test',
            'nickname' => 'test',
            'email'    => 'test@admin.com',
            'password' => 'testuser'
        );

        $user = $this->getUserService()->register($userText);

        $this->assertEquals($user['username'], $userText['username']);
        $this->assertEquals($user['nickname'], $userText['nickname']);
        $this->assertEquals($user['email'], $userText['email']);
    }

    /**
     * @expectedException Biz\Common\Exception\InvalidArgumentException
     * @expectedExceptionCode 0
     */
    public function testRegisterWithMissFields()
    {
        $userText = array(
            'username' => 'test',
            'email'    => 'test@admin.com',
            'password' => 'testuser'
        );

        $this->getUserService()->register($userText);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function testRegisterWithUsernameIsExit()
    {
        $userText = array(
            'username' => 'test',
            'nickname' => 'test',
            'email'    => 'test@admin.com',
            'password' => 'testuser'
        );

        $this->getUserService()->register($userText);

        $userText = array(
            'username' => 'test',
            'nickname' => 'test',
            'email'    => 'test@admin.com',
            'password' => 'testuser'
        );

        $this->getUserService()->register($userText);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function testRegisterWithNickname()
    {
        $userText = array(
            'username' => 'test',
            'nickname' => '昵称过长超过18昵称过长超过18昵称过长超过18昵称过长超过18昵称过长超过18昵称过长超过18',
            'email'    => 'test@admin.com',
            'password' => 'testuser'
        );

        $this->getUserService()->register($userText);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function testRegisterWithEmail()
    {
        $userText = array(
            'username' => 'test',
            'nickname' => 'test',
            'email'    => 'test.com',
            'password' => 'testuser'
        );

        $this->getUserService()->register($userText);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function testRegisterWithEmailIsExit()
    {
        $userText1 = array(
            'username' => 'test1',
            'nickname' => 'test1',
            'email'    => 'test@admin.com',
            'password' => 'testuser'
        );

        $this->getUserService()->register($userText1);

        $userText2 = array(
            'username' => 'test2',
            'nickname' => 'test2',
            'email'    => 'test@admin.com',
            'password' => 'testuser'
        );

        $this->getUserService()->register($userText2);
    }

    /**
     * @expectedException Biz\Common\Exception\UnexpectedValueException
     * @expectedExceptionCode 0
     */
    public function testRegisterWithMobie()
    {
        $userText1 = array(
            'username' => 'test1',
            'nickname' => 'test1',
            'email'    => 'test@admin.com',
            'password' => 'testuser',
            'mobile'   => '11111111'
        );

        $this->getUserService()->register($userText1);
    }

    public function testGetUser()
    {
        $userText = array(
            'username' => 'test',
            'nickname' => 'test',
            'email'    => 'test@admin.com',
            'password' => 'testuser'
        );

        $user = $this->getUserService()->register($userText);

        $found = $this->getUserService()->getUser($user['id']);

        $this->assertEquals($user['id'], $found['id']);
    }

    public function testGetUserByUsername()
    {
        $userText = array(
            'username' => 'test',
            'nickname' => 'test',
            'email'    => 'test@admin.com',
            'password' => 'testuser'
        );

        $user = $this->getUserService()->register($userText);

        $found = $this->getUserService()->getUserByUsername($user['username']);

        $this->assertEquals($user['username'], $found['username']);
    }

    protected function getUserService()
    {
        return self::$biz->service('User:UserService');
    }
}
