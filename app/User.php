<?php

namespace App;

use App\Notifications\VerifyApiEmail;
use App\Services\Responder;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWallet;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\User
 *
 * @property int                                                        $id
 * @property string|null                                                $name
 * @property string|null                                                $username
 * @property string                                                     $email
 * @property string|null                                                $mobile
 * @property string|null                                                $biography
 * @property string|null                                                $birthday
 * @property string|null                                                $profession
 * @property string|null                                                $gender
 * @property string|null                                                $avatar
 * @property string|null                                                $password
 * @property string|null                                                $remember_token
 * @property string|null                                                $verification_code
 * @property Carbon|null                                                $email_verified_at
 * @property Carbon|null                                                $created_at
 * @property Carbon|null                                                $updated_at
 * @property string|null                                                $deleted_at
 * @property-read Collection|Article[]                                  $articles
 * @property-read int|null                                              $articles_count
 * @property-read Collection|Role[]                                     $roles
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null                                              $notifications_count
 * @property-read Collection|Permission[]                               $permissions
 * @property-read int|null                                              $permissions_count
 * @property-read int|null                                              $roles_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User permission( $permissions )
 * @method static Builder|User query()
 * @method static Builder|User role( $roles, $guard = null )
 * @method static Builder|User whereAvatar( $value )
 * @method static Builder|User whereBiography( $value )
 * @method static Builder|User whereBirthday( $value )
 * @method static Builder|User whereCreatedAt( $value )
 * @method static Builder|User whereDeletedAt( $value )
 * @method static Builder|User whereEmail( $value )
 * @method static Builder|User whereEmailVerifiedAt( $value )
 * @method static Builder|User whereGender( $value )
 * @method static Builder|User whereId( $value )
 * @method static Builder|User whereMobile( $value )
 * @method static Builder|User whereName( $value )
 * @method static Builder|User wherePassword( $value )
 * @method static Builder|User whereProfession( $value )
 * @method static Builder|User whereRememberToken( $value )
 * @method static Builder|User whereUpdatedAt( $value )
 * @method static Builder|User whereUsername( $value )
 * @method static Builder|User whereVerificationCode( $value )
 * @mixin Eloquent
 * @property-read Collection|Article[]                                  $bookmarks
 * @property-read int|null                                              $bookmarks_count
 * @property-read VerificationCode                                      $verificationCode
 */
class User extends Authenticatable implements JWTSubject, MustVerifyEmail, Wallet
{


    protected string $guard_name = 'api';

    use Notifiable, HasRoles, HasWallet;


    protected $fillable = [
        'name', 'username', 'email', 'password', 'verification_token', 'email_verified_at',
    ];


    protected $hidden = [
        'password', 'remember_token', 'verification_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $responder;


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'http://localhost:8000/api/v1/auth/email/verify/5?expires=1586735223&verification_token
            =CBDjffJnG1oVIeChhmXdmEjB0mL0rFXL&signature=715fde2c19d4dc860cf6478da6ef243f32eda011b
            ee84477d99d1fb72d0a689f](http://localhost:8000/api/v1/auth/email/verify/5?expires=158
            6735223&verification_token=CBDjffJnG1oVIeChhmXdmEjB0mL0rFXL&signature=715fde2c19d4dc8
            60cf6478da6ef243f32eda011bee84477d99d1fb72d0a689f)',
        ];
    }


    public function sendVerificationCodeEmail()
    {
        $this->notify( new VerifyApiEmail ); // my notification
    }


    /**
     * @return HasMany
     */
    public function articles(): HasMany
    {
        return $this->hasMany( Article::class );
    }

    /**
     * @return MorphToMany
     */
    public function bookmarks(): MorphToMany
    {
        return $this->morphedByMany( Article::class, 'bookmarkable', 'bookmarks' );
    }

    public function verification_code()
    {
        return $this->hasOne( VerificationCode::class );
    }
}
