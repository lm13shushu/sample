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
}