<?php
namespace AppBundle\Listener;

use Symfony\Component\HttpFoundation\Request;
use Biz\Common\Exception\AccessDeniedException;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Codeages\Biz\Framework\Context\Biz;

class KernelRequestListener
{
    protected $biz;
    public function __construct($container, Biz $biz)
    {
        $this->container = $container;
        $this->biz = $biz;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $settingService = $this->biz->service('System:SettingService');

        $blacklistIps = $settingService->get('blacklist_ip')['value'];

        if (!empty($blacklistIps)) {
            $blacklistIps = json_decode($blacklistIps, true);
        }

        $clientIp = $request->getClientIp();

        if (isset($blacklistIps['ips'])) {
            if ($this->matchIpConfigList($clientIp, $blacklistIps['ips'])) {
                throw new AccessDeniedException('您的IP已被列入黑名单，访问被拒绝，如有疑问请联系管理员！');
            }
        }
    }

    private function matchIpConfigList($clientIp, $ipConfigList)
    {
        foreach ($ipConfigList as $ipConfigEntry) {
            if ($this->matchIp($clientIp, $ipConfigEntry)) {
                return true;
            }
        }

        return false;
    }

    private function matchIp($clientIp, $ipConfigEntry)
    {
        $ipConfigEntry = trim($ipConfigEntry);

        if (strlen($ipConfigEntry) > 0) {
            $regex = str_replace(".", "\.", $ipConfigEntry);
            $regex = str_replace("*", "\d{1,3}", $regex);
            $regex = "/^" . $regex . "/";

            return preg_match($regex, $clientIp);
        } else {
            return false;
        }
    }
}
