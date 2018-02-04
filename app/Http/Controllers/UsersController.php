<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
	 public function __construct()
    {
        $this->middleware('auth', [            
            'except' => ['show', 'create', 'store','index']
        ]);
        
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

     public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

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
    	Auth::login($user);
    	session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
    	return redirect()->route('users.show', [$user]);

    }

     public function edit(User $user)
    {
    	$this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);
		//authorize 方法接收两个参数，第一个为授权策略的名称，第二个为进行授权验证的数据
        $this->authorize('update', $user);
        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', $user->id);
    }

     public function destroy(User $user)
    {
    	if ($this->authorize('destroy', $user)) {
			$user->delete();
			session()->flash('success', '成功删除用户！');
			return back();
    	}     
    }

}