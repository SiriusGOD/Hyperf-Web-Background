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
 * @property string $name
 * @property string $image_url
 * @property string $url
 * @property int $position
 * @property string $start_time
 * @property string $end_time
 * @property string $buyer
 * @property int $expire
 * @property int $site_id
 * @property int $sort
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Icon extends Model
{
    // 每頁頁數
    public const PAGE_PER = 10;

    public const POSITION = [
        'top' => 1,
        'bottom' => 2,
    ];

    public const EXPIRE = [
        'no' => 0,
        'yes' => 1,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'icons';

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
    protected $casts = ['id' => 'integer', 'position' => 'integer', 'expire' => 'integer', 'site_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'sort' => 'integer'];

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id', 'id')->withTrashed();
    }
}
