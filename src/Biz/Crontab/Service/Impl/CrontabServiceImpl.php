<?php

namespace Biz\Crontab\Service\Impl;

use Biz\Common\BaseService;
use Biz\Crontab\Service\CrontabService;
use Biz\Common\ArrayToolkit;
use Symfony\Component\Yaml\Yaml;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\AccessDeniedException;

class CrontabServiceImpl extends BaseService implements CrontabService
{
    public function getJob($id)
    {
        return $this->getJobDao()->get($id);
    }

    public function searchJobs($conditions, $sort, $start, $limit)
    {
        $conditions = $this->prepareSearchConditions($conditions);

        switch ($sort) {
            case 'created':
                $sort = array('created_time' => 'DESC');
                break;
            case 'createdByAsc':
                $sort = array('created_time' => 'ASC');
                break;
            case 'nextExcutedTime':
                $sort = array('next_excuted_time' => 'DESC');
                break;
            default:
                throw new InvalidArgumentException('参数sort不正确！');
                break;
        }

        return $this->getJobDao()->search($conditions, $sort, $start, $limit);
    }

    public function searchJobsCount($conditions)
    {
        $conditions = $this->prepareSearchConditions($conditions);
        return $this->getJobDao()->count($conditions);
    }

    public function createJob($job)
    {
        $user = $this->getCurrentUser();

        if (!ArrayToolKit::requireds($job, array('nextExcutedTime'))) {
            throw new InvalidArgumentException('缺少 nextExcutedTime 字段,创建job失败');
        }

        $job['user_id'] = $user['id'];

        $job = $this->getJobDao()->create($job);

        $this->refreshNextExecutedTime();

        return $job;
    }

    public function executeJob($id)
    {
        $job = array();
        // 开始执行job的时候，设置next_executed_time为0，防止更多的请求进来执行
        $this->setNextExcutedTime(0);
        $result = $this->syncronizeUpdateExecutingStatus($id);
        if (!$result) {
            return;
        }
        $this->getJobDao()->db()->beginTransaction();
        try {
            // 加锁
            $job = $this->getJob($id, true);

            $jobInstance = new $job['job_class']($this->biz);
            if (!empty($job['target_type'])) {
                $job['job_params']['target_type'] = $job['target_type'];
            }

            if (!empty($job['target_id'])) {
                $job['job_params']['target_id'] = $job['target_id'];
            }

            $jobInstance->execute($job['job_params']);
        } catch (\Exception $e) {
            $message = $e->getMessage();
           // $this->getJobDao()->updateJob($job['id'], array('executing' => 0));
            $this->getLogService()->error('crontab', $id, 'execute', "执行任务(#{$job['id']})失败: {$message}", $job);
        }

        $this->afterJobExecute($job);
        $this->getJobDao()->db()->commit();
        $this->refreshNextExecutedTime();
    }

    private function syncronizeUpdateExecutingStatus($id)
    {
        // 并发的时候，一旦有多个请求进来执行同个任务，阻止第２个起的请求执行任务
        $lockName = "job_{$id}";
        $lock = $this->getLock();
        $lock->get($lockName, 10);

        $job = $this->getJob($id);
        if (empty($job) || $job['executing']) {
            $this->getLogService()->error('crontab', $id, 'execute', "任务(#{$job['id']})已经完成或者在执行");
            $lock->release($lockName);
            return false;
        }

        $this->getJobDao()->update($job['id'], array('executing' => 1));
        $lock->release($lockName);
        return true;
    }

    protected function afterJobExecute($job)
    {
        if ($job['cycle'] == 'once') {
            $this->getJobDao()->delete($job['id']);
        }

        if ($job['cycle'] == 'everyhour') {
            $time = time();
            $this->getJobDao()->update($job['id'], array(
                'executing'          => '0',
                'latest_executed_time' => $time,
                'next_excuted_time'    => strtotime('+1 hours', $time)
            ));
        }

        if ($job['cycle'] == 'everyday') {
            $time = time();
            $this->getJobDao()->updateJob($job['id'], array(
                'executing'          => '0',
                'latest_executed_time' => $time,
                'next_excuted_time'    => strtotime(date('Y-m-d', strtotime('+1 day', $time)).' '.$job['cycle_time'])
            ));
        }
    }

    public function deleteJob($id)
    {
        $deleted = $this->getJobDao()->delete($id);
        $this->refreshNextExecutedTime();
        return $deleted;
    }

    public function deleteJobs($targetId, $targetType)
    {
        $deleted = $this->getJobDao()->deleteByTargetIdAndTargetType($targetId, $targetType);
        $this->refreshNextExecutedTime();
        return $deleted;
    }

    public function scheduleJobs()
    {
        $conditions = array(
            'executing'       => 0,
            'nextExcutedTime' => time()
        );
        $job = $this->getJobDao()->search($conditions, array('next_excuted_time' => 'ASC'), 0, 1);

        if (!empty($job)) {
            $job = $job[0];
            $this->getLogService()->info('crontab', $job['id'], 'job_start', "定时任务(#{$job['id']})开始执行！", $job);
            $this->executeJob($job['id']);
            $newJob = $this->getJob($job['id']);
            $this->getLogService()->info('crontab', $job['id'], 'job_end', "定时任务(#{$job['id']})执行结束！", $job, $newJob);
        }
    }

    protected function refreshNextExecutedTime()
    {
        $conditions = array(
            'executing' => 0
        );

        $job = $this->getJobDao()->search($conditions, array('next_excuted_time' => 'ASC'), 0, 1);

        if (empty($job)) {
            $this->setNextExcutedTime(0);
        } else {
            $this->setNextExcutedTime($job[0]['next_excuted_time']);
        }
    }

    public function getNextExcutedTime()
    {
        $filePath = __DIR__.'/../../../../../app/config/crontab_config.yml';
        $yaml     = new Yaml();

        if (!file_exists($filePath)) {
            $content = $yaml->dump(array('crontab_next_executed_time' => 0));
            $fh      = fopen($filePath, "w");
            fwrite($fh, $content);
            fclose($fh);
        }

        $fileContent = file_get_contents($filePath);
        $config      = $yaml->parse($fileContent);

        return $config['crontab_next_executed_time'];
    }

    public function setNextExcutedTime($nextExcutedTime)
    {
        $filePath = __DIR__.'/../../../../../app/config/crontab_config.yml';
        $yaml     = new Yaml();
        $content  = $yaml->dump(array('crontab_next_executed_time' => $nextExcutedTime));
        $fh       = fopen($filePath, "w");
        fwrite($fh, $content);
        fclose($fh);
    }

    public function findJobByTargetTypeAndTargetId($targetType, $targetId)
    {
        return $this->getJobDao()->findByTargetTypeAndTargetId($targetType, $targetId);
    }

    public function findJobByNameAndTargetTypeAndTargetId($jobName, $targetType, $targetId)
    {
        return $this->getJobDao()->findByNameAndTargetTypeAndTargetId($jobName, $targetType, $targetId);
    }

    public function updateJob($id, $fields)
    {
        return $this->getJobDao()->update($id, $fields);
    }

    protected function prepareSearchConditions($conditions)
    {
        if (!empty($conditions['nextExcutedStartTime']) && !empty($conditions['nextExcutedEndTime'])) {
            $conditions['nextExcutedStartTime'] = strtotime($conditions['nextExcutedStartTime']);
            $conditions['nextExcutedEndTime']   = strtotime($conditions['nextExcutedEndTime']);
        } else {
            unset($conditions['nextExcutedStartTime']);
            unset($conditions['nextExcutedEndTime']);
        }

        if (empty($conditions['cycle'])) {
            unset($conditions['cycle']);
        }

        if (empty($conditions['name'])) {
            unset($conditions['name']);
        } else {
            $conditions['name'] = '%'.$conditions['name'].'%';
        }

        return $conditions;
    }

    protected function getJobDao()
    {
        return $this->biz->dao('Crontab:JobDao');
    }

    protected function getLogService()
    {
        return $this->biz->service('System:LogService');
    }

    protected function getSettingService()
    {
        return $this->biz->service('System:SettingService');
    }
}
