<?php

namespace App\Console\Commands\Laravel;

use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;

class CacheLock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:cache_lock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cache lock demo';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $lock_name = __METHOD__;
        $is_locked = false;
        $lock = Cache::lock($lock_name, 600);

        try {
            $is_locked = $lock->block(5);
            $this->info("获取锁成功，执行代码...");
        } catch (LockTimeoutException $e){
            $this->error("获取锁失败，超时");
        } finally {
            $is_locked ? $lock->release() : false;
        }

        return 0;
    }
}
