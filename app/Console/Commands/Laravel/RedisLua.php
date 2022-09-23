<?php

namespace App\Console\Commands\Laravel;

use Illuminate\Console\Command;
use Oh86\Semaphore\RedisSemaphore;
use Oh86\Semaphore\SemaphoreTimeoutException;

class RedisLua extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:redis_lua';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'redis eval lua demo';

    /**
     * @var \Illuminate\Redis\RedisManager
     */
    protected $redis;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redis = app('redis');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $this->demo1();
        $this->semaphoreDemo();

        return 0;
    }

    /**
     * redis信号量demo（信号量实现互斥）
     */
    public function semaphoreDemo()
    {
        $semaphore = new RedisSemaphore("test");
        $semaphore->initCount(1); // 初始信号量，如果已经初始过了，函数执行不成功，相当于跳过

        $is_p = false;
        try {
            $is_p = $semaphore->p(5);   // 尝试获取信号量
            $this->info("获得信号量，执行程序...");
        } catch (SemaphoreTimeoutException $e) {
            $this->error("超时");
        } finally {
            if ($is_p) {
                $semaphore->v();        // 如果获取信号量成功，并且执行完代码后，就释放信号量
            }
        }
    }

    /**
     * https://www.runoob.com/redis/scripting-eval.html
     */
    public function demo1()
    {
        $redis = $this->redis;

        $redis->eval("return {KEYS[1],KEYS[2],ARGV[1],ARGV[2]}", 2, "key1", "key2", "val1", "val2");
    }
}
