<?php

namespace App\Console\Commands\Laravel;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RedisTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:redis_transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'redis transaction demo';

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
        $redis = app('redis');

        $result = $redis->transaction(function(\Redis $r){
            $r->setex('k1', 60, 10);  // 执行结果储存在事务的返回值$result[0]
            $r->get('k1');                          // 执行结果储存在事务的返回值$result[1]

            $r->del('k1');                    // 执行结果储存在事务的返回值$result[2]
        });

        var_dump($result);
        /**
            array(3) {
                [0]=> bool(true)
                [1]=> string(2) "10"
                [2]=> int(1)
            }
         */

        return 0;
    }
}
