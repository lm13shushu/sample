<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;

class UsersController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    //路由/users/{user}  --使用Eloquent模型的单数小写格式来作为路由
    //$user匹配路由片段中的{user}
    //自动注入与请求URL中传入的ID对应的用户模型实例
    {
    	//$user=compact('user');
    	//$one=1;
        return view('users.show',compact('user'));
    }
    public function store(Request $request){
    	$this->validate($request,[
    		 'name' => 'required|max:50',
    		 'email' => 'required|email|unique:users|max:255',
    		 'password' => 'required|confirmed|min:6']);
    	 $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
    	 //[$user]等同于[$user->id]
    	session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
    	return redirect()->route('users.show', [$user]);

    }
}