<?php

namespace Biz\System\Service\Impl;

use Biz\System\Logger;
use Biz\System\Service\LogService;
use Codeages\Biz\Framework\Service\BaseService;
use AppBundle\Security\CurrentUser;

class LogServiceImpl extends BaseService implements LogService
{
    public function info($module, $moduleId, $operation, $message, array $oldData = array(), array $newData = array())
    {
        return $this->createLog('info', $module, $moduleId, $operation, $message, $oldData, $newData);
    }

    public function warning($module, $moduleId, $operation, $message, array $oldData = array(), array $newData = array())
    {
        return $this->createLog('warning', $module, $moduleId, $operation, $message, $oldData, $newData);
    }

    public function danger($module, $moduleId, $operation, $message, array $oldData = array(), array $newData = array())
    {
        return $this->createLog('danger', $module, $moduleId, $operation, $message, $oldData, $newData);
    }

    public function error($module, $moduleId, $operation, $message, array $oldData = array(), array $newData = array())
    {
        return $this->createLog('error', $module, $moduleId, $operation, $message, $oldData, $newData);
    }

    public function searchLogsCount($fields)
    {
        return $this->getLogDao()->count($fields);
    }

    public function getLogById($logId)
    {
        return $this->getLogDao()->get($logId);
    }

    public function searchLogs($conditions, $orderbys, $start, $limit)
    {
        $conditions = $this->prepareSearchConditions($conditions);
        return $this->getLogDao()->search($conditions, $orderbys, $start, $limit);
    }

    protected function prepareSearchConditions($conditions)
    {
        if (!empty($conditions['start_time']) && !empty($conditions['end_time'])) {
            $conditions['start_time'] = strtotime($conditions['start_time']);
            $conditions['end_time'] = strtotime($conditions['end_time']);
        } else {
            unset($conditions['start_time']);
            unset($conditions['end_time']);
        }

        return $conditions;
    }

    protected function prepareCreateConditions($oldData, $newData)
    {
        $currentOldData = array();
        $currentNewData = array();
        if (isset($oldData['updated_time'])) {
            unset($oldData['updated_time']);
        }
        if (isset($newData['updated_time'])) {
            unset($newData['updated_time']);
        }
        foreach ($oldData as $key => $value) {
            if ($value !== $newData[$key]) {
                $currentOldData = array_merge($currentOldData, array($key => $value));
                $currentNewData = array_merge($currentNewData, array($key => $newData[$key]));
            }
        }

        return array(
            'currentOldData' => empty($currentOldData) ? '' : json_encode($currentOldData),
            'currentNewData' => empty($currentNewData) ? '' : json_encode($currentNewData)
        );
    }

    protected function createLog($level, $module, $moduleId, $operation, $message, array $oldData = array(), array $newData = array())
    {
        $data = $this->prepareCreateConditions($oldData, $newData);

        $isExist = strpos($operation, 'update');

        if (($isExist) && empty($data['currentOldData'])) {
            return null;
        } else {
            return $this->getLogDao()->create(array(
                'username'     => $this->biz['user']->getUsername(),
                'module'       => Logger::getModule($module),
                'module_id'    => $moduleId,
                'operation'    => $operation,
                'message'      => $message,
                'old_data'     => $data['currentOldData'],
                'new_data'     => $data['currentNewData'],
                'level'        => $level
            ));
        }
    }

    protected function getLogDao()
    {
        return $this->biz->dao('System:LogDao');
    }
}
