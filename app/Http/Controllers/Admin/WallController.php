<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Attainment;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

class WallController extends Controller
{
    /**show the attainment wall
     * @return mixed
     */
    public function getWall(){
        $urlSet = Attainment::groupby('url')->lists('url');
        $attainments = Attainment::where('user_id',1)->wherein('url',$urlSet)->orderBy('id','desc')->paginate(10);
        return view('wall/attainmentWall', compact('attainments'));
    }

    /**open target page with writing function
     * @param Request $request
     * @return mixed
     */
    public function getTarget(Request $request){
        $target = http_build_query($request->all());
        $target = (urldecode($target));
        $target = substr($target,7);
        $attainments = Attainment::where('url',$target)->get();
        $users = [];
        $old = '';
        if (!empty($attainments))
            foreach($attainments as $attainment) {
                $user = User::find($attainment->user_id);
                if(!empty($user)) {
                    array_push($users, $user);
                    if ($user->id == Auth::user()->id){
                        $old = $attainment->content;
                    }
                }
                else return ($content["未知用户"] = $attainment->content);
            }
        else return ($content["错误"]="获取心得失败");
        return view('wall/targetPage', compact('target','attainments','users','old'));
    }

    /**receive attainment
     * @param Request $request
     * @return mixed
     */
    public function postWrite(Request $request){
        $target = $request->get('target');
        $user = Auth::user();
        $attainment = $request->get('attainment');

        if(empty($user) || $user->id == 1) return redirect('/logout');
        else if(empty($attainment)) return back();
        else if(empty($target)) return back();
        else{
            Attainment::updateOrCreate([
                'user_id'   =>$user->id,
                'url'       =>$target,
            ],[
                'user_id'   =>$user->id,
                'url'       =>$target,
                'content'   =>$attainment,
            ]);
        }
        return redirect('/wall');
    }

    /**get all attainment
     * @param Request $request
     * @return mixed
     */
    public function getTable(Request $request){
        for($i = 0;$i < 30;$i++) $recentDays[$i] = date("Y-m-d",strtotime("-".($i + 1)."days"));

        $users = User::where('id','>',1)->get();
        $attainmentCount = [];
        $index = 0;
        foreach ($users as $user){
            for($i = 0;$i<30;$i++) {
                 $attainments =  Attainment::where('user_id',$user->id)
                     ->where('created_at','>',date("Y-m-d 00:00:00",strtotime("-".($i+1)."days")))
                     ->where('created_at','<',date("Y-m-d 00:00:00",strtotime("-".($i)."days")))->get();
                $attainmentCount[$index][$i] = 0;
                foreach ($attainments as $attainment) {
                    if (!empty($attainment)) $attainmentCount[$index][$i] += strlen($attainment->content);
                }
            }
            $index++;
        }
        return view('wall/overview',compact('attainments','recentDays','users','attainmentCount'));
    }

    /**Create new attainment target
     * @param Request $request
     * @return mixed
     */
    public function newTarget(Request $request){
        $target = $request->get('target');
        Attainment::create([
            'user_id'   =>1,
            'url'       => $target,
            'name'      => $request->get('name'),
            'date'      => $request->get('date'),
        ]);
        return redirect('/wall');
    }

    /**delete target
     * @param Request $request
     * @return mixed
     */
    public function deleteTarget(Request $request){
        $target = $request->get('target');
        Attainment::where('url',$target)->delete();
        return redirect('/wall');
    }
}
