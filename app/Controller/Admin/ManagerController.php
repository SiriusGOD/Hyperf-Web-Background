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
use App\Model\User;
use App\Service\UserService;
use App\Service\RoleService;
use App\Request\ManagerRequest;
use Hyperf\Validation\Rule;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
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
class ManagerController extends AbstractController
{
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
    public function index(RequestInterface $request, UserService $service)
    {
        $page = $request->input('page') ? intval($request->input('page'), 10) : 1;
        $users = $service->getList($page, User::PAGE_PER);
        $total = $service->allCount();
        $data['last_page'] = ceil($total / User::PAGE_PER);
        if ($total == 0) {
            $data['last_page'] = 1;
        }
        $data['total'] = $total;
        $data['datas'] = $users;
        $data['page'] = $page;
        $data['step'] = User::PAGE_PER;
        $path = '/admin/manager/index';
        $data['next'] = $path . '?page=' . ($page + 1);
        $data['prev'] = $path . '?page=' . ($page - 1);
        $data['navbar'] = trans('default.manager_control.manager_control');
        $data['user_active'] = 'active';

        return $this->render->render('admin.manager.index', $data);
    }

    /**
     * @RequestMapping(path="store", methods={"POST"})
     */
    public function store(ManagerRequest $request, ResponseInterface $response, UserService $service): PsrResponseInterface
    {
        $data['id'] = $request->input('id') ? $request->input('id') : null;
        $data['name'] = $request->input('name');
        $data['role_id'] = $request->input('role_id');
        $data['password'] = $request->input('password');
        $service->storeUser($data);
        return $response->redirect('/admin/manager/index');
    }

    /**
     * @RequestMapping(path="create", methods={"get"})
     */
    public function create(RoleService $roleService)
    {
        $data['navbar'] = trans('default.manager_control.manager_insert');
        $data['user'] = new User();
        $data['roles'] = $roleService->getAll();
        $data['user_active'] = 'active';
        return $this->render->render('admin.manager.form', $data);
    }

    /**
     * @RequestMapping(path="edit", methods={"get"})
     */
    public function edit(RequestInterface $request, UserService $service,RoleService $roleService)
    {
        $id = $request->input('id');
        $data['user'] = $service->findUser(intval($id));
        $data['navbar'] = trans('default.manager_control.manager_update');
        $data['user_active'] = 'active';
        $data['roles'] = $roleService->getAll();
        return $this->render->render('admin.manager.form', $data);
    }

    /**
     * @RequestMapping(path="delete", methods={"POST"})
     */
    public function delete(RequestInterface $request, ResponseInterface $response, UserService $service): PsrResponseInterface
    {
        $service->deleteUser($request->input('id'));
        return $response->redirect('/admin/manager/index');
    }
}
