<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\User;
use App\Wechat;
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
        $code =$request->get('code');
        $access_token = $this->getAccessToken($code);
        $url_suffix = Session::get('authParams');
        $this->wechat($access_token,$url_suffix,$request);
    }

    private function wechat($access_token,$url_suffix,$request){
        if(!empty($access_token)){
            $token_info = json_decode($access_token,true);
            $unionid = $token_info['unionid'];
            $openid = $token_info['openid'];
            $wechat = Wechat::where('unionid',$unionid)->first();

            if($wechat != false){
                $user = User::where("wechat_id",$wechat->id)->first();
                if($user != false){
                    Session::set('userId',$user->id);
                    response(redirect('/auth?'.$url_suffix));
                }else{
                    Session::set('wechatId',$wechat->id);
                    response(redirect('/binding?'.$url_suffix));
                }
            }else{
                if(!empty($this->refreshAccessToken())){
                    if($this->AuthAccessToken($request,$openid)){
                        $user_info_json = $this->getuserinfo($request,$openid);
                        $user_info_array = json_decode($user_info_json,true);
                        $exist_wechat = Wechat::where("openid",$openid)->first();

                        if(!empty($exist_wechat)){
                            $exist_wechat->unionid = $unionid;
                            if(!$exist_wechat->save()){
                                var_dump('error');
                            }else{
                                $user = User::where('wechat_id',$exist_wechat->id)->first();
                            }
                        }else{
                            $wechat_model = new Wechat();
                            $wechat_model->nickname = $user_info_array['nickname'];
                            $wechat_model->openid = $user_info_array['openid'];
                            $wechat_model->unionid = $user_info_array['unionid'];
                            $wechat_model->headimgurl = $user_info_array['headimgurl'];
                            $wechat_model->sex = $user_info_array['sex'];
                            $wechat_model->city = $user_info_array['city'];
                            $wechat_model->province = $user_info_array['province'];
                            $wechat_model->created_time = time();

                            if(!$wechat_model->save()){
                                var_dump('error');
                            }else{
                                $user = User::where('wechat_id',$wechat_model->id)->first;
                            }
                        }

                        if(!empty($user)){
                            Session::set('userId',$user->id);
                            response(redirect('/auth?'.$url_suffix));
                        }else{
                            Session::set('wechatId',$user->wechat_id);
                            response(redirect('/binding?'.$url_suffix));
                        }
                    }
                }
            }
        }else{
            echo "error";
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
