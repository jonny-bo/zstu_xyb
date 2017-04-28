<?php
namespace AppBundle\Twig;

use Biz\Common\ConvertIpToolkit;

class HelperExtension extends \Twig_Extension
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
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
            return date('m-d H:i:s', $time);
        }

        return date('Y-n-d H:i:s', $time);
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
}
