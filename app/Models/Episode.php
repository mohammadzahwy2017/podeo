<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property int podcast_id
 * @property string title
 * @property string description
 * @property float duration
 * @property boolean published
 * @property string created_at
 * @property string updated_at
 * @property Podcast podcast
 * @method static Builder paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
 * @method static create(array $array)
 */
class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'published',
        'podcast_id'
    ];

    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class, 'podcast_id', 'id');
    }
}
