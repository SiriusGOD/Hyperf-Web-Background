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
class ShareSeed implements BaseInterface
{
    public function up(): void
    {
        $model = new \App\Model\Share();
        $model->code = 'fwe123';
        $model->ip = '127.0.0.1';
        $model->fingerprint = 'dedwefgww';
        $model->status = 0;
        $model->site_id = 1;
        $model->save();
    }

    public function down(): void
    {
        \App\Model\Share::truncate();
    }

    public function base(): bool
    {
        return false;
    }
}
