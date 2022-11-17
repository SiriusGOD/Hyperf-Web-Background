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

use App\Service\IconCountService;
use App\Service\ObfuscationService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * @Controller
 */
class IconCountController
{
    /**
     * @RequestMapping(path="click", methods="get")
     */
    public function click(RequestInterface $request, IconCountService $service, ObfuscationService $response)
    {
        $ip = getIp($request->getHeaders(), $request->getServerParams());
        $res = $service->updateIconCount($request->input('icon_id'), $ip, $request->input('site_id'));
        return $response->replyData(['response_code' => $res['code'], 'msg' => $res['msg']]);
    }
}
