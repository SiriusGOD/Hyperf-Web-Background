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

use App\Constants\ApiCode;
use App\Service\AdvertisementCountService;
use App\Service\ObfuscationService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * @Controller
 */
class AdvertisementCountController
{
    /**
     * @RequestMapping(path="click", methods="get")
     */
    public function click(RequestInterface $request, ObfuscationService $response, AdvertisementCountService $service)
    {
        $ip = getIp($request->getHeaders(), $request->getServerParams());
        $site_id = $request->input('site_id') ? $request->input('site_id') : 1;
        $res = $service->insertClickData($ip, (int) $site_id, (int) $request->input('advertisements_id'));
        $data['msg'] = $res['msg'];
        if ($res['code'] == ApiCode::OK) {
            return $response->replyData($data);
        }
        return $response->replyData($data, $res['code']);
    }
}
