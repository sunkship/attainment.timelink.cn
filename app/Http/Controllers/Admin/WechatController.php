<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class WechatController extends Controller
{
    /**
     * 微信登陆回调
     * @param Request $request
     */
    public function loginAction(Request $request){
        $urlCode = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx6a4516f5c033a3f3&redirect_uri=http://attainment.timelink.cn/get_code&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
        $re = $this->http($urlCode,'POST');
    }
    
    public function receiveWechatCode(Request $request){
        $code = $request->code;
        $access_token = $this->getAccessToken($code);
        $this->wechat($access_token,$request);
    }

    private function wechat($access_token,$request){
        if(!empty($access_token)){
            $token_info = json_decode($access_token,true);
            $unionid = $token_info['unionid'];
            $openid = $token_info['openid'];

            $user = User::where("unionid",$unionid)->first();
            if(!empty($user)){
                Session::set('userId',$user->id);
                response(redirect('/wall'));
            }else{
                response(redirect('/login'));
            }
            if(!empty($this->refreshAccessToken())){
                if($this->AuthAccessToken($request,$openid)){
                    $user_info_json = $this->getuserinfo($request,$openid);
                    $user_info_array = json_decode($user_info_json,true);
                    $user = User::updateOrCreate(['openid','unionid'],[
                        'nickname'  =>$user_info_array['nickname'],
                        'openid'    => $user_info_array['openid'],
                        'unionid'   => $user_info_array['unionid'],
                        'header_url'=> $user_info_array['headimgurl'],
                        'gender'    => $user_info_array['sex'],
                        'city'      => $user_info_array['city'],
                        'province'  => $user_info_array['province'],
                    ]);

                    if(!$user->save()){
                        return response(array(
                            'error code'=> 1001,
                            'message'   => '发生未知错误，请重试'
                        ));
                    }
                    return redirect('/wall');
                } else return response(array(
                    'error code'=> 1002,
                    'message'   => '获取token失败，请重试'
                ));
            }else return response(array(
                'error code'=> 1003,
                'message'   => '无法获取refreshToken，请重试'
            ));

        }else{
            return response(array(
                'error code'=> 1002,
                'message'   => '获取token失败，请重试'
            ));
        }
    }

    /**
     * @param Request $request
    */
    public function wxAction($request){
        $code = $request->get('code');

        $this->applyNewWX('wxc50e59d3bea57416','71c4d4aa5eeb8a9b2c2efdd0fc3e1a28');

        $access_token = $this->getAccessToken($code);

        $authParams = array(
            'client_id' => 'attainment',
            'redirect_uri' => 'http://attainment.timelink.cn/auth/callback',
            'response_type' => 'code'
        );

        $url_suffix = http_build_query($authParams);

        $this->wechat($access_token,$url_suffix,$request);
    }


    public function showAction(){

    }

    public function bindingAction(){

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

    /**
     * 新建weixin对象
     */
    public function applyNewWX($appid=null,$secret=null,$access_token = NULL, $openid = NULL, $refresh_token = NULL){
        if(is_null($appid) && is_null($secret)){
            $wechat_array = require_once ('../config/Wechat.php');
            $this->AppID         = $wechat_array['WECHAT_APPID'];
            $this->AppSecret     = $wechat_array['WECHAT_SECRET'];
        }else{
            $this->AppID         = $appid;
            $this->AppSecret     = $secret;
        }

        $this->access_token  = $access_token;
        $this->openid        = $openid;
        $this->refresh_token = $refresh_token;
    }

    /**获取access_token代码
     * @param null $code
     * @return mixed
     */
    public function getAccessToken($code = NULL){
        $url = $this->api."/sns/oauth2/access_token?";
        $param = array(
            'appid'      => $this->AppID,
            'secret'     => $this->AppSecret,
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
        $this->access_token = isset($arr['access_token'])?$arr['access_token']:'';
        return $re;
    }

    /**获取用户信息
     * @param Request $request,Integer $openid
     * @return mixed
     */
    public function getuserinfo($request,$openid){
        $url = $this->api.'/sns/userinfo?';
        $param = array(
            'access_token' => $request->get('access_token'),
            'openid'       => $openid,
        );
        return $this->oAuthRequest($url, 'GET', $param);
    }

    /**
     * 检测acces_token有效期
     * @param Request $request，$openid
     * @return string
     */
    public function AuthAccessToken($request,$openid){
        $url = $this->api."/sns/auth?";
        $param = array(
            'access_token' => $request->get('access_token'),
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