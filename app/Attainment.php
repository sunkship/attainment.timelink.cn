<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attainment extends Model
{
    protected $table = 'attainment';
    protected $fillable = ['user_id','target_id','content','url','name','date'];

    protected function User(){
        $this->hasOne('user');
    }
}
