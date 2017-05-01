<?php
namespace Biz\System;

/**
* logger的模块 以及操作
*/
class Logger
{
    /**
     * [$USER 用户]
     * @var string
     */
    const USER = 'user';
    /**
     * [$IDC 数据中心]
     * @var string
     */
    const IDC = 'idc';
    /**
     * [$HOST 服务器]
     * @var string
     */
    const HOST = 'host';
    /**
     * [$IP IP]
     * @var string
     */
    const IP = 'ip';
    /**
     * [$tag 标签]
     * @var string
     */
    const TAG = 'tag';
    /**
     * [$tagGroup 标签组]
     * @var string
     */
    const TAGGROUP = 'tagGroup';
    
    public static function getModule($module)
    {
        $modules = array_keys(self::systemModuleConfig());

        if (in_array($module, $modules)) {
            return $module;
        }
        return $module;
    }

    /**
     * 模块(module)  -> 操作(action)
     */
    public static function systemModuleConfig()
    {
        return array(
            self::USER        => array(
                'create' => '新增',
                'update' => '修改'
            ),
            self::IDC         =>array(
                'create' => '新增',
                'update' => '修改'
            ),
            self::HOST        =>array(
                'create' => '新增',
                'update' => '修改'
            ),
            self::IP       =>array(
                'create' => '新增',
                'update' => '修改'
            ),
            self::TAG         => array(
                'create' => '新增',
                'update' => '修改',
                'delete' => '删除'
            ),
            self::TAGGROUP    => array(
                'create' => '新增',
                'update' => '修改',
                'delete' => '删除'
            )
        );
    }

    public static function getLogModuleDict()
    {
        return array(
            self::USER    => '用户',
            self::IDC     => '数据中心',
            self::HOST    => '服务器',
            self::IP      => 'IP',
            self::TAG     => '标签',
            self::TAGGROUP => '标签组'
        );
    }
}
