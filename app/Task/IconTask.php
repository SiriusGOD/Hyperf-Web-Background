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

use App\Model\Advertisement;
use App\Model\Icon;
use App\Service\AdvertisementService;
use Carbon\Carbon;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Redis\Redis;

/**
 * @Crontab(name="IconTask", rule="* * * * *", callback="execute", memo="入口圖標上下架定時任務")
 */
class IconTask
{
    protected Redis $redis;

    protected $service;

    private \Psr\Log\LoggerInterface $logger;

    public function __construct(AdvertisementService $service, LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('crontab', 'crontab');
        $this->service = $service;
    }

    public function execute()
    {
        $now = Carbon::now()->toDateTimeString();
        $models = Icon::where('end_time', '<=', $now)
            ->where('expire', Icon::EXPIRE['no'])
            ->get();

        if (count($models) == 0) {
            return;
        }

        $this->logger->info('有入口圖標過期');
        foreach ($models as $model) {
            $model->expire = Icon::EXPIRE['yes'];
            $model->save();
            $this->logger->info('入口圖標 id : ' . $model->id . ' 過期');
        }

        $this->service->updateCache();

        $this->logger->info('更新入口圖標完成');
    }
}
