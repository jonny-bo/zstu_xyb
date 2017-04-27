<?php

namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('smart_time', array($this, 'smarttimeFilter')),
            new \Twig_SimpleFilter('deadline_time', array($this, 'deadlineTimeFilter'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('format_ip', array($this, 'formatIp')),
            new \Twig_SimpleFilter('json_decode', array($this, 'jsonDecodeFilter')),
            new \Twig_SimpleFilter('convert_to_cn', array($this, 'convertToCnFilter')),
            new \Twig_SimpleFilter('array_merge', array($this, 'arrayMerge')),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('parameter', array($this, 'getParameter')),
            new \Twig_SimpleFunction('my_router_params', array($this, 'routerParams')),
            new \Twig_SimpleFunction('orderby_url', array($this, 'getOrderbyUrl')),
            new \Twig_SimpleFunction('get_sort_status', array($this, 'getSortStatus')),
            new \Twig_SimpleFunction('ip_to_array', array($this, 'explodeIP'))
        );
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

        return date('Y-m-d', $time);
    }

    public function deadlineTimeFilter($time)
    {
        $diff = $time - time();

        if ($diff < 2952000 && $diff > 86400) {
            $rest = '还剩'.round($diff / 86400).'天';
            return '<p class="text-warning">'.$rest.'</p>';
        }

        if ($diff < 86400 && $diff > 0) {
            return '<p class="text-warning">还剩1天</p>';
        }

        if ($diff < 0) {
            return '<p class="text-danger">已到期</p>';
        }

        return date('Y-m-d', $time);
    }

    public function getParameter($name, $default = null)
    {
        if (!$this->container->hasParameter($name)) {
            return $default;
        }

        return $this->container->getParameter($name);
    }

    public function routerParams($name)
    {
        $request = $this->container->get('request');
        return $request->query->get($name);
    }

    public function getOrderbyUrl($orderbyName)
    {
        $request = $this->container->get('request');
        $basePath = $request->getPathInfo();
        $query = $request->query->all();
        if (!isset($query['orderby']) || empty($query['orderby'])) {
            $query['orderby'] = $orderbyName.' asc';
        } else {
            if ($query['orderby'] == $orderbyName.' desc') {
                $query['orderby'] = $orderbyName.' asc';
            } else {
                $query['orderby'] = $orderbyName.' desc';
            }
        }

        return $this->buildUrl($basePath, $query);
    }

    public function getSortStatus($orderbyName)
    {
        $query = $this->container->get('request')->query->all();

        if (!isset($query['orderby']) || empty($query['orderby'])) {
            return ' fa-sort';
        }
        if ($query['orderby'] == $orderbyName.' desc') {
            return 'fa-sort-desc';
        } else if ($query['orderby'] == $orderbyName.' asc') {
            return 'fa-sort-asc';
        } else {
            return ' fa-sort';
        }
    }

    public function buildUrl($path, $query)
    {
        return $path.'?'.http_build_query($query);
    }

    public function explodeIP($ipStr)
    {
        return explode('|', $ipStr);
    }

    public function formatIp($ipStr)
    {
        $ips = explode(',', $ipStr);
        if (empty($ips)) {
            return '';
        } else {
            $ipCount = count($ips);
            if ($ipCount == 1) {
                return $ipStr;
            }
            return $ips[0]." ( {$ipCount} )";
        }
    }

    public function getName()
    {
        return 'app_extension';
    }

    public function jsonDecodeFilter($data)
    {
        $dataArray = json_decode($data, true);
        
        return json_encode($dataArray, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    }

    public function convertToCnFilter($level)
    {
        if ($level == 'info') {
            return '提示';
        } else if ($level == 'warning') {
            return '警告';
        } else if ($level == 'danger') {
            return '错误';
        } else {
            return $level;
        }
    }

    public function arrayMerge($text, $content)
    {
        $array = array_merge($text, $content);
        return $array;
    }
}
