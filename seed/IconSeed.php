<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @see     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
class IconSeed implements BaseInterface
{
    public function up(): void
    {
        $model = new \App\Model\Icon();
        $model->name = 'test';
        $model->image_url = '/icon/avatar.png';
        $model->url = 'www.google.com.tw';
        $model->position = '1';
        $model->start_time = '2022-10-20 00:00:00';
        $model->end_time = '2022-11-20 00:00:00';
        $model->buyer = '提姆';
        $model->expire = 0;
        $model->site_id = 1;
        $model->save();

        $model = new \App\Model\Icon();
        $model->name = 'test2';
        $model->image_url = '/icon/avatar.png';
        $model->url = 'www.google2.com.tw';
        $model->position = '1';
        $model->start_time = '2022-10-20 00:00:00';
        $model->end_time = '2022-11-20 00:00:00';
        $model->buyer = '提姆';
        $model->expire = 0;
        $model->site_id = 2;
        $model->save();
    }

    public function down(): void
    {
        \App\Model\Icon::truncate();
    }

    public function base(): bool
    {
        return false;
    }
}
