<?php

namespace Topxia\Api\Filter;

use Codeages\PluginBundle\System\PluginConfigurationManager;
use Topxia\Service\Common\ServiceKernel;
use AppBundle\Security\CurrentUser;

class UserFilter extends BaseFilter implements Filter
{
    //输出前的字段控制
    //查看权限,附带内容可以写在这里

    public function filter(array &$data)
    {
        unset($data['password']);
        unset($data['salt']);

        $data['login_time'] = date('c', $data['login_time']);
        $data['updated_time'] = date('c', $data['updated_time']);
        $data['created_time'] = date('c', $data['created_time']);

        $user = new CurrentUser($this->biz['user']);

        if (!$user->isLogin() || !$user->isAdmin() || ($user['id'] != $data['id'])) {
            unset($data['email']);
            unset($data['point']);
            unset($data['coin']);
            unset($data['login_ip']);
            unset($data['updated_time']);
            unset($data['login_time']);
            unset($data['created_time']);

            return $data;
        }

        return $data;
    }

    public function filters(array &$datas)
    {
        $num = 0;
        $results = array();
        foreach ($datas as $data) {
            $results[$num] = $this->filter($data);
            $num++;
        }
        return $results;
    }

    public function convertAbsoluteUrl($host, $html)
    {
        $html = preg_replace_callback('/src=[\'\"]\/(.*?)[\'\"]/', function ($matches) use ($host) {
            return "src=\"{$host}/{$matches[1]}\"";
        }, $html);

        return $html;
    }
}
