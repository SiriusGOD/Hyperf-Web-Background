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
use App\Middleware\PermissionMiddleware;
use App\Model\Advertisement;
use App\Model\AdvertisementCount;
use App\Traits\SitePermissionTrait;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Paginator\Paginator;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\View\RenderInterface;
use HyperfExt\Jwt\Contracts\JwtFactoryInterface;
use HyperfExt\Jwt\Contracts\ManagerInterface;
use HyperfExt\Jwt\Jwt;

/**
 * @Controller
 * @Middleware(PermissionMiddleware::class)
 */
class AdvertisementCountController extends AbstractController
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
    public function index(RequestInterface $request, ResponseInterface $response, RenderInterface $render)
    {
        // 顯示幾筆
        $startTime = $request->input('start_time', Carbon::yesterday()->toDateString());
        $endTime = $request->input('end_time', Carbon::today()->toDateString());
        $siteId = $request->input('site_id');

        $dates = [];
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $count = abs($end->diffInDays($start));
        for ($i = 0; $i <= $count; ++$i) {
            $day = $start->copy()->addDays($i)->toDateString();
            $dates[] = $day;
        }

        if (! empty($siteId)) {
            $advertisements = Advertisement::where('site_id', $siteId)->get();
        } else {
            $advertisements = Advertisement::get();
        }

        $result = [];
        $ad_name = [];
        foreach ($advertisements as $advertisement) {
            $query = AdvertisementCount::whereBetween('click_date', [$startTime, $endTime])
                ->where('advertisements_id', $advertisement->id)
                ->groupBy(['click_date'])
                ->orderBy('click_date')
                ->select('click_date', Db::raw('count(*) as total'));
            $query = $this->attachQueryBuilder($query);

            $models = $query->get();

            $data = [];
            foreach ($dates as $key => $value) {
                $data[$key] = 0;
                foreach ($models as $model) {
                    if ($value == $model->click_date) {
                        $data[$key] = $model->total;
                    }
                }
            }
            $ad_name[] = $advertisement->name;
            $result[] = $data;
        }

        $data['models'] = $result;
        $data['labels'] = $dates;
        $data['advertisement_name'] = $ad_name;
        $data['start_time'] = $startTime;
        $data['end_time'] = $endTime;
        $data['site_id'] = $siteId;
        $data['navbar'] = trans('default.adCount_control.adCount_control');
        $data['advertisement_count_active'] = 'active';
        return $this->render->render('admin.advertisementCount.index', $data);
    }

    /**
     * @RequestMapping(path="list", methods={"GET"})
     */
    public function list(RequestInterface $request)
    {
        // 顯示幾筆
        $step = AdvertisementCount::PAGE_PER;
        $startTime = $request->input('start_time', Carbon::yesterday()->toDateString());
        $endTime = $request->input('end_time', Carbon::today()->toDateString());
        $siteId = $request->input('site_id');
        $page = $request->input('page') ? intval($request->input('page'), 10) : 1;
        $advertisements_name = $request->input('advertisements_name');

        $table_advertisment_site = Advertisement::join('sites', 'sites.id', '=', 'advertisements.site_id')
            ->select('advertisements.id', 'advertisements.name as advertisement_name', 'sites.name as site_name');

        $query = AdvertisementCount::joinSub($table_advertisment_site, 'table_advertisment_site', function ($join) {
            $join->on('advertisement_counts.advertisements_id', '=', 'table_advertisment_site.id');
        })
            ->select('advertisement_name', 'click_date', Db::raw('count(*) as click_num'), 'site_name')
            ->whereBetween('click_date', [$startTime, $endTime])
            ->groupBy(['advertisements_id', 'click_date'])
            ->orderBy('click_date');

        if (! empty($siteId)) {
            $query = $query->where('site_id', $siteId);
        }

        if (! empty($advertisements_name)) {
            $query = $query->where('advertisement_name', $advertisements_name);
        }

        $query = $this->attachQueryBuilder($query);

        $total = count($query->get());
        $models = $query->offset(($page - 1) * $step)->limit($step)->get();
        $data = [];
        $data['last_page'] = ceil($total / $step);
        if ($total == 0) {
            $data['last_page'] = 1;
        }
        $data['navbar'] = trans('default.adCount_control.adCount_list_control');
        $data['advertisement_count_list_active'] = 'active';
        $data['total'] = $total;
        $data['datas'] = $models;
        $data['page'] = $page;
        $data['step'] = $step;
        $path = '/admin/advertisement_count/list';
        // $data['next'] = $path . '?page=' . ($page + 1);
        // $data['prev'] = $path . '?page=' . ($page - 1);
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
        $data['advertisements_name'] = $advertisements_name;
        $paginator = new Paginator($models, $step, $page);

        $data['paginator'] = $paginator->toArray();

        return $this->render->render('admin.advertisementCount.list', $data);
    }
}
