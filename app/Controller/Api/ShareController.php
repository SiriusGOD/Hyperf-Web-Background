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

use App\Service\ObfuscationService;
use App\Service\ShareService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\View\RenderInterface;

/**
 * @Controller
 */
class ShareController
{
    /**
     * @RequestMapping(path="status", methods="get")
     */
    public function status(RequestInterface $request, ShareService $service, ObfuscationService $response)
    {
        $data = $service->status((int) $request->input('share_id'));

        return $response->replyData($data);
    }

    /**
     * 前端生成分享網址 API.
     * @RequestMapping(path="getUri", methods="get")
     */
    public function getUri(RequestInterface $request, ShareService $service, ObfuscationService $response)
    {
        $ip = getIp($request->getHeaders() ,$request->getServerParams());
        $res = $service->genUri($ip, (int) $request->input('site_id', 1), $request->input('fingerprint'), $request->url());
        $shareInfo=$service->insertShareData($ip, (int) $request->input('site_id', 1), $request->input('fingerprint'), $res['share_code']);
        return $response->replyData(['code' => $res['code'], 'msg' => $res['msg'],'share_id' => $shareInfo['share_id'] , 'share_code' => $res['share_code'], 'clickUri' => $res['clickUri']]);
    }

    /**
     * 分享網址點擊 API.
     * @RequestMapping(path="click", methods="get")
     */
    public function click(RequestInterface $request, ShareService $service, RenderInterface $render, ObfuscationService $response)
    {
        $ip = getIp($request->getHeaders() ,$request->getServerParams());
        $res = $service->insertClickData($ip, (int) $request->input('site_id'), $request->input('share_code'));
        if ($res['error_code'] == 1) {
            return $response->replyData(['code' => $res['code'], 'msg' => $res['msg']]);
        }
        $url = $res['sitesurl'];
        if (strpos($url, 'ttp:') == 0 && strpos($url, 'ttps:') == 0) {
            $url = 'http://' . $url;
        }
        return $render->render('redirect', ['rui' => $url]);
    }
}
