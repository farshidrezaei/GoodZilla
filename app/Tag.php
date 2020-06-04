<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use App\Article;

/**
 * App\Tag
 *
 * @property int                       $id
 * @property string                    $title
 * @property Carbon|null               $created_at
 * @property Carbon|null               $updated_at
 * @property-read Collection|Article[] $articles
 * @property-read int|null             $articles_count
 * @method static Builder|Tag newModelQuery()
 * @method static Builder|Tag newQuery()
 * @method static Builder|Tag query()
 * @method static Builder|Tag whereCreatedAt( $value )
 * @method static Builder|Tag whereId( $value )
 * @method static Builder|Tag whereTitle( $value )
 * @method static Builder|Tag whereUpdatedAt( $value )
 * @mixin Eloquent
 */
class Tag extends Model
{
    protected $guarded = [];

    public function articles(): MorphToMany
    {
        return $this->morphedByMany( Article::class, 'taggable' );
    }
}
