<?php
/**
 * @author  Thiago Bruno <thiago.bruno@birdy.studio>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
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
