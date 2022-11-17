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
namespace App\Controller\Api;

use App\Service\IconService;
use App\Service\ObfuscationService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * @Controller
 */
class IconController
{
    /**
     * @RequestMapping(path="list", methods="get")
     */
    public function list(RequestInterface $request, IconService $service, ObfuscationService $response)
    {
        $siteId = (int) $request->input('site_id', 1);
        $data = $service->getIcons($siteId);
        // 取得網址前綴
        $result = [];
        $url = $request->url();
        $urlArr = parse_url($url);
        $port = $urlArr['port'] ?? '80';
        $host = $urlArr['scheme'] . '://' . $urlArr['host'] . ':' . $port;
        foreach ($data as $item) {
            if (! empty($item['image_url'])) {
                $item['image_url'] = $host . $item['image_url'];
            }
            $result[] = $item;
        }

        return $response->replyData($result);
    }
}
