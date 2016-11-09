<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

class AuthBaseController extends Controller
{
    public function Log($managerId,$msg,$ip='0.0.0.0'){
        Log::create([
            'manager_id'    => $managerId,
            'msg'   =>  $msg,
            'ip'    =>  $ip
        ]);
    }
    protected function getCode($type,$data){
        $url = env('API_PASSPORT_AUTH_URL')
            ."?client_id=".env('CLIENT_ID')
            ."&redirect_uri=".env('REDIRECT_URI')
            ."&response_type=code";

        $params = [
            'type'  => $type,
            'username' => $data['username'],
            'password' => $data['password']
        ];

        $output = $this->http($url,'POST',$params);
        return $output;
    }

    protected function getUserInfo($token){
        $url = 'http://passport.timelink.cn/api/user/info';
        $params = [
            'access_token' =>$token
        ];

        $re = $this->http($url,'POST',$params);
        return $re;
    }

    protected function getAccessToken($code){
        $url = 'http://passport.timelink.cn/api/auth/token';

        $params = [
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'redirect_uri' => env('REDIRECT_URI'),
            'grant_type' =>'authorization_code',
            'code'=>$code
        ];

        $re = $this->http($url,'POST',$params);
        return $re;
    }

    protected function createUser($token,$data){
        $url = 'http://passport.timelink.cn/api/user/create';
        $params = [
            'username' => $data['username'],
            'password' => $data['password'],
            'nickname' =>  array_key_exists('nickname',$data)?$data['nickname']:null,
            'avatar' => array_key_exists('avatar',$data)?$data['avatar']:null,
            'access_token'=>$token
        ];

        $re = $this->http($url,'POST',$params);
        return $re;
    }

    protected function importUser($token,$data){
        $url = 'http://passport.timelink.cn/api/user/import';
        $params = [
            'data'  => $data,
            'access_token'=>$token
        ];

        $re = $this->http($url,'POST',$params);
        return $re;
    }

    protected function editUser($token,$data){
        $url = 'http://passport.timelink.cn/api/user/edit';
        $params = [
            'passport_id' => $data['passport_id'],
            'password' => $data['password'],
            'access_token'=>$token
        ];

        $re = $this->http($url,'POST',$params);
        return $re;
    }

    protected function passwordModify($data){
        $url = 'http://passport.timelink.cn/api/user/password';
        $re = $this->http($url,'POST',$data);
        return $re;
    }


    private function http($url,$method,$params){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt ( $ch ,  CURLOPT_POSTFIELDS ,  $params );
        }
        $output = curl_exec($ch);//输出内容
        curl_close($ch);
        return $output;
    }
}
