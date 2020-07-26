<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * Class UserSave
 *
 *
 * @author  Thiago Bruno <thiago.bruno@birdy.studio>
 */
class UserSave extends Model
{
    protected $table = "user_save";

    protected $fillable = [
        'user_id',
        'value'
    ];

    protected $casts = [
        'value' => 'Array'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
