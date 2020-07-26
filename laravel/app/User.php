<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 *
 *
 * @author  Thiago Bruno <thiago.bruno@birdy.studio>
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'codename',
        'name',
        'lastname',
        'email',
        'password',
        'activation_code',
        'passwordNew',
        'passwordExpires',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function getFullNameAttribute()
    {
        return ucfirst(mb_strtolower($this->name)) . " " . ucfirst(mb_strtolower($this->lastname));
    }

    public function save_data()
    {
        return $this->hasOne('App\UserSave', 'user_id');
    }
}
