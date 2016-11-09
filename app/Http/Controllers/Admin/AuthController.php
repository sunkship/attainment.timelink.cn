<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class AuthController extends AuthBaseController
{

    public function getLogin(){
        return view('auth.login');
    }

    public function postLogin(Request $request){
        $data = [
            'username'  => $request->get('username'),
            'password'  => $request->get('password')
        ];

        if(Auth::attempt($data)){
            //dd(Auth::user());
            return redirect('/wall');
        }else{
            Flash::error(trans('front.login_fail'));
            return redirect('/login');
        }

        /*$rules = ['captcha' => 'required|captcha'];

        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails()){
            Flash::error(trans('admin.code_error'));
            return back()->withInput();
        }*/
        /*$result = $this->getCode('user',$data);
        $arr = json_decode($result,true);
        if($arr['status_code'] != 1){
            Flash::error($arr['message']);
            return back()->withInput();
        }else{
            $code = $arr['code'];
            $tokenInfo = $this->getAccessToken($code);
            $tokenInfoArr = json_decode($tokenInfo,true);

            if(isset($tokenInfoArr['status_code'])){
                Flash::error($tokenInfoArr['message']);
                return back()->withInput();
            }else{
                $userInfo = $this->getUserInfo($tokenInfoArr['access_token']);
                $userInfoArray = json_decode($userInfo,true);
                $passport_id = $userInfoArray['owner_id'];
                if(Auth::guard('manager')->attempt(['passport_id'=>$passport_id,'password'=>''])){
                    if(Auth::guard('manager')->user()->is('manager') || Auth::guard('manager')->user()->is('ambassador')){
                        $request->session()->put('school_id',Auth::guard('manager')->user()->manager->school_id);
                        $this->Log(Auth::guard('manager')->user()->manager->id,trans('admin.login_success'),$request->getClientIp());
                    }else if(Auth::guard('manager')->user()->is('admin')){
                        $this->Log('',trans('admin.login_success'),$request->getClientIp());
                    }else{
                        Flash::error(trans('admin.login_deny'));
                        return back()->withInput();
                    }

                    $user = Auth::guard('manager')->user();
                    $user->last_login_ip = $request->getClientIp();
                    $user->save();

                    $request->session()->put('tokenInfo',$tokenInfoArr);
                    return redirect('/admin');
                }else{
                    Flash::error(trans('admin.user_not_register'));
                    return back()->withInput();
                }
            }
        }*/

    }


    public function getLogout(Request $request)
    {
//        $manager_guard = Auth::guard('manager');
//        if($manager_guard->guest()){
//            return redirect('/admin');
//        }
//        if(!$manager_guard->user()->is('admin')) {
//            $this->Log($manager_guard->user()->manager->id,trans('admin.logout'),$request->getClientIp());
//        }else{
//            $this->Log('',trans('admin.logout'),$request->getClientIp());
//        }
        Auth::logout();
//
//        $request->session()->clear();
        return redirect('/login');
    }

    public function getPassword(){
        return view('admin.auth.password');
    }

    public function postPassword(Requests\PasswordRequest $request){
        $tokenInfo = session('tokenInfo');

        $data['origin_password']    = $request->get('origin_password');
        $data['password']   = $request->get('password');
        $data['passport_id']    = Auth::guard('manager')->user()->passport_id;
        $data['access_token']   = $tokenInfo['access_token'];

        $result  = parent::passwordModify($data);
        $resultArr = json_decode($result,true);

        if($resultArr['status_code'] != 1){
            Flash::error($this->transError($resultArr['status_code']));
            return back()->withInput();
        }else{
            Flash::message(trans('admin.change_password_success'));
            return view('auth.password');
        }
    }

    private function transError($code){
        $message = '';
        switch ($code){
            case 1:
                $message = trans('front.change_password_success');
                break;
            case 4002:
                $message = trans('front.modify_retry');
                break;
            case 4005:
                $message = trans('front.source_password_error');
                break;
            case 4003:
            case 4001:
            case 4004:
                $message = trans('front.authorize_error');
                break;
            default:
                break;
        }
        return $message;
    }

}
