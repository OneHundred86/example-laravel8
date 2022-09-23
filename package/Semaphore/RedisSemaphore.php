<?php


namespace Oh86\Semaphore;


class RedisSemaphore
{
    /**
     * 信号量名称
     * @var string
     */
    private $name;

    /**
     * @var \Illuminate\Redis\RedisManager
     */
    protected $redis;

    public function __construct(string $name)
    {
        $this->name = sprintf("%s@semaphore", $name);
        $this->redis = app('redis');
    }

    /**
     * 初始化信号量数量，返回true表示设置成功，false表示信号量已被初始化
     * @param int $cnt
     * @return bool
     */
    public function initCount(int $cnt): bool
    {
        return (bool)$this->redis->setnx($this->name, $cnt);
    }

    /**
     * @return true
     */
    public function v()
    {
        $this->redis->incr($this->name);
    }

    /**
     * @param int $timeout_sec
     * @return bool
     * @throws SemaphoreTimeoutException
     */
    public function p(int $timeout_sec = 10): bool
    {
        $script =<<<lua
    local key = KEYS[1]

    local v = redis.call("GET", key)

    v = tonumber(v)

    if v and v > 0 then
        redis.call("DECR", key)
        return true
    else
        return false
    end
lua;

        $left = $timeout_sec * 1000; // 毫秒
        while(true){
            if($this->redis->eval($script, 1, $this->name)){
                return true;
            }else{
                if($left <= 0){
                    throw new SemaphoreTimeoutException();
                }

                // 等待250ms
                $left -= 250;
                usleep(250*1000);
            }
        }
    }
}
