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
 * @property string $code
 * @property string $ip
 * @property string $fingerprint
 * @property int $status
 * @property int $site_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Share extends Model
{
    public const PAGE_PER = 10;

    public const STATUS = [
        'undone' => 0,
        'done' => 1,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shares';

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
    protected $casts = ['id' => 'integer', 'status' => 'integer', 'site_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
