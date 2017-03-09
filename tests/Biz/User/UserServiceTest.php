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

    public function testSearchUsersCount()
    {
        $userText1 = array(
            'username' => 'test1',
            'nickname' => 'test1',
            'email'    => 'test1@admin.com',
            'password' => 'testuser'
        );
        $userText2 = array(
            'username' => 'test2',
            'nickname' => 'test2',
            'email'    => 'test2@admin.com',
            'password' => 'testuser'
        );

        $oldCount = $this->getUserService()->searchUsersCount(array());

        $this->getUserService()->register($userText1);

        $this->getUserService()->register($userText2);

        $result = $this->getUserService()->searchUsersCount(array());

        $this->assertEquals($result, $oldCount+2);

        $result = $this->getUserService()->searchUsersCount(array('username' => 'test2'));

        $this->assertEquals($result, 1);
    }

    public function testSearchUsers()
    {
        $userText1 = array(
            'username' => 'test1',
            'nickname' => 'test1',
            'email'    => 'test1@admin.com',
            'password' => 'testuser'
        );
        $userText2 = array(
            'username' => 'test2',
            'nickname' => 'test2',
            'email'    => 'test2@admin.com',
            'password' => 'testuser'
        );

        $this->getUserService()->register($userText1);

        $this->getUserService()->register($userText2);

        $result = $this->getUserService()->searchUsers(array('username' => 'test1'), array(), 0, 10);

        $this->assertEquals($result[0]['username'], 'test1');

        $result = $this->getUserService()->searchUsers(array(), array(), 0, 10);

        $this->assertEquals(count($result), 3);
    }

    public function testMakeTokenAndGetToken()
    {
        $currentUser = self::$biz['user'];

        $result = $this->getUserService()->makeToken('mobile_login', $currentUser['id']);
        $token  = $this->getUserService()->getToken('mobile_login', $result);

        $this->assertEquals($token['user_id'], $currentUser['id']);
        $this->assertEquals($token['token'], $result);
        $this->assertEquals($token['type'], 'mobile_login');
    }

    public function testSearchTokenCount()
    {
        $this->getUserService()->makeToken('mobile_login', 1);
        $this->getUserService()->makeToken('mobile_login', 2);

        $tokensCount  = $this->getUserService()->searchTokenCount(array());

        $this->assertEquals($tokensCount, 2);

        $tokensCount  = $this->getUserService()->searchTokenCount(array('user_id' => 1));

        $this->assertEquals($tokensCount, 1);
    }

    public function testDeleteToken()
    {
        $currentUser = self::$biz['user'];

        $token = $this->getUserService()->makeToken('mobile_login', $currentUser['id']);

        $this->getUserService()->deleteToken('mobile_login', $token);

        $result = $this->getUserService()->getToken('mobile_login', $token);

        $this->assertEquals($result, null);
    }

    public function testVerifyPassword()
    {
        $userText1 = array(
            'username' => 'test1',
            'nickname' => 'test1',
            'email'    => 'test@admin.com',
            'password' => 'testuser'
        );

        $user   = $this->getUserService()->register($userText1);

        $result = $this->getUserService()->verifyPassword($user['id'], 'testuser');

        $this->assertEquals($result, true);
    }

    public function testMarkLoginInfo()
    {
        $user   = self::$biz['user'];

        $result = $this->getUserService()->markLoginInfo();

        $this->assertEquals($result['login_ip'], $user['login_ip']);
    }

    protected function getUserService()
    {
        return self::$biz->service('User:UserService');
    }
}
