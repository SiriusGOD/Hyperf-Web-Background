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
class AdvertisementCountSeed implements BaseInterface
{
    public function up(): void
    {
        $model = new \App\Model\AdvertisementCount();
        $model->advertisements_id = 1;
        $model->ip = '127.0.0.1';
        $model->click_date = '2022-11-12';
        $model->site_id = 1;
        $model->save();

        $model = new \App\Model\AdvertisementCount();
        $model->advertisements_id = 1;
        $model->ip = '127.0.0.2';
        $model->click_date = '2022-11-12';
        $model->site_id = 1;
        $model->save();

        $model = new \App\Model\AdvertisementCount();
        $model->advertisements_id = 1;
        $model->ip = '127.0.0.1';
        $model->click_date = '2022-11-13';
        $model->site_id = 1;
        $model->save();

        $model = new \App\Model\AdvertisementCount();
        $model->advertisements_id = 1;
        $model->ip = '127.0.0.3';
        $model->click_date = '2022-11-14';
        $model->site_id = 1;
        $model->save();

        $model = new \App\Model\AdvertisementCount();
        $model->advertisements_id = 2;
        $model->ip = '127.0.0.2';
        $model->click_date = '2022-11-13';
        $model->site_id = 2;
        $model->save();
    }

    public function down(): void
    {
        \App\Model\AdvertisementCount::truncate();
    }

    public function base(): bool
    {
        return false;
    }
}
