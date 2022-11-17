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
namespace App\Model;

/**
 * @property int $id
 * @property int $advertisements_id
 * @property string $ip
 * @property string $click_date
 * @property int $site_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class AdvertisementCount extends Model
{
    public const PAGE_PER = 10;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'advertisement_counts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'advertisements_id' => 'integer', 'site_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class, 'advertisements_id', 'id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
