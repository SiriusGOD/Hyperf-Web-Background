<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $id 
 * @property int $share_id 
 * @property string $ip 
 * @property string $click_date
 * @property  int $site_id
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class ShareCount extends Model
{
    public const SUCCESS_COUNT = 3;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'share_counts';
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
    protected $casts = ['id' => 'integer', 'share_id' => 'integer', 'site_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function share()
    {
        return $this->belongsTo(Share::class);
    }
}