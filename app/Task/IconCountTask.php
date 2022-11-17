<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Task;

use App\Service\IconCountService;
use Carbon\Carbon;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Redis\Redis;

/**
 * @Crontab(name="IconCountTask", rule="* * * * *", callback="execute", memo="定時把redis的值回寫table")
 */
class IconCountTask
{
    protected Redis $redis;

    protected $service;

    private \Psr\Log\LoggerInterface $logger;

    public function __construct(IconCountService $service, LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('crontab', 'crontab');
        $this->service = $service;
    }

    public function execute()
    {
        $now = Carbon::now()->toDateTimeString();
        $data = $this->service->getRedisDatas();
        if ($data['code'] != 400) {
            $this->service->insertData($data);
            $this->service->redisClean($data);
            $this->logger->info('icon點擊回寫完成 ' . $now);
        } else {
            $this->logger->info('icon點擊回寫失敗' . $now);
        }
    }
}
