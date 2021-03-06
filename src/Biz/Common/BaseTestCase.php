<?php

namespace Biz\Common;

use Codeages\Biz\Framework\UnitTests\BaseTestCase as ParentTestCase;
use AppBundle\Security\CurrentUser;

class BaseTestCase extends ParentTestCase
{
    protected function removeEvent($eventName)
    {
        $biz = self::$biz;
        $listeners = $biz['dispatcher']->getListeners($eventName);
        foreach ($listeners as $listener) {
            $biz['dispatcher']->removeListener($eventName, $listener);
        }
    }

    public function setUp()
    {
        parent::setUp();
        $this->initUser();
    }

    protected function initUser()
    {
        $user = $this->createUser('test_user');
        
        $currentUser = new CurrentUser($user);

        self::$biz['user'] = $currentUser;

        $this->getUserService()->changePayPassword($user['id'], '123456');
    }

    protected function createUser($username, $roles = array('ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'))
    {
        $user = array(
            'username' => $username,
            'nickname' => "test",
            "phone"    => '13582654789',
            "email"    => "{$username}@test.com",
            'password' => '12345',
            'roles'    => $roles,
            'coin' => 100
        );
        $user = $this->getUserService()->register($user);

        return $user;
    }

    protected function getUserService()
    {
        return self::$biz->service('User:UserService');
    }
    /**
     * [exmaple]
     * array('user.service' => array('getUserId' => array('id' => 1, )))
     */

    protected function mockService($mockSrevices)
    {
        $biz = self::$biz;

        $mockObjects = array();
        foreach ($mockSrevices as $serviceName => $expecteds) {
            list($module, $name) = explode(':', $serviceName);
            $class = "Biz\\{$module}\\Service\\Impl\\{$name}Impl";
            $mock = $this->getMockBuilder($class)
                ->disableOriginalConstructor()
                ->getMock();

            foreach ($expecteds as $method => $return) {
                $mock->expects($this->any())->method($method)->will($this->returnValue($return));
            }
            
            $mockObjects[$class] = $mock;
        }

        unset($biz['autoload.object_maker.service']);
        $biz['autoload.object_maker.service'] = function ($biz) use ($mockObjects) {
            return function ($namespace, $name) use ($biz, $mockObjects) {
                $class = "{$namespace}\\Service\\Impl\\{$name}Impl";
                if (array_key_exists($class, $mockObjects)) {
                    return $mockObjects[$class];
                } else {
                    return new $class($biz);
                }
            };
        };
    }
}
