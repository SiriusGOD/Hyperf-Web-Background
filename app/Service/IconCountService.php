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

use App\Constants\ApiCode;
use App\Model\IconCount;
use App\Model\Site;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Redis\Redis;
use Hyperf\Utils\Collection;

class IconCountService
{
    /*
     * KEY:日期:IP:圖標ID:次數
     * icon_count:date:ip:icon_id:count
     * ttl 1分鐘
     * */
    public const CACHE_KEY = 'icon_count';

    public const SITE_KEY = 'allSites';

    public const TTL_SEC = 60;

    public const TTL_A_DAY = 86400;

    public const LOCK_TTL = 10;

    protected Redis $redis;

    protected \Psr\Log\LoggerInterface $logger;

    public function __construct(Redis $redis, LoggerFactory $loggerFactory)
    {
        $this->redis = $redis;
        $this->logger = $loggerFactory->get('reply');
    }

    // 設定
    public function setSiteRedis()
    {
        $sites = Site::all();
        foreach ($sites as $key => $site) {
            $this->redis->hSet(self::SITE_KEY, strval($site->id), $site->url);
        }
        $this->redis->expire(self::SITE_KEY, 60 * 60);
    }

    // 更新 Icon Count 資訊
    public function updateIconCount($icon_id, $ip, $host_id)
    {
        if (empty($icon_id)) {
            $this->logger->error('未定義 icon_id 關鍵字');
            return '';
        }
        if (empty($host_id)) {
            $this->logger->error('未定義 host_id 關鍵字');
            return '';
        }

        $date = date('Ymd');
        $key = self::CACHE_KEY;
        $checkRedisKey = "{$key}:{$host_id}:{$date}:{$ip}:{$icon_id}:check";
        $redisKey = "{$key}:{$date}:{$host_id}";
        $lockKey = "{$key}:{$date}:{$host_id}:{$icon_id}:lock";
        if (! $this->redis->exists($checkRedisKey) && self::lock($lockKey)) {
            $this->redis->set($checkRedisKey, 1);
            $this->redis->expire($checkRedisKey, 86400);

            $this->redis->hIncrBy($redisKey, strval($icon_id), 1);
            $this->redis->expire($redisKey, self::TTL_A_DAY);
            $this->redis->del($lockKey);
            return ['code' => ApiCode::OK, 'msg' => 'ok'];
        }
        return ['code' => ApiCode::BAD_REQUEST, 'msg' => '己有相同IP'];
    }

    // 取得icon count Redis資料
    public function getRedisDatas()
    {
        // 如果沒有 去取資料 存入REDIS
        if (! $this->redis->exists(self::SITE_KEY)) {
            self::setSiteRedis();
        }
        $allSites = $this->redis->hGetAll(self::SITE_KEY);
        $data = [];
        foreach ($allSites as $site_id => $site) {
            $redisKey = self::CACHE_KEY . ':' . date('Ymd') . ':' . $site_id;
            if ($this->redis->exists($redisKey)) {
                $data[$site_id] = $this->redis->hGetAll($redisKey);
            }
        }
        if ($data) {
            return ['code' => ApiCode::OK, 'data' => $data];
        }
        return ['code' => ApiCode::BAD_REQUEST, 'msg' => 'redis not found!'];
    }

    // redis 值寫入DB
    public function insertData(array $data)
    {
        $date = date('Ymd');
        foreach ($data['data'] as $site_id => $datas) {
            $lockKey = self::CACHE_KEY . ':' . $date . ":{$site_id}:lock";
            foreach ($datas as $icon_id => $count) {
                self::lock($lockKey);
                $model = IconCount::where(
                    [
                        ['icon_id', '=', $icon_id],
                        ['date', '=', $date],
                        ['site_id', '=', $site_id],
                    ]
                )->first();
                if ($model) {
                    $model->count = $model->count + $count;
                } else {
                    $model = new IconCount();
                    $model->count = $count;
                    $model->site_id = $site_id;
                    $model->icon_id = $icon_id;
                    $model->date = date('Ymd');
                    $model->icon_name = '';
                }
                if (! $model->save()) {
                    $this->logger->alert($model->id . ' 未存成功');
                }
            }
            $this->redis->del($lockKey);
        }
        return ['code' => ApiCode::OK, 'msg' => 'ok!'];
    }

    // 刪除 REDIS 中的值
    public function redisClean(array $sites)
    {
        foreach ($sites['data'] as $site_id => $site) {
            $redisKey = self::CACHE_KEY . ':' . date('Ymd') . ":{$site_id}";
            if ($this->redis->exists($redisKey)) {
                $redisData = $this->redis->hGetAll($redisKey);
                foreach ($redisData as $icon_id => $c) {
                    $this->redis->hDel($redisKey, $icon_id);
                }
            }
        }
        return ['code' => ApiCode::OK, 'msg' => 'redis is clean'];
    }

    // redis鎖
    public function lock($key)
    {
        return $this->redis->setnx($key, 1) && $this->redis->expire($key, self::LOCK_TTL);
    }

    public function addTotal($models, $start, $end)
    {
        $keyed = $models->mapWithKeys(function ($item, $key) {
            return [$item['date'] => $item['total']];
        })->toArray();
        $count = abs($end->diffInDays($start));
        for ($i = 0; $i <= $count; ++$i) {
            $base = $start->copy();
            $key = $base->addDays($i)->toDateString();
            if (! array_key_exists($key, $keyed)) {
                $keyed[$key] = 0;
            }
        }

        $result = [];
        ksort($keyed);
        foreach ($keyed as $item) {
            $result[] = (int) $item;
        }

        return $result;
    }
}
