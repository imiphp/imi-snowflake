<?php

namespace Imi\Snowflake;

use Godruoyi\Snowflake\SequenceResolver;
use Imi\Redis\RedisManager;

class ImiRedisResolver implements SequenceResolver
{
    /**
     * The cache prefix.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Redis 连接池名称.
     *
     * 为 NULL 则使用默认连接池
     *
     * @var string|null
     */
    protected $redisPool;

    const SEQUENCE_LUA = <<<LUA
if(redis.call('exists',KEYS[1])<1 and redis.call('psetex',KEYS[1],ARGV[2],ARGV[1]))
then
    return 0
else
    return redis.call('incrby', KEYS[1], 1)
end
LUA;

    public function __construct(?string $redisPool = null)
    {
        $this->redisPool = $redisPool;
    }

    /**
     *  {@inheritdoc}
     */
    public function sequence(int $currentTime)
    {
        $redis = RedisManager::getInstance($this->redisPool);
        if (!$redis)
        {
            throw new \RuntimeException('Get redis instance failed');
        }

        return $redis->evalEx(static::SEQUENCE_LUA, [$this->prefix . $currentTime, 1, 1000], 1);
    }

    /**
     * Set cacge prefix.
     *
     * @param string $prefix
     *
     * @return self
     */
    public function setCachePrefix(string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }
}
