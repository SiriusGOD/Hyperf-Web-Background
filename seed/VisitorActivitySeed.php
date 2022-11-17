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
class VisitorActivitySeed implements BaseInterface
{
    public function up(): void
    {
        $model = new \App\Model\VisitorActivity();
        $model->ip = '127.0.0.1';
        $model->site_id = 1;
        $model->visit_date = '2022-10-27';
        $model->created_at = '2022-10-27 00:00:00';
        $model->save();

        $model = new \App\Model\VisitorActivity();
        $model->ip = '127.0.0.2';
        $model->site_id = 1;
        $model->visit_date = '2022-10-27';
        $model->created_at = '2022-10-27 00:00:00';
        $model->save();

        $model = new \App\Model\VisitorActivity();
        $model->ip = '127.0.0.1';
        $model->site_id = 1;
        $model->visit_date = '2022-10-28';
        $model->created_at = '2022-10-28 00:00:00';
        $model->save();

        $model = new \App\Model\VisitorActivity();
        $model->ip = '127.0.0.2';
        $model->site_id = 1;
        $model->visit_date = '2022-10-28';
        $model->created_at = '2022-10-28 00:00:00';
        $model->save();

        $model = new \App\Model\VisitorActivity();
        $model->ip = '127.0.0.1';
        $model->site_id = 2;
        $model->visit_date = '2022-10-27';
        $model->created_at = '2022-10-27 00:00:00';
        $model->save();

        $model = new \App\Model\VisitorActivity();
        $model->ip = '127.0.0.3';
        $model->site_id = 2;
        $model->visit_date = '2022-10-27';
        $model->created_at = '2022-10-27 00:00:00';
        $model->save();

        $model = new \App\Model\VisitorActivity();
        $model->ip = '127.0.0.2';
        $model->site_id = 2;
        $model->visit_date = '2022-10-27';
        $model->created_at = '2022-10-27 00:00:00';
        $model->save();

        $model = new \App\Model\VisitorActivity();
        $model->ip = '127.0.0.1';
        $model->site_id = 2;
        $model->visit_date = '2022-10-28';
        $model->created_at = '2022-10-28 00:00:00';
        $model->save();

        $model = new \App\Model\VisitorActivity();
        $model->ip = '127.0.0.2';
        $model->site_id = 2;
        $model->visit_date = '2022-10-28';
        $model->created_at = '2022-10-28 00:00:00';
        $model->save();
    }

    public function down(): void
    {
        \App\Model\VisitorActivity::truncate();
    }

    public function base(): bool
    {
        return false;
    }
}
