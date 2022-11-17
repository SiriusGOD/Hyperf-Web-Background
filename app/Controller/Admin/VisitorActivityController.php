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
use App\Model\VisitorActivity;
use App\Traits\SitePermissionTrait;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\View\RenderInterface;
use HyperfExt\Jwt\Contracts\JwtFactoryInterface;
use HyperfExt\Jwt\Contracts\ManagerInterface;
use HyperfExt\Jwt\Jwt;

use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\PermissionMiddleware;

/**
 * @Controller
 * @Middleware(PermissionMiddleware::class)
 */
class VisitorActivityController extends AbstractController
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
        $startTime = $request->input('start_time', Carbon::yesterday()->toDateString());
        $endTime = $request->input('end_time', Carbon::today()->toDateString());
        $siteId = $request->input('site_id');

        $query = VisitorActivity::whereBetween('visit_date', [$startTime, $endTime])
            ->groupBy(['visit_date'])
            ->orderBy('visit_date')
            ->select('visit_date', Db::raw('count(*) as total'));
        $query = $this->attachQueryBuilder($query);

        if (! empty($siteId)) {
            $query->where('site_id', $siteId);
        }

        $models = $query->get();

        $labels = [];
        $datas = [];
        foreach ($models as $model) {
            $labels[] = $model->visit_date;
            $datas[] = $model->total;
        }

        $data['models'] = $models;
        $data['labels'] = $labels;
        $data['data'] = $datas;
        $data['start_time'] = $startTime;
        $data['end_time'] = $endTime;
        $data['site_id'] = $siteId;
        $data['navbar'] = trans('default.visitoractivity_control.visitoractivity_control');
        $data['visitor_activity_active'] = 'active';
        return $this->render->render('admin.visitorActivity.index', $data);
    }

    /**
     * @RequestMapping(path="list", methods={"GET"})
     */
    public function list(RequestInterface $request)
    {
        // 顯示幾筆
        $startTime = $request->input('start_time', Carbon::yesterday()->toDateString());
        $endTime = $request->input('end_time', Carbon::today()->toDateString());
        $siteId = $request->input('site_id');

        $query = VisitorActivity::whereBetween('visit_date', [$startTime, $endTime])
            ->groupBy(['visit_date'])
            ->orderBy('visit_date')
            ->select('visitor_activities.visit_date', Db::raw('count(*) as total'));
        $query = $this->attachQueryBuilder($query);

        if (! empty($siteId)) {
            $query->where('site_id', $siteId);
        }

        $models = $query->get();

        $data['models'] = $models;
        $data['start_time'] = $startTime;
        $data['end_time'] = $endTime;
        $data['site_id'] = $siteId;
        $data['navbar'] = trans('default.visitoractivity_control.visitoractivity_list_control');
        $data['visitor_activity_list_active'] = 'active';
        return $this->render->render('admin.visitorActivity.list', $data);
    }
}
