<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Like
 *
 * @property int $user_id
 * @property int $likeable_id
 * @property string $likeable_type
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Like newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Like newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Like query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Like whereLikeableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Like whereLikeableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Like whereUserId($value)
 * @mixin \Eloquent
 */
class Like extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function user()
    {
        return $this->hasOne('App\User');
    }
}
