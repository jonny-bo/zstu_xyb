<?php
namespace AppBundle\Twig;

use Biz\Common\ConvertIpToolkit;
use Codeages\Biz\Framework\Context\Biz;

class HelperExtension extends \Twig_Extension
{
    protected $container;
    protected $biz;

    public function __construct($container, Biz $biz)
    {
        $this->container = $container;
        $this->biz = $biz;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('select_options', array($this, 'selectOptions'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('radios', array($this, 'radios'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('checkboxs', array($this, 'checkboxs'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('parameter', array($this, 'getParameter')),
            new \Twig_SimpleFunction('form_csrf_token', array($this, 'rendorFormCsrfToken'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('convertIP', array($this, 'getConvertIP')),
            new \Twig_SimpleFunction('file_path', array($this, 'getFilePath')),
            new \Twig_SimpleFunction('get', array($this, 'getSetting')),
            new \Twig_SimpleFunction('crontab_next_executed_time', array($this, 'getNextExecutedTime')),
        );
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('money', [$this, 'formatMoney']),
            new \Twig_SimpleFilter('smart_time', [$this, 'smarttimeFilter']),
        ];
    }

    public function smarttimeFilter($time)
    {
        $diff = time() - $time;

        if ($diff < 0) {
            return '未来';
        }

        if ($diff == 0) {
            return '刚刚';
        }

        if ($diff < 60) {
            return '秒前';
        }

        if ($diff < 3600) {
            return round($diff / 60).'分钟前';
        }

        if ($diff < 86400) {
            return round($diff / 3600).'小时前';
        }

        if ($diff < 2592000) {
            return round($diff / 86400).'天前';
        }

        if ($diff < 31536000) {
            return date('m-d', $time);
        }

        return date('Y-n-d H:i', $time);
    }

    public function getSetting($name, $default = null)
    {
        $names = explode('.', $name);

        $name = array_shift($names);

        if (empty($name)) {
            return $default;
        }

        $value = $this->getSettingService()->get($name);

        if (!isset($value)) {
            return $default;
        }

        if (empty($names)) {
            return $value;
        }

        $result = json_decode($value, true);

        foreach ($names as $name) {
            if (!isset($result[$name])) {
                return $default;
            }

            $result = $result[$name];
        }

        return $result;
    }

    public function getNextExecutedTime()
    {
        return $this->biz->service('Crontab:CrontabService')->getNextExcutedTime();
    }

    public function selectOptions($choices, $selected = null, $empty = null)
    {
        $html = '';

        if (!is_null($empty)) {
            if (is_array($empty)) {
                foreach ($empty as $key => $value) {
                    $html .= "<option value=\"{$key}\">{$value}</option>";
                }
            } else {
                $html .= "<option value=\"0\">{$empty}</option>";
            }
        }

        foreach ($choices as $value => $name) {
            if ($selected == $value) {
                $html .= "<option value=\"{$value}\" selected=\"selected\">{$name}</option>";
            } else {
                $html .= "<option value=\"{$value}\">{$name}</option>";
            }
        }

        return $html;
    }

    public function radios($name, $choices, $checked = null)
    {
        $html = '';

        foreach ($choices as $value => $label) {
            if ($checked == $value) {
                $html .= "<label><input type=\"radio\" name=\"{$name}\" value=\"{$value}\" checked=\"checked\"> {$label}</label>";
            } else {
                $html .= "<label><input type=\"radio\" name=\"{$name}\" value=\"{$value}\"> {$label}</label>";
            }
        }

        return $html;
    }

    public function checkboxs($name, $choices, $checkeds = array())
    {
        $html = '';

        if (!is_array($checkeds)) {
            $checkeds = array($checkeds);
        }

        foreach ($choices as $value => $label) {
            if (in_array($value, $checkeds)) {
                $html .= "<label><input type=\"checkbox\" name=\"{$name}[]\" value=\"{$value}\" checked=\"checked\"> {$label}</label>";
            } else {
                $html .= "<label><input type=\"checkbox\" name=\"{$name}[]\" value=\"{$value}\"> {$label}</label>";
            }
        }

        return $html;
    }

    public function getParameter($name)
    {
        return $this->container->getParameter($name);
    }

    public function rendorFormCsrfToken($id = null)
    {
        if (empty($id)) {
            $id = $this->container->getParameter('app.csrf.token_id.default');
        }

        $token = $this->container->get('security.csrf.token_manager')->getToken($id)->getValue();

        return sprintf('<input type="hidden" name="%s" value="%s">', $this->container->getParameter('app.csrf.token_form_name'), $token);
    }

    public function formatMoney($value)
    {
        $value = (string) $value;
        $value = str_pad($value, 3, '0', STR_PAD_LEFT);

        $integer = substr($value, 0, -2);
        $decimals = substr($value, -2);

        return "{$integer}.{$decimals}";
    }

    public function getName()
    {
        return 'app_helper';
    }

    public function getConvertIP($ip)
    {
        if (!empty($ip)) {
            $location = ConvertIpToolkit::convertIp($ip);

            if ($location === 'INNA') {
                return '未知区域';
            }

            return $location;
        }

        return '';
    }

    public function getFilePath($uri, $default = '', $absolute = false)
    {
        $assets  = $this->container->get('templating.helper.assets');
        $request = $this->container->get('request');

        if (empty($uri)) {
            $url = $assets->getUrl('assets/img/default/'.$default);

            if ($absolute) {
                $url = $request->getSchemeAndHttpHost().$url;
            }

            return $url;
        }

        if (strpos($uri, "http://") !== false) {
            return $uri;
        }

        $uri = $this->parseFileUri($uri);

        if ($uri['access'] == 'public') {
            $url = rtrim($this->biz['upload.public_url_path'], ' /').'/'.$uri['path'];
            $url = ltrim($url, ' /');
            $url = $assets->getUrl($url);

            if ($absolute) {
                $url = $request->getSchemeAndHttpHost().$url;
            }

            // if (!file_exists($url)) {
            //     return $assets->getUrl('assets/img/default/default.jpg');
            // }

            return $url;
        }
    }

    public function parseFileUri($uri)
    {
        return $this->getFileService()->parseFileUri($uri);
    }

    protected function getFileService()
    {
        return $this->biz->service('File:FileService');
    }

    protected function getSettingService()
    {
        return $this->biz->service('System:SettingService');
    }
}
