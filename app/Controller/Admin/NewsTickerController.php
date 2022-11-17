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
use App\Model\Advertisement;
use App\Model\NewsTicker;
use App\Request\NewsTickerRequest;
use App\Service\AdvertisementService;
use App\Service\NewsTickerService;
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
class NewsTickerController extends AbstractController
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
        $step = NewsTicker::PAGE_PER;
        $page = $request->input('page') ? intval($request->input('page'), 10) : 1;
        $query = NewsTicker::with('site')
            ->offset(($page - 1) * $step)
            ->limit($step);
        $query = $this->attachQueryBuilder($query);
        $newTickers = $query->get();

        $query = NewsTicker::select('*');
        $query = $this->attachQueryBuilder($query);
        $total = $query->count();

        $data['last_page'] = ceil($total / $step);
        if ($total == 0) {
            $data['last_page'] = 1;
        }
        $data['navbar'] = trans('default.newsticker_control.newsticker_control');
        $data['news_ticker_active'] = 'active';
        $data['total'] = $total;
        $data['datas'] = $newTickers;
        $data['page'] = $page;
        $data['step'] = $step;
        $path = '/admin/news_ticker/index';
        $data['next'] = $path . '?page=' . ($page + 1);
        $data['prev'] = $path . '?page=' . ($page - 1);
        $paginator = new Paginator($newTickers, $step, $page);

        $data['paginator'] = $paginator->toArray();
        return $this->render->render('admin.newsTicker.index', $data);
    }

    /**
     * @RequestMapping(path="store", methods={"POST"})
     */
    public function store(NewsTickerRequest $request, ResponseInterface $response, NewsTickerService $service): PsrResponseInterface
    {
        $data['id'] = $request->input('id') ? $request->input('id') : null;
        $data['name'] = $request->input('name');
        $data['detail'] = $request->input('detail');
        $data['start_time'] = $request->input('start_time');
        $data['end_time'] = $request->input('end_time');
        $data['buyer'] = $request->input('buyer');
        $data['site_id'] = $request->input('site_id');
        $data['expire'] = $request->input('expire');
        $service->storeNewsTicker($data);
        return $response->redirect('/admin/news_ticker/index');
    }

    /**
     * @RequestMapping(path="create", methods={"get"})
     */
    public function create()
    {
        $data['navbar'] = trans('default.newsticker_control.newsticker_insert');
        $data['news_ticker_active'] = 'active';
        $model = new NewsTicker();
        $model->expire = NewsTicker::EXPIRE['no'];
        $data['model'] = $model;
        return $this->render->render('admin.newsTicker.form', $data);
    }

    /**
     * @RequestMapping(path="edit", methods={"get"})
     */
    public function edit(RequestInterface $request)
    {
        $id = $request->input('id');
        $data['model'] = NewsTicker::findOrFail($id);
        $data['navbar'] = trans('default.newsticker_control.newsticker_update');
        $data['news_ticker_active'] = 'active';
        return $this->render->render('admin.newsTicker.form', $data);
    }

    /**
     * @RequestMapping(path="expire", methods={"POST"})
     */
    public function expire(RequestInterface $request, ResponseInterface $response, AdvertisementService $service): PsrResponseInterface
    {
        $query = NewsTicker::where('id', $request->input('id'));
        $query = $this->attachQueryBuilder($query);
        $record = $query->first();

        if (empty($record)) {
            return $response->redirect('/admin/news_ticker/index');
        }

        $record->expire = $request->input('expire', 1);
        $record->save();
        $service->updateCache();
        return $response->redirect('/admin/news_ticker/index');
    }
}
