<?php

namespace Biz\Express\Service;

interface ExpressApplyService
{
    public function getExpressApply($expressApplyId);

    public function searchExpressApply($conditions, $orderBy, $start, $limit);

    public function searchExpressApplyCount($conditions);

    public function createExpressApply($fields);

    public function updateExpressApply($expressApplyId, $fields);

    public function deleteExpressApply($expressApplyId);

    public function findExpressApplyByExpressId($expressId);

    public function apply($expressId, $userId);

    public function auth($expressId, $userId);
}
