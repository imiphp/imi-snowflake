<?php

declare(strict_types=1);

namespace Imi\Snowflake;

use Imi\App;

class SnowflakeUtil
{
    /**
     * 实例对象集合.
     */
    private static array $instances = [];

    /**
     * 获取实例对象
     *
     * 不存在则实例化
     *
     * @return \Imi\Snowflake\SnowflakeClass
     */
    public static function getInstance(string $name): SnowflakeClass
    {
        if (isset(static::$instances[$name]))
        {
            return static::$instances[$name];
        }
        /** @var \Imi\Snowflake\Bean\Snowflake $snowflake */
        $snowflake = App::getBean('Snowflake');
        $options = $snowflake->getByName($name);
        if (null === $options)
        {
            throw new \RuntimeException(sprintf('Get snowflake options %s failed', $name));
        }

        return static::$instances[$name] = static::newInstance($options['datacenterId'] ?? null, $options['workerId'] ?? null, $options['startTimeStamp'] ?? null, $options['redisPool'] ?? null);
    }

    /**
     * 实例化对象
     *
     * @return \Imi\Snowflake\SnowflakeClass
     */
    public static function newInstance(?int $datacenterId = null, ?int $workerId = null, ?int $startTimeStamp = null, ?string $redisPool = null): SnowflakeClass
    {
        $instance = new SnowflakeClass($datacenterId, $workerId);
        $instance->setSequenceResolver(new ImiRedisResolver($redisPool));
        if ($startTimeStamp)
        {
            $instance->setStartTimeStamp($startTimeStamp);
        }

        return $instance;
    }

    /**
     * 使用雪花算法生成ID.
     */
    public static function id(string $name): string
    {
        return static::getInstance($name)->id();
    }

    /**
     * 解析雪花算法生成的ID.
     */
    public static function parseId(string $name, string $id, bool $transform = false): array
    {
        return static::getInstance($name)->parseId($id, $transform);
    }
}
