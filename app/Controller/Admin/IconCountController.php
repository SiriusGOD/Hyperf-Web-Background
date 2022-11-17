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
use App\Model\IconCount;
use App\Service\IconCountService;
use App\Traits\SitePermissionTrait;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Paginator\Paginator;
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
class IconCountController extends AbstractController
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
    public function index(RequestInterface $request, IconCountService $service)
    {
        // 顯示幾筆
        $startTime = $request->input('start_time', Carbon::yesterday()->toDateString());
        $endTime = $request->input('end_time', Carbon::today()->toDateString());
        $siteId = $request->input('site_id');

        $labels = [];
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $count = abs($end->diffInDays($start));
        for ($i = 0; $i <= $count; ++$i) {
            $day = $start->copy()->addDays($i)->toDateString();
            $labels[] = $day;
        }

        $icons = Icon::all();
        $result = [];
        foreach ($icons as $icon) {
            $query = IconCount::whereBetween('date', [$startTime, $endTime])
                ->where('icon_id', $icon->id)
                ->groupBy(['date'])
                ->orderBy('date')
                ->select('date', Db::raw('sum(count) as total'));
            $query = $this->attachQueryBuilder($query);

            if (! empty($siteId)) {
                $query->where('site_id', $siteId);
            }
            $result[$icon->name] = $service->addTotal($query->get(), $start, $end);
        }

        $data['models'] = $result;
        $data['labels'] = $labels;
        $data['start_time'] = $startTime;
        $data['end_time'] = $endTime;
        $data['site_id'] = $siteId;
        $data['navbar'] = trans('default.iconcount_control.iconcount_control');
        $data['icon_count_active'] = 'active';
        return $this->render->render('admin.iconCount.index', $data);
    }

    /**
     * @RequestMapping(path="list", methods={"GET"})
     */
    public function list(RequestInterface $request, IconCountService $service)
    {
        // 顯示幾筆
        $step = IconCount::PAGE_PER;
        $startTime = $request->input('start_time', Carbon::yesterday()->toDateString());
        $endTime = $request->input('end_time', Carbon::today()->toDateString());
        $siteId = $request->input('site_id');
        $page = $request->input('page') ? intval($request->input('page'), 10) : 1;

        $query = IconCount::with(['icon', 'site'])->whereBetween('date', [$startTime, $endTime]);
        if (! empty($siteId)) {
            $query = $query->where('site_id', $siteId);
        }

        $query = $this->attachQueryBuilder($query);
        $total = $query->count();
        $models = $query
            ->offset(($page - 1) * $step)
            ->limit($step)
            ->get();

        $data = [];
        $data['last_page'] = ceil($total / $step);
        if ($total == 0) {
            $data['last_page'] = 1;
        }
        $data['navbar'] = trans('default.iconcount_control.iconcount_list_control');
        $data['icon_count_list_active'] = 'active';
        $data['total'] = $total;
        $data['datas'] = $models;
        $data['page'] = $page;
        $data['step'] = $step;
        $path = '/admin/icon_count/list';
        $pageArr = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'site_id' => $siteId,
        ];
        $pageArr['page'] = $page + 1;
        $nextQuery = http_build_query($pageArr);
        $data['next'] = $path . '?' . $nextQuery;

        $pageArr['page'] = $page - 1;
        $prevQuery = http_build_query($pageArr);
        $data['prev'] = $path . '?' . $prevQuery;

        $data['start_time'] = $startTime;
        $data['end_time'] = $endTime;
        $data['site_id'] = $siteId;
        $paginator = new Paginator($models, $step, $page);

        $data['paginator'] = $paginator->toArray();

        return $this->render->render('admin.iconCount.list', $data);
    }
}
