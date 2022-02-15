<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'anime_entries';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'myanimelist_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Get the resources that owns the platform.
     */
    public function resource()
    {
        return $this->hasMany(Resource::class);
    }
}
