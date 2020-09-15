<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AccountHistory
 * @package App\Models
 * @version January 17, 2020, 7:54 am UTC
 *
 * @property integer account_id
 * @property integer user_id
 * @property string message
 */
class AccountHistory extends Model
{
    use SoftDeletes;

    public $table = 'account_histories';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'account_id',
        'user_id',
        'message'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'account_id' => 'integer',
        'user_id' => 'integer',
        'message' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'account_id' => 'required',
        'user_id' => 'required',
        'message' => 'required'
    ];

    //get the user that own this account history.

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    

    
}
