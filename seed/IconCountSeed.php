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
class IconCountSeed implements BaseInterface
{
    public function up(): void
    {
        $model = new \App\Model\IconCount();
        $model->date = '2022-10-30';
        $model->icon_name = 'test';
        $model->icon_id = 1;
        $model->count = 2;
        $model->site_id = 1;
        $model->save();

        $model = new \App\Model\IconCount();
        $model->date = '2022-10-31';
        $model->icon_id = 1;
        $model->icon_name = 'test';
        $model->count = 3;
        $model->site_id = 1;
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
