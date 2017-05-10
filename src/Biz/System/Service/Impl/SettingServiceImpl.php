<?php

namespace Biz\System\Service\Impl;

use Biz\Common\BaseService;
use Biz\System\Service\SettingService;

class SettingServiceImpl extends BaseService implements SettingService
{
    public function set($name, $value)
    {
        $this->delete($name);

        $setting = array(
            'name'  => $name,
            'value' => $value
        );

        $this->getSettingDao()->create($setting);
    }

    public function get($name)
    {
        return $this->getSettingDao()->getByName($name);
    }

    public function delete($name)
    {
        $setting = $this->get($name);

        if ($setting) {
            $this->getSettingDao()->delete($setting['id']);
        }
    }

    public function setting(array $settings)
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value);
        }
    }

    protected function getSettingDao()
    {
        return $this->biz->dao('System:SettingDao');
    }
}
