<?php
namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\BaseController;
use AppBundle\Common\Paginator;
use Biz\Common\StringToolkit;
use Symfony\Component\Finder\Finder;

class SettingController extends BaseController
{
    public function siteAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $settings = $request->request->all();
            $this->getSettingService()->setting($settings);

            return $this->redirect($this->generateUrl('admin_setting_site'));
        }

        return $this->render('AppBundle:admin/system:index.html.twig');
    }

    public function ipBlacklistAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();

            $purifiedBlackIps = trim(str_replace(array("\r\n", "\n", "\r"), " ", $data['blackListIps']));

            if (empty($purifiedBlackIps)) {
                $this->getSettingService()->delete('blacklist_ip');

                $blackListIps['ips'] = array();
            } else {
                $blackListIps['ips'] = array_filter(explode(' ', $purifiedBlackIps));

                $this->getSettingService()->set('blacklist_ip', json_encode($blackListIps));
            }

            $this->getLogService()->info('system', 0, 'update_ip_blacklist', '更新IP黑名单', array(), $blackListIps);

            $this->setFlashMessage('success', '保存成功！');

            return $this->redirect($this->generateUrl('admin_system_ip_blacklist'));
        }

        $blackListIps = $this->getSettingService()->get('blacklist_ip');

        if (!empty($blackListIps)) {
            $blackListIps = json_decode($blackListIps, true);
            $default['ips'] = join("\n", $blackListIps['ips']);
            $blackListIps = array_merge($blackListIps, $default);
        } else {
            $blackListIps = array();
        }

        return $this->render('AppBundle:admin/system:ip-blicklist.html.twig', array(
            'blackListIps' => $blackListIps
        ));
    }

    public function statusAction()
    {
        return $this->render('AppBundle:admin/system:status.html.twig', array(
            'env'             => $this->getSystemStatus(),
            'systemDiskUsage' => $this->getSystemDiskUsage()
        ));
    }

    public function checkDirAction()
    {
        $paths = array(
            '/'           => array('depth' => '<1', 'dir' => true),
            'app'         => array('depth' => '<1', 'dir' => true),
            'src'         => array(),
            'plugins'     => array(),
            'api'         => array(),
            'vendor'      => array('depth' => '<1', 'dir' => true),
            'var' => array('depth' => '<1', 'dir' => true),
            'web'         => array('depth' => '<1', 'dir' => true)
        );

        $errorPaths = array();

        if (PHP_OS !== 'WINNT') {
            foreach ($paths as $folder => $opts) {
                $finder = new Finder();

                if (!empty($opts['depth'])) {
                    $finder->depth($opts['depth']);
                }

                if (!empty($opts['dir'])) {
                    $finder->directories();
                }

                try {
                    $finder->in($this->container->getParameter('kernel.root_dir').'/../'.$folder);

                    foreach ($finder as $fileInfo) {
                        $relaPath = $fileInfo->getRealPath();

                        if (!(is_writable($relaPath) && is_readable($relaPath))) {
                            $errorPaths[] = $relaPath;
                        }
                    }
                } catch (\Exception $e) {
                    $errorPaths[] = $e->getMessage();
                }
            }
        }

        return $this->render('AppBundle::admin/system/report/dir-permission.html.twig', array(
            'errorPaths' => $errorPaths
        ));
    }

    protected function getSystemStatus()
    {
        $env                        = array();
        $env['os']                  = PHP_OS;
        $env['phpVersion']          = PHP_VERSION;
        $env['phpVersionOk']        = version_compare(PHP_VERSION, '5.3.0') >= 0;
        $env['user']                = getenv('USER');
        $env['pdoMysqlOk']          = extension_loaded('pdo_mysql');
        $env['uploadMaxFilesize']   = ini_get('upload_max_filesize');
        $env['uploadMaxFilesizeOk'] = intval($env['uploadMaxFilesize']) >= 2;
        $env['postMaxsize']         = ini_get('post_max_size');
        $env['postMaxsizeOk']       = intval($env['postMaxsize']) >= 8;
        $env['maxExecutionTime']    = ini_get('max_execution_time');
        $env['maxExecutionTimeOk']  = ini_get('max_execution_time') >= 30;
        $env['mbstringOk']          = extension_loaded('mbstring');
        $env['gdOk']                = extension_loaded('gd');
        $env['curlOk']              = extension_loaded('curl');
        $env['safemode']            = ini_get('safe_mode');

        return $env;
    }

    private function getSystemDiskUsage()
    {
        $logsDir = $this->biz['log_directory'];
        $logs    = array(
            'name'  => '/var/logs',
            'dir'   => $logsDir,
            'title' => '用户在站点进行操作的日志存放目录'
        );

        $webFileDir =$this->biz['upload.public_directory'];

        $webFiles   = array(
            'name'  => substr($webFileDir, strrpos($webFileDir, '/')),
            'dir'   => $webFileDir,
            'title' => '用户在站点上传图片的存放目录'
        );

        $materialDir = $this->biz['disk.local_directory'];
        $material    = array(
            'name'  => substr($materialDir, strrpos($materialDir, '/')),
            'dir'   => $materialDir,
            'title' => '用户资料库中资源的所在目录'
        );

        return array_map(function ($array) {
            $name  = $array['name'];
            $dir   = $array['dir'];
            $total = disk_total_space($dir);
            $free  = disk_free_space($dir);
            $rate  = (string) number_format($free / $total, 2) * 100 .'%';
            return array(
                'name'  => $name,
                'rate'  => $rate,
                'free'  => StringToolkit::printMem($free),
                'total' => StringToolkit::printMem($total),
                'title' => $array['title']
            );
        }, array($logs, $webFiles, $material));
    }

    public function magicAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $setting = $request->request->get('setting', '{}');
            $setting = json_decode($setting, true);
            if (empty($setting)) {
                $setting = array('disable_web_crontab' => 0);
            }
            $this->getSettingService()->set('magic', json_encode($setting));
            $this->getLogService()->info('system', 0, 'update_settings', '更新Magic设置', $setting);
            $this->setFlashMessage('success', '设置已保存！');
        }

        $setting = $this->getSettingService()->get('magic');

        return $this->render('AppBundle:admin/system:magic.html.twig', array(
            'setting' => $setting
        ));
    }


    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getSettingService()
    {
        return $this->biz->service('System:SettingService');
    }
}
