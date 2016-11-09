<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wechat extends Model
{
    public $id;
    public $openid;
    public $unionid;
    public $nickname;
    public $sex;
    public $city;
    public $province;
    public $headimgurl;
}
