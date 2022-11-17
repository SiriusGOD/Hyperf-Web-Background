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

use App\Model\NewsTicker;
use App\Service\NewsTickerService;
use Carbon\Carbon;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Redis\Redis;

/**
 * @Crontab(name="NewsTickerTask", rule="* * * * *", callback="execute", memo="跑馬燈上下架定時任務")
 */
class NewsTickerTask
{
    protected Redis $redis;

    protected $service;

    private \Psr\Log\LoggerInterface $logger;

    public function __construct(NewsTickerService $service, LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('crontab', 'crontab');
        $this->service = $service;
    }

    public function execute()
    {
        $now = Carbon::now()->toDateTimeString();
        $models = NewsTicker::where('end_time', '<=', $now)
            ->where('expire', NewsTicker::EXPIRE['no'])
            ->get();

        if (count($models) == 0) {
            return;
        }

        $this->logger->info('有跑馬燈過期');
        foreach ($models as $model) {
            $model->expire = NewsTicker::EXPIRE['yes'];
            $model->save();
            $this->logger->info('跑馬燈 id : ' . $model->id . ' 過期');
        }

        $this->service->updateCache();

        $this->logger->info('更新跑馬燈完成');
    }
}
