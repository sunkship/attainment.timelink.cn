<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['passport_id','password','nickname','account','header_url','last_login_ip','remember_token',
        'openid','unionid','gender','city','province'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**check if user exist
     * @param $username
     * @return boolean
     */
    public function checkUser($username){
        $user = User::findFirst("username = '$username'");
        if($user)
            return false;
        return true;
    }

}
