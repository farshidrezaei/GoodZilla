<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Comment
 *
 * @property int                       $id
 * @property int                       $user_id
 * @property int|null                  $parent_id
 * @property string                    $body
 * @property int                       $commentable_id
 * @property string                    $commentable_type
 * @property Carbon|null               $created_at
 * @property Carbon|null               $updated_at
 * @property-read Collection|Comment[] $children
 * @property-read int|null             $children_count
 * @property-read Model|Eloquent       $commentable
 * @property-read Comment|null         $parent
 * @property-read User                 $user
 * @method static Builder|Comment newModelQuery()
 * @method static Builder|Comment newQuery()
 * @method static Builder|Comment query()
 * @method static Builder|Comment whereBody( $value )
 * @method static Builder|Comment whereCommentableId( $value )
 * @method static Builder|Comment whereCommentableType( $value )
 * @method static Builder|Comment whereCreatedAt( $value )
 * @method static Builder|Comment whereId( $value )
 * @method static Builder|Comment whereParentId( $value )
 * @method static Builder|Comment whereUpdatedAt( $value )
 * @method static Builder|Comment whereUserId( $value )
 * @mixin Eloquent
 */
class Comment extends Model
{

    protected $guarded = [];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo( User::class );
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo( __CLASS__, 'parent_id' );
    }

    public function children(): HasMany
    {
        return $this->hasMany( __CLASS__, 'parent_id', 'id' );
    }

}
