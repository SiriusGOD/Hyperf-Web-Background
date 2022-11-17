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
use App\Model\Share;
use App\Model\ShareCount;
use Carbon\Carbon;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Redis\Redis;

class ShareService
{
    /*
     * KEY:日期:IP:圖標ID:次數
     * icon_count:date:ip:icon_id:count
     * ttl 1分鐘
     * */
    public const SITE_CLICK_KEY = 'siteClick';

    public const CACHE_KEY = 'share';

    public const CACHE_SUCCESS_KEY = 'share:success:';

    protected Redis $redis;

    protected \Psr\Log\LoggerInterface $logger;

    public function __construct(Redis $redis, LoggerFactory $loggerFactory)
    {
        $this->redis = $redis;
        $this->logger = $loggerFactory->get('reply');
    }

    // 算出日期到12點的時間

    public function ttlTo12(): int
    {
        $now = Carbon::now();
        $tomorrow = Carbon::tomorrow();
        return abs($tomorrow->diffInSeconds($now));
    }

    //產生 分享網址
    public function genUri(string $ip, int $site_id, string $fingerprint, string $apiUrl): array|string
    {
        $sitesurl = getSitesUrl($site_id);
        if (empty($site_id) || empty($fingerprint)) {
            $this->logger->error('未定義 site_id or user_info 關鍵字');
            return '';
        }
        $share_code = md5($fingerprint . $ip);
        $share = '/?share_code=' . $share_code;
        $uri = $sitesurl . $share;
        $urlArr = parse_url($apiUrl);
        $host = $urlArr['scheme'] . '://' . $urlArr['host'];
        if (!empty($urlArr['port'])) {
            $host = $host . ':' . $urlArr['port'];
        }
        $share = "/api/share/click?site_id={$site_id}&share_code={$share_code}";
        return ['code' => ApiCode::OK, 'msg' => 'ok', 'uri' => $uri, 'share_code' => $share_code, 'clickUri' => $host . $share];
    }

    // uri 的記錄寫入DB 也要存REDIS

    /**
     * @throws \RedisException
     */
    public function insertShareData(string $ip, int $site_id, ?string $fingerprint, string $code, $status = 0): array
    {
        $redisKey = self::CACHE_KEY . ':' . $ip . ':' . $site_id . ':' . $code;
        if (!$this->redis->exists($redisKey)) {
            $model = Share::where(
                [
                    ['fingerprint', '=', $fingerprint],
                    ['site_id', '=', $site_id],
                    ['ip', '=', $ip]
                ]
            )->first();
            if (!$model) {
                $model = new Share();
                $model->fingerprint = $fingerprint;
                $model->site_id = $site_id;
                $model->code = $code;
                $model->ip = $ip;
                $model->status = $status;
                if (!$model->save()) {
                    $this->logger->alert('share table ' . $model->id . ' 未存成功');
                }
            }
            $this->redis->set($redisKey, $model->id);
            $this->redis->expire($redisKey, $this->ttlTo12());

            // status = 1 表示設定分享網址為已完成
            // 新增進redis
            if ($status == Share::STATUS['done']) {
                $key = self::CACHE_SUCCESS_KEY . $model->id;
                if (!$this->redis->exists($key)) {
                    $result = [
                        'status' => $status,
                        'count' => 0,
                    ];
                    $this->redis->set($key, json_encode($result), 864000);
                }
            }
            return ['code' => ApiCode::OK, 'msg' => 'ok!', 'share_id' => $model->id];
        } else {
            return ['code' => ApiCode::OK, 'msg' => 'ok!', 'share_id' => intval($this->redis->get($redisKey))];
        }
    }

    /**
     * @throws \RedisException
     */
    public function insertClickData(string $ip, int $site_id, string $shareCode): array
    {
        $sitesurl = getSitesUrl($site_id);
        if (!$sitesurl) {
            return ['code' => ApiCode::OK, 'msg' => '找不到對應的 site id ', 'error_code' => 1];
        }
        $clickRedisKey = self::SITE_CLICK_KEY . ':' . $ip . ':' . $site_id . ':' . $shareCode;

        if ($this->redis->exists($clickRedisKey)) {
            $shareId = $this->redis->get($clickRedisKey);
        } else {
            $share = Share::where([['code', '=', $shareCode]])->first();
            if (!$share) {
                return ['code' => ApiCode::OK, 'msg' => '找不到 share code error', 'error_code' => 1, 'sitesurl' => $sitesurl];
            }
            $shareId = $share->id;
            $this->redis->set($clickRedisKey, $shareId, 86400);
        }
        $model = new ShareCount();
        $model->share_id = $shareId;
        $model->click_date = date('Y-m-d');
        $model->ip = $ip;
        if (!$model->save()) {
            $this->logger->alert('share count table 未存成功 ');
            return ['code' => ApiCode::OK, 'msg' => 'error', 'error_code' => 2];
        }
        return ['code' => ApiCode::OK, 'msg' => 'ok!', 'error_code' => 0, 'sitesurl' => $sitesurl];
    }

    // 新增分享代碼，不會更新

    /**
     * @throws \RedisException
     */
    public function storeShare(array $data): void
    {
        $ip = $data['ip'];
        $site_id = $data['site_id'];
        $status = $data['status'];
        $code = md5(Carbon::now()->toDateTimeString());
        $this->insertShareData($ip, $site_id, null, $code, $status);
    }

    /**
     * @throws \RedisException
     */
    public function status(int $shareId): array
    {
        $key = self::CACHE_SUCCESS_KEY . $shareId;
        if ($this->redis->exists($key)) {
            return json_decode($this->redis->get($key), true);
        }

        $count = ShareCount::where('share_id', $shareId)->count();
        if ($count >= ShareCount::SUCCESS_COUNT) {
            $share = Share::find($shareId);
            $share->status = Share::STATUS['done'];
            $share->save();

            $result = [
                'status' => Share::STATUS['done'],
                'count' => $count,
            ];

            $this->redis->set($key, json_encode($result), 864000);
            return $result;
        }

        return [
            'status' => Share::STATUS['undone'],
            'count' => $count,
        ];
    }

    public function updateCache(Share $record): void
    {
        $key = self::CACHE_SUCCESS_KEY . $record->id;
        $count = ShareCount::where('share_id', $record->id)->count();
        $this->redis->set($key, json_encode([
            'status' => $record->status,
            'count' => $count,
        ]), 864000);
    }
}
