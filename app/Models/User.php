<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 * @package App\Models
 * @version January 15, 2020, 1:09 pm UTC
 *
 * @property string name
 * @property integer role_id
 * @property string email
 * @property string password
 * @property string remember_token
 */
class User extends Model
{
    use SoftDeletes;

    public $table = 'users';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'name',
        'role_id',
        'email',
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'role_id' => 'integer',
        'email' => 'string',
        'password' => 'string',
        'remember_token' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'role_id' => 'required',
        'email' => 'required',
        'password' => 'required'
    ];

    /**Get the transcation for the user. */

    public function transactions(){
        return $this->hasMany('App\Models\Transaction');

    }

    /**Get the user role */
    
    public function role(){
        return $this->belongsTo('App\Models\Role');

    }

    /**Get the qrcodes for the user. */

    public function qrcodes(){
        return $this->hasMany('App\Models\Qrcode');

    }

    public function account_histories(){
        return $this->hasMany('App\Models\AccountHistory');

    }

    //get the account record associated with the user.

    public function account(){
        return $this->hasOne('App\Models\Account');
    }


    
}
