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

use App\Model\VisitorActivity;
use Carbon\Carbon;
use Hyperf\Redis\Redis;

class VisitorActivityService
{
    protected Redis $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function isCache(string $ip, int $siteId): bool
    {
        $key = $this->getCacheKey($ip, $siteId);
        if ($this->redis->exists($key)) {
            return true;
        }

        return false;
    }

    public function storeVisitorActivity(string $ip, int $siteId): void
    {
        $now = Carbon::now();
        $model = new VisitorActivity();
        $model->ip = $ip;
        $model->site_id = $siteId;
        $model->visit_date = $now->toDateString();
        $model->save();

        $key = $this->getCacheKey($ip, $siteId);
        $tomorrow = Carbon::tomorrow();
        $expire = abs($tomorrow->diffInSeconds($now));
        $this->redis->set($key, 1, $expire);
    }

    protected function getCacheKey(string $ip, int $siteId): string
    {
        return 'visitor:' . $ip . ':' . $siteId;
    }
}
