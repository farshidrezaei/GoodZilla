<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\VerificationCode
 *
 * @property int       $user_id
 * @property string    $code
 * @property string    $expired_at
 * @property-read User $verificationCode
 * @method static Builder|VerificationCode newModelQuery()
 * @method static Builder|VerificationCode newQuery()
 * @method static Builder|VerificationCode query()
 * @method static Builder|VerificationCode whereCode( $value )
 * @method static Builder|VerificationCode whereExpiredAt( $value )
 * @method static Builder|VerificationCode whereUserId( $value )
 * @mixin Eloquent
 * @property int       $id
 * @property-read User $user
 * @method static Builder|VerificationCode whereId( $value )
 */
class VerificationCode extends Model
{
    public    $timestamps = false;
    protected $guarded    = [];


    protected $dates=['expired_at'];
    public function user(): BelongsTo
    {
        return $this->belongsTo( User::class );
    }
}
