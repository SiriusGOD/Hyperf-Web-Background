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
namespace App\Service;

use App\Model\Advertisement;
use App\Model\Icon;
use App\Model\NewsTicker;
use App\Model\Site;
use Carbon\Carbon;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Redis\Redis;

class NewsTickerService
{
    public const CACHE_KEY = 'news_ticker';

    protected Redis $redis;

    protected \Psr\Log\LoggerInterface $logger;

    public function __construct(Redis $redis, LoggerFactory $loggerFactory)
    {
        $this->redis = $redis;
        $this->logger = $loggerFactory->get('reply');
    }

    // 取得廣告
    public function getNewsTickers(int $siteId)
    {
        if ($this->redis->exists(self::CACHE_KEY . ':' . $siteId)) {
            $jsonResult = $this->redis->get(self::CACHE_KEY . ':' . $siteId);
            return json_decode($jsonResult, true);
        }

        $now = Carbon::now()->toDateTimeString();
        $result = NewsTicker::where('start_time', '<=', $now)
            ->where('site_id', $siteId)
            ->where('end_time', '>=', $now)
            ->get()
            ->toArray();

        $this->redis->set(self::CACHE_KEY . ':' . $siteId, json_encode($result));

        return $result;
    }

    // 更新快取
    public function updateCache(): void
    {
        $sites = Site::all();

        foreach ($sites as $site) {
            $now = Carbon::now()->toDateTimeString();
            $result = NewsTicker::where('start_time', '<=', $now)
                ->where('end_time', '>=', $now)
                ->where('expire', Icon::EXPIRE['no'])
                ->where('site_id', $site->id)
                ->get()
                ->toArray();

            $this->redis->set(self::CACHE_KEY . ':' . $site->id, json_encode($result));
        }
    }

    // 新增或更新廣告
    public function storeNewsTicker(array $data): void
    {
        $model = NewsTicker::findOrNew($data['id']);
        $model->name = $data['name'];
        $model->detail = $data['detail'];
        $model->start_time = $data['start_time'];
        $model->end_time = $data['end_time'];
        $model->buyer = $data['buyer'];
        $model->expire = $data['expire'];
        $model->site_id = $data['site_id'];
        $model->save();
        $this->updateCache();
    }
}
