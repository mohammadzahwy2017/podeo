<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static Podcast create(array $array)
 * @method static Builder paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
 * @property int id
 * @property string title
 * @property string about
 * @property boolean published
 * @property string created_at
 * @property string updated_at
 * @property Episode[] episodes
 */
class Podcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'about',
        'published',
    ];

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class, 'podcast_id', 'id');
    }
}
