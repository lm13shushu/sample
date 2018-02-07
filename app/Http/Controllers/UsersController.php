<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Mail;

class UsersController extends Controller
{
	 public function __construct()
    {
        $this->middleware('auth', [            
            'except' => ['show', 'create', 'store','index','confirmEmail']
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
    	$statuses = $user->statuses()
    					 ->orderBy('created_at', 'desc')
    					 ->paginate(30);
        return view('users.show',compact('user','statuses'));
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
    	$this->sendEmailConfirmationTo($user);
    	session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
    	return redirect('/');

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

    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'aufree@yousails.com';
        $name = 'Aufree';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }
//Eloquent 的 where 方法接收两个参数，第一个参数为要进行查找的字段名称，第二个参数为对应的值，查询结果返回的是一个数组
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = '粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }

}