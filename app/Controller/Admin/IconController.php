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
use App\Model\Icon;
use App\Request\AdvertisementRequest;
use App\Request\IconRequest;
use App\Service\AdvertisementService;
use App\Service\IconCountService;
use App\Service\IconService;
use App\Traits\SitePermissionTrait;
use Carbon\Carbon;
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
class IconController extends AbstractController
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
        $step = Icon::PAGE_PER;
        $page = $request->input('page') ? intval($request->input('page'), 10) : 1;
        $query = Icon::with('site')
            ->offset(($page - 1) * $step)
            ->limit($step);
        $query = $this->attachQueryBuilder($query);
        $models = $query->get();

        $query = Icon::select('*');
        $query = $this->attachQueryBuilder($query);
        $total = $query->count();

        $data['last_page'] = ceil($total / $step);
        if ($total == 0) {
            $data['last_page'] = 1;
        }
        $data['navbar'] = trans('default.icon_control.icon_control');
        $data['icon_active'] = 'active';
        $data['total'] = $total;
        $data['datas'] = $models;
        $data['page'] = $page;
        $data['step'] = $step;
        $path = '/admin/icon/index';
        $data['next'] = $path . '?page=' . ($page + 1);
        $data['prev'] = $path . '?page=' . ($page - 1);
        $paginator = new Paginator($models, $step, $page);

        $data['paginator'] = $paginator->toArray();

        return $this->render->render('admin.icon.index', $data);
    }

    /**
     * @RequestMapping(path="store", methods={"POST"})
     */
    public function store(IconRequest $request, ResponseInterface $response, IconService $service): PsrResponseInterface
    {
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $request->file('image')->getExtension();
            $filename = sha1(Carbon::now()->toDateTimeString());
            $imageUrl = '/icon/' . $filename . '.' . $extension;
            $file->moveTo(BASE_PATH . '/public' . $imageUrl);
        }
        $data['id'] = $request->input('id') ? $request->input('id') : null;
        $data['name'] = $request->input('name');
        if (! empty($imageUrl)) {
            $data['image_url'] = $imageUrl;
        }
        $data['url'] = $request->input('url');
        $data['position'] = $request->input('position');
        $data['start_time'] = $request->input('start_time');
        $data['end_time'] = $request->input('end_time');
        $data['buyer'] = $request->input('buyer');
        $data['site_id'] = $request->input('site_id');
        $data['sort'] = $request->input('sort');
        $data['expire'] = $request->input('expire');
        $service->storeIcon($data);
        return $response->redirect('/admin/icon/index');
    }

    /**
     * @RequestMapping(path="create", methods={"get"})
     */
    public function create()
    {
        $data['navbar'] = trans('default.icon_control.icon_insert');
        $data['icon_active'] = 'active';
        $model = new Icon();
        $model->expire = Icon::EXPIRE['no'];
        $model->position = Icon::POSITION['top'];
        $data['model'] = $model;
        return $this->render->render('admin.icon.form', $data);
    }

    /**
     * @RequestMapping(path="edit", methods={"get"})
     */
    public function edit(RequestInterface $request)
    {
        $id = $request->input('id');
        $data['model'] = Icon::findOrFail($id);
        $data['navbar'] = trans('default.icon_control.icon_update');
        $data['icon_active'] = 'active';
        return $this->render->render('admin.icon.form', $data);
    }

    /**
     * @RequestMapping(path="expire", methods={"POST"})
     */
    public function expire(RequestInterface $request, ResponseInterface $response, IconService $service): PsrResponseInterface
    {
        $query = Icon::where('id', $request->input('id'));
        $query = $this->attachQueryBuilder($query);
        $record = $query->first();

        if (empty($record)) {
            return $response->redirect('/admin/icon/index');
        }

        $record->expire = $request->input('expire', 1);
        $record->save();
        $service->updateCache();
        return $response->redirect('/admin/icon/index');
    }
}
