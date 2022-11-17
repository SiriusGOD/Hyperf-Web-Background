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
namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\Share;
use App\Request\ShareRequest;
use App\Service\ShareService;
use App\Traits\SitePermissionTrait;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Paginator\Paginator;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\View\RenderInterface;
use HyperfExt\Jwt\Contracts\JwtFactoryInterface;
use HyperfExt\Jwt\Contracts\ManagerInterface;
use HyperfExt\Jwt\Jwt;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\PermissionMiddleware;

/**
 * @Controller
 * @Middleware(PermissionMiddleware::class)
 */
class ShareController extends AbstractController
{
    use SitePermissionTrait;

    /**
     * 提供了对 JWT 编解码、刷新和失活的能力。
     */
    protected ManagerInterface $manager;

    /**
     * 提供了从请求解析 JWT 及对 JWT 进行一系列相关操作的能力。
     */
    protected Jwt $jwt;

    protected RenderInterface $render;

    /**
     * @Inject
     */
    protected ValidatorFactoryInterface $validationFactory;

    public function __construct(ManagerInterface $manager, JwtFactoryInterface $jwtFactory, RenderInterface $render)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->jwt = $jwtFactory->make();
        $this->render = $render;
    }

    /**
     * @RequestMapping(path="index", methods={"GET"})
     */
    public function index(RequestInterface $request)
    {
        // 顯示幾筆
        $step = Share::PAGE_PER;
        $page = $request->input('page') ? intval($request->input('page'), 10) : 1;
        $query = Share::with('site')
            ->offset(($page - 1) * $step)
            ->limit($step);
        $query = $this->attachQueryBuilder($query);
        $newTickers = $query->get();

        $query = Share::select('*');
        $query = $this->attachQueryBuilder($query);
        $total = $query->count();

        $data['last_page'] = ceil($total / $step);
        if ($total == 0) {
            $data['last_page'] = 1;
        }
        $data['navbar'] = trans('default.share_control.share_control');
        $data['share_active'] = 'active';
        $data['total'] = $total;
        $data['datas'] = $newTickers;
        $data['page'] = $page;
        $data['step'] = $step;
        $path = '/admin/share/index';
        $data['next'] = $path . '?page=' . ($page + 1);
        $data['prev'] = $path . '?page=' . ($page - 1);
        $paginator = new Paginator($newTickers, $step, $page);

        $data['paginator'] = $paginator->toArray();

        // 取得分享網址前綴
        $apiUrl = $request->url();
        $urlArr = parse_url($apiUrl);
        $host = $urlArr['scheme'] . '://' . $urlArr['host'] . ':' . $urlArr['port'];
        $share = '/api/share/click';
        $data['share_url_prefix'] = $host . $share;

        return $this->render->render('admin.share.index', $data);
    }

    /**
     * @RequestMapping(path="store", methods={"POST"})
     */
    public function store(ShareRequest $request, ResponseInterface $response, ShareService $service): PsrResponseInterface
    {
        $data['site_id'] = $request->input('site_id');
        $data['ip'] = $request->input('ip');
        $data['status'] = $request->input('status');
        $service->storeShare($data);
        return $response->redirect('/admin/share/index');
    }

    /**
     * @RequestMapping(path="create", methods={"get"})
     */
    public function create()
    {
        $data['navbar'] = trans('default.share_control.share_insert');
        $data['share_active'] = 'active';
        $model = new Share();
        $model->status = Share::STATUS['undone'];
        $data['model'] = $model;
        return $this->render->render('admin.share.form', $data);
    }

    /**
     * @RequestMapping(path="status", methods={"POST"})
     */
    public function status(RequestInterface $request, ResponseInterface $response, ShareService $service): PsrResponseInterface
    {
        $id = (int) $request->input('id');
        $query = Share::where('id', $id);
        $query = $this->attachQueryBuilder($query);
        $record = $query->first();

        if (empty($record)) {
            return $response->redirect('/admin/share/index');
        }

        $record->status = $request->input('status', Share::STATUS['undone']);
        $record->save();
        $service->updateCache($record);
        return $response->redirect('/admin/share/index');
    }
}
