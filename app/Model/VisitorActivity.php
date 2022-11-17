<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $id 
 * @property string $ip 
 * @property int $site_id 
 * @property string $visit_date 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class VisitorActivity extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'visitor_activities';
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
    protected $casts = ['id' => 'integer', 'site_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
