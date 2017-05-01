<?php

namespace Biz\System\Service;

interface LogService
{
    public function info($module, $moduleId, $operation, $message, array $oldData = array(), array $newData = array());

    public function warning($module, $moduleId, $operation, $message, array $oldData = array(), array $newData = array());

    public function danger($module, $moduleId, $operation, $message, array $oldData = array(), array $newData = array());

    public function searchLogsCount($fields);

    public function getLogById($logID);

    public function searchLogs($fields, $orderbys, $start, $limit);
}
