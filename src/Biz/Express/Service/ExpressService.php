<?php

namespace Biz\Express\Service;

interface ExpressService
{
    public function getExpress($expressId);

    public function searchExpress($conditions, $orderBy, $start, $limit);

    public function searchExpressCount($conditions);

    public function createExpress($fields);

    public function updateExpress($expressId, $fields);

    public function deleteExpress($expressId);
}
