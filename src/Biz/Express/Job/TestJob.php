<?php

namespace Biz\Express\Job;

use Biz\Crontab\Service\Job;

class TestJob extends Job
{
    public function execute($params)
    {
        $testId = $params['target_id'];
        $type   = $params['target_type'];

        var_dump($testId);
        var_dump($type);
    }
}
