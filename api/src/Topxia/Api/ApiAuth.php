<?php

namespace Topxia\Api;

use AppBundle\Security\CurrentUser;
use Symfony\Component\HttpFoundation\Request;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;

class ApiAuth
{
    private $whilelist;
    private $biz;

    public function __construct($whilelist, $biz)
    {
        $this->whilelist = $whilelist;
        $this->biz = $biz;
    }

    public function auth(Request $request)
    {
        $token = $request->headers->get('X-Auth-Token');

        $method = strtolower($request->headers->get('X-Auth-Method'));

        if ($method == 'keysign') {
            $this->setCurrentUser(array(
                'id'        => 0,
                'nickname'  => '游客',
                'currentIp' => $request->getClientIp(),
                'roles'     => array()
            ));

            $decoded = $this->decodeKeysign($token);
        } else {
            $whilelist = isset($this->whilelist[$request->getMethod()]) ? $this->whilelist[$request->getMethod()] : array();

            $path = rtrim($request->getPathInfo(), '/');

            $inWhiteList = 0;

            foreach ($whilelist as $pattern) {
                if (preg_match($pattern, $path)) {
                    $inWhiteList = 1;
                    break;
                }
            }

            if (!$inWhiteList && empty($token)) {
                throw new RuntimeException('API Token不存在！');
            }

            $token = $this->getUserService()->getToken('mobile_login', $token);


            if (!$inWhiteList && empty($token['user_id'])) {
                throw new RuntimeException('API Token不正确！');
            }

            $user = $this->getUserService()->getUser($token['user_id']);

            if (!$inWhiteList && empty($user)) {
                throw new RuntimeException('登录用户不存在！');
            }

            if ($user) {
                $user['login_ip'] = $request->getClientIp();
            }

            $this->setCurrentUser($user);
        }
    }

    public function decodeKeysign($token)
    {
        $token = explode(':', $token);

        if (count($token) != 3) {
            throw new RuntimeException('API Token格式不正确！');
        }

        list($accessKey, $policy, $sign) = $token;

        if (empty($accessKey) || empty($policy) || empty($sign)) {
            throw new RuntimeException('API Token不正确！');
        }

        $settings = $this->getSettingService()->get('storage', array());

        if (empty($settings['cloud_access_key']) || empty($settings['cloud_secret_key'])) {
            throw new RuntimeException("系统尚未配置AccessKey/SecretKey");
        }

        if ($accessKey != $settings['cloud_access_key']) {
            throw new RuntimeException("AccessKey不正确！");
        }

        $expectedSign = $this->encodeBase64(hash_hmac('sha1', $policy, $settings['cloud_secret_key'], true));

        if ($sign != $expectedSign) {
            throw new RuntimeException("API Token 签名不正确！");
        }

        $policy = json_decode($this->decodeBase64($policy), true);

        if (empty($policy)) {
            throw new RuntimeException("API Token 解析失败！");
        }

        if (time() > $policy['deadline']) {
            throw new RuntimeException(sprintf("API Token 已过期！(%s)", date('Y-m-d H:i:s')));
        }

        return $policy;
    }

    public function encodeKeysign($request, $role = 'guest', $lifetime = 600)
    {
        $settings = $this->getSettingService()->get('storage', array());

        $policy = array(
            'method'   => $request->getMethod(),
            'uri'      => $request->getRequestUri(),
            'role'     => $role,
            'deadline' => time() + $lifetime
        );

        $encoded = $this->encodeBase64(json_encode($policy));

        $sign = hash_hmac('sha1', $encoded, $settings['cloud_secret_key'], true);

        return $settings['cloud_access_key'].':'.$encoded.':'.$this->encodeBase64($sign);
    }

    private function getSettingService()
    {
        return $this->biz->service('System:SettingService');
    }

    private function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    private function encodeBase64($string)
    {
        $find    = array('+', '/');
        $replace = array('-', '_');

        return str_replace($find, $replace, base64_encode($string));
    }

    private function decodeBase64($string)
    {
        $find    = array('-', '_');
        $replace = array('+', '/');

        return base64_decode(str_replace($find, $replace, $string));
    }

    private function setCurrentUser($user)
    {
        $currentUser = new CurrentUser($user);
        $this->biz['user'] = $currentUser;
    }
}
