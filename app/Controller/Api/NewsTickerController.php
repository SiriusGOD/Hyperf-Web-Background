<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\AdvertisementService;
use App\Service\NewsTickerService;
use App\Service\ObfuscationService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * @Controller()
 */
class NewsTickerController
{
    /**
     * @RequestMapping(path="list", methods="get")
     */
    public function list(RequestInterface $request, NewsTickerService $service, ObfuscationService $response)
    {
        $siteId = (int) $request->input('site_id', 1);
        $data = $service->getNewsTickers($siteId);

        return $response->replyData($data);
    }
}
