<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Like
 *
 * @property int       $user_id
 * @property int       $likeable_id
 * @property string    $likeable_type
 * @property-read User $user
 * @method static Builder|Like newModelQuery()
 * @method static Builder|Like newQuery()
 * @method static Builder|Like query()
 * @method static Builder|Like whereLikeableId( $value )
 * @method static Builder|Like whereLikeableType( $value )
 * @method static Builder|Like whereUserId( $value )
 * @mixin Eloquent
 */
class Like extends Model
{
    public    $timestamps = false;
    protected $guarded    = [];

    public function user(): HasOne
    {
        return $this->hasOne( 'App\User' );
    }
}
