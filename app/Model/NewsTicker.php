<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $id 
 * @property string $name 
 * @property string $detail 
 * @property string $start_time 
 * @property string $end_time 
 * @property string $buyer 
 * @property int $expire 
 * @property int $site_id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class NewsTicker extends Model
{
    public const PAGE_PER = 10;
    public const EXPIRE = [
        'no' => 0,
        'yes' => 1,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'news_tickers';
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
    protected $casts = ['id' => 'integer', 'expire' => 'integer', 'site_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id', 'id')->withTrashed();
    }
}
