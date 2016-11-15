<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class WechatController extends Controller
{
    protected $WechatInfo = [
        "WECHAT_APPID" => "wxc50e59d3bea57416",
        "WECHAT_SECRET" => "71c4d4aa5eeb8a9b2c2efdd0fc3e1a28"
    ];

    /**
     * 微信登陆回调
     */
    public function loginAction(Request $request){
        $redirectURL = 'http%3A%2F%2Fattainment.timelink.cn%2FWechatLogin';
        $urlCode = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->WechatInfo['WECHAT_APPID']
            .'&redirect_uri='.$redirectURL.'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';

        $code = $request->get('code');
        if(empty($code)) return redirect($urlCode);
        else return $this->receiveWechatCode($code);
    }

    public function receiveWechatCode($code){
        $access_token = $this->access_token = $this->getAccessToken($code);
        $re = $this->wechat($access_token);
        if($re){
            return redirect('/wall');
        }else{
            return redirect('/login');
        }
    }

    protected function wechat($access_token){
        if(!empty($access_token)){
            $token_info = json_decode($access_token,true);
            $openid = $token_info['openid'];

            if(!empty($this->refreshAccessToken())){
                dd($this->access_token,$access_token);
                if($this->AuthAccessToken($this->access_token,$openid)){
                    $user_info_json = $this->getuserinfo($this->access_token,$openid);
                    $user_info_array = json_decode($user_info_json,true);
                    $user = User::where("openid",$user_info_array['openid'])
                        ->where('unionid',$user_info_array['unionid'])->where('id','<>',1)->first();
                    if(!empty($user)){
                        $user->username     = $user_info_array['nickname'];
                        $user->header_url   = $user_info_array['headimgurl'];
                        $user->gender       = $user_info_array['sex'];
                        $user->city         = $user_info_array['city'];
                        $user->province     = $user_info_array['province'];
                        $user->password     = bcrypt('123123');

                        if(!$user->save()){
                            return array(
                                'error code'=> 1001,
                                'message'   => '发生未知错误，请重试'
                            );
                        }
                    }else{
                        $user = User::create([
                            'username'  => $user_info_array['nickname'],
                            'openid'    => $user_info_array['openid'],
                            'unionid'   => $user_info_array['unionid'],
                            'header_url'=> $user_info_array['headimgurl'],
                            'gender'    => $user_info_array['sex'],
                            'city'      => $user_info_array['city'],
                            'province'  => $user_info_array['province'],
                            'password'  => bcrypt('123123'),
                        ]);
                    }
                    Session::set('userId',$user->id);
                    if($this->signIn($user->username,"123123")){
                        return true;
                    }else{
                        Flash::error(trans('front.login_fail'));
                        return array(
                            'error code'=> 1004,
                            'message'   => '用户名或密码错误，请重试'
                        );
                    }
                }else return array(
                    'error code'=> 1002,
                    'message'   => '获取token失败，请重试'
                );
            }else return array(
                'error code'=> 1003,
                'message'   => '无法获取refreshToken，请重试'
            );

        }else{
            return array(
                'error code'=> 1002,
                'message'   => '获取token失败，请重试'
            );
        }
    }

    private function signIn($username,$password){
        $data = [
            'username'  => $username,
            'password'  => $password,
        ];
        if(Auth::attempt($data)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 微信应用ID
     * @var string
     */
    private $AppID;

    /**
     * 微信应用Secret
     * @var string
     */
    private $AppSecret;

    /**
     * 微信api 地址
     * */
    private $api = 'https://api.weixin.qq.com';

    /**
     * curl连接超时时长
     * @var integer
     */
    private $connecttimeout = 30;

    /**
     * curl请求时长
     * @var integer
     */
    private $timeout = 30;

    /**private data for Wechat
    */
    private $access_token;
    private $openid;
    private $refresh_token;

    /**获取access_token代码
     * @param null $code
     * @return mixed
     */
    public function getAccessToken($code = NULL){
        $url = $this->api."/sns/oauth2/access_token?";
        $param = array(
            'appid'      => $this->WechatInfo['WECHAT_APPID'],
            'secret'     => $this->WechatInfo['WECHAT_SECRET'],
            'code'       => $code,
            'grant_type' => 'authorization_code',
        );

        $re = $this->oAuthRequest($url, 'GET', $param);
        $arr = json_decode($re,true);
        $this->refresh_token = isset($arr['refresh_token'])?$arr['refresh_token']:'';
        $this->openid = isset($arr['openid'])?$arr['openid']:'';
        return $re;
    }

    /**
     * 刷新access_token
     * @return string
     */
    public function refreshAccessToken(){
        $url = $this->api."/sns/oauth2/refresh_token?";

        $param = array(
            'appid'         => $this->AppID,
            'secret'        => $this->AppSecret,
            'refresh_token' => $this->refresh_token,
            'grant_type'    => 'refresh_token',
        );
        $re = $this->oAuthRequest($url, 'GET', $param);
        $arr = json_decode($re,true);
        dd($arr);
        $this->access_token = isset($arr['access_token'])?$arr['access_token']:'';
        return $re;
    }

    /**获取用户信息
     * @param $access_token,Integer $openid
     * @return mixed
     */
    public function getuserinfo($access_token,$openid){
        $url = $this->api.'/sns/userinfo?';
        $param = array(
            'access_token' => $access_token,
            'openid'       => $openid,
        );
        return $this->oAuthRequest($url, 'GET', $param);
    }

    /**
     * 检测acces_token有效期
     * @param Request $request，$openid
     * @return string
     */
    public function AuthAccessToken($access_token,$openid){
        $url = $this->api."/sns/auth?";
        $param = array(
            'access_token' => $access_token,
            'openid'       => $openid,
        );
        $re = $this->oAuthRequest($url, 'GET', $param);
        $arr = json_decode($re,true);
        if(isset($arr['errcode'])){
            return $arr['errcode']==0?true:false;
        }else{
            return false;
        }
    }

    /**
     * 发起http请求
     */
    private function oAuthRequest($url, $method, $parameters){
        $url = $url . http_build_query($parameters);
        return $this->http($url, $method);
    }

    /**
     * Http方法
     *
     */
    private function http($url,$method){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if($method == 'POST')
            curl_setopt($ch, CURLOPT_POST, TRUE);
        $output = curl_exec($ch);//输出内容
        curl_close($ch);
        return $output;
    }
}
