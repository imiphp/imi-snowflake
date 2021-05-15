<?php

namespace Imi\Snowflake;

use Godruoyi\Snowflake\Snowflake;
use Imi\Worker;

class SnowflakeClass extends Snowflake
{
    public function __construct(int $datacenter = null, int $workerid = null)
    {
        if (null === $workerid)
        {
            $workerid = Worker::getWorkerID();
        }
        parent::__construct($datacenter, $workerid);
    }

    public function getWorkerId(): int
    {
        return $this->workerid;
    }

    public function getDatacenter(): int
    {
        return $this->datacenter;
    }
}
