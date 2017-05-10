<?php

namespace Biz\System\Service;

interface SettingService
{
    public function set($name, $value);

    public function get($name);

    public function delete($name);

    public function setting(array $settings);
}
