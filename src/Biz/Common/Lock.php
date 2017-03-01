<?php
namespace Biz\Common;

use Codeages\Biz\Framework\Context\Biz;

class Lock
{
    protected $connection;

    public function __construct(Biz $biz)
    {
        $this->connection = $biz['db'];
    }

    public function get($lockName, $lockTime)
    {
        $result = $this->connection->fetchAssoc("SELECT GET_LOCK('im_{$lockName}', {$lockTime}) AS getLock");
        return $result['getLock'];
    }

    public function release($lockName)
    {
        $result = $this->connection->fetchAssoc("SELECT RELEASE_LOCK('im_{$lockName}') AS releaseLock");
        return $result['releaseLock'];
    }
}
