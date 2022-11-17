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
class SeoKeywordSeed implements BaseInterface
{
    public function up(): void
    {
        $model = new \App\Model\SeoKeyword();
        $model->keywords = '二次元、自拍、美女主播、摄像头、福利姬、抖音网红、小说影视、制服、按摩、逼哩逼哩、变装、野外、情趣内衣、小秘书、诱惑、同城交友、人妻绿帽、微博女神、重口味、小红书';
        $model->site_id = 1;
        $model->save();

        $model = new \App\Model\SeoKeyword();
        $model->keywords = '二次元、自拍、美女主播、摄像头、福利姬、抖音网红、小说影视、制服、按摩、逼哩逼哩、变装、野外、情趣内衣、小秘书、诱惑、同城交友、人妻绿帽、微博女神、重口味、小红书';
        $model->site_id = 2;
        $model->save();
    }

    public function down(): void
    {
        \App\Model\SeoKeyword::truncate();
    }

    public function base(): bool
    {
        return false;
    }
}
