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
class ShareCountSeed implements BaseInterface
{
    public function up(): void
    {
        $model = new \App\Model\ShareCount();
        $model->share_id = 1;
        $model->ip = '127.0.0.1';
        $model->click_date = '2022-10-22';
        $model->save();

        $model = new \App\Model\ShareCount();
        $model->share_id = 1;
        $model->ip = '127.0.0.2';
        $model->click_date = '2022-10-22';
        $model->save();

        $model = new \App\Model\ShareCount();
        $model->share_id = 1;
        $model->ip = '127.0.0.3';
        $model->click_date = '2022-10-22';
        $model->save();
    }

    public function down(): void
    {
        \App\Model\ShareCount::truncate();
    }

    public function base(): bool
    {
        return false;
    }
}
