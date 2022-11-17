<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\ObfuscationService;
use App\Service\SeoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * @Controller()
 */
class SeoController
{
    /**
     * @RequestMapping(path="keywords", methods="get")
     */
    public function keywords(RequestInterface $request, SeoService $service, ObfuscationService $response)
    {
        $siteId = (int) $request->input('site_id', 1);
        $keywords = $service->getKeywords($siteId);

        return $response->replyData(['keywords' => $keywords]);
    }
}
