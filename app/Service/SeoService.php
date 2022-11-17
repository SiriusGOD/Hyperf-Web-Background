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

use App\Model\SeoKeyword;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Redis\Redis;

class SeoService
{
    public const ONLY_ONE = 1;

    public const CACHE_KEY = 'seo_keyword';

    protected Redis $redis;

    protected \Psr\Log\LoggerInterface $logger;

    public function __construct(Redis $redis, LoggerFactory $loggerFactory)
    {
        $this->redis = $redis;
        $this->logger = $loggerFactory->get('reply');
    }

    // 取得 seo 關鍵字
    public function getKeywords($siteId): string
    {
        if ($this->redis->exists(self::CACHE_KEY . ':' . $siteId)) {
            return $this->redis->get(self::CACHE_KEY . ':' . $siteId);
        }

        $keywords = SeoKeyword::where('site_id', $siteId)->first();

        if (empty($keywords)) {
            $this->logger->error('未定義 seo 關鍵字');
            return '';
        }

        $this->redis->set(self::CACHE_KEY . ':' . $siteId, $keywords->keywords);

        return $keywords->keywords;
    }

    // 更新 seo 關鍵字
    public function storeKeywords(string $keywords, int $siteId): void
    {
        $model = new SeoKeyword();
        $seoModel = SeoKeyword::where('site_id', $siteId)->first();
        if (! empty($seoModel)) {
            $model = $seoModel;
        }
        $model->keywords = $keywords;
        $model->site_id = $siteId;
        $model->save();
        $this->redis->set(self::CACHE_KEY . ':' . $siteId, $keywords);
    }
}
