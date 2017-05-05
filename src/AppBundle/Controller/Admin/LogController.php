<?php
namespace AppBundle\Controller\Admin;

use AppBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Common\Paginator;

class LogController extends BaseController
{
    const START = 0;
    const LIMIT = 10;

    public function showModalAction(Request $request)
    {
        $fields = $request->query->all();
        $recentLogs = $this->getLogService()->searchLogs(
            $fields,
            array('created_time' => 'DESC'),
            self::START,
            self::LIMIT
        );
        return $this->createJsonResponse(array('logs' => $recentLogs));
    }

    public function showInfoModalAction($logId)
    {
        $log = $this->getLogService()->getLogById($logId);

        return $this->render('AppBundle:admin/system:log-info-modal.html.twig', array('log' => $log));
    }

    public function showListAction(Request $request)
    {
        $fields = $request->query->all();

        $conditions = $this->prepareSearchConditions($fields);

        $logsCount = $this->getLogService()->searchLogsCount($conditions);
        $paginator = new Paginator(
            $request,
            $logsCount,
            parent::DEFAULT_PAGE_COUNT
        );
        
        $logs = $this->getLogService()->searchLogs(
            $conditions,
            array('created_time' => 'DESC'),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        return $this->render('AppBundle:admin/system:logs.html.twig', array(
            'logs'       => $logs,
            'paginator'  => $paginator,
        ));
    }

    protected function prepareSearchConditions($fields)
    {
        $conditions = array(
            'level'      => '',
            'module'     => '',
            'username'   => '',
            'start_time' => '',
            'end_time'   => ''
        );

        if (!empty($fields)) {
            if (!empty($fields['username'])) {
                $fields['username'] = '%' .trim($fields['username']). '%';
            }
            $conditions = array_merge($conditions, $fields);
        }
        return $conditions;
    }

    protected function getLogService()
    {
        return $this->biz->service('System:LogService');
    }
}
