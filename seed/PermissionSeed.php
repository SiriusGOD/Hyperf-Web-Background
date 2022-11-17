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
class PermissionSeed implements BaseInterface
{
    public function up(): void
    {
        $permissions = [
            # 角色
            'roles' => [
                'role-index',
                'role-create',
                'role-edit',
                'role-delete',
                'role-store',
            ],
            # 後台管理員
            'manager' => [
                'manager-index',
                'manager-create',
                'manager-edit',
                'manager-delete',
                'manager-store',
            ],
            # 廣告管理
            'advertisement' => [
                'advertisement-index',
                'advertisement-create',
                'advertisement-edit',
                'advertisement-expire',
                'advertisement-store',
            ],
            # 廣告圖表管理
            'advertisementcount' => [
                'advertisementcount-index',
                'advertisementcount-list',
            ],
            # SEO管理
            'seo' => [
                'seo-index',
                'seo-create',
                'seo-edit',
                'seo-delete',
                'seo-store',
            ],
            # 用戶對應多站管理
            'usersite' => [
                'usersite-index',
                'usersite-create',
                'usersite-edit',
                'usersite-delete',
                'usersite-store',
            ],
            # 多站管理
            'site' => [
                'site-index',
                'site-create',
                'site-edit',
                'site-delete',
                'site-store',
            ],
            # 入口圖標管理
            'icon' => [
                'icon-index',
                'icon-create',
                'icon-edit',
                'icon-expire',
                'icon-store',
            ],
            # 入口圖標圖表管理
            'iconcount' => [
                'iconcount-index',
                'iconcount-list',
            ],
            # 分享圖標管理
            'share' => [
                'share-index',
                'share-create',
                'share-edit',
                'share-status',
                'share-store',
            ],
            # 分享圖標圖表管理
            'sharecount' => [
                'sharecount-index',
            ],
            # 用戶活躍數圖表管理
            'visitoractivity' => [
                'visitoractivity-index',
            ],
            # 跑馬燈管理
            'newsticker' => [
                'newsticker-index',
                'newsticker-create',
                'newsticker-edit',
                'newsticker-expire',
                'newsticker-store',
            ],
        ];

        foreach ($permissions as $main => $permission) {
            foreach ($permission as $name) {
                $model = new \App\Model\Permission();
                $model->main = $main;
                $model->name = $name;
                $model->save();
            }
        }
    }

    public function down(): void
    {
        \App\Model\Permission::truncate();
    }

    public function base(): bool
    {
        return true;
    }
}
