<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Auth;//权限
use Mail;//邮件

class UsersController extends Controller
{
    /**
     * 身份验证中间件,除了 except 数组中指定的动作，其他的动作都必须登录以后才能操作
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    /**
     * 展示用户列表
     *
     * @return [type] [description]
     */
    public function index()
    {
        $users = User::paginate(10);//分页输出
        return view('users.index', compact('users'));
    }

    /**
     * 处理注册
     *
     * @return [type] [description]
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * 展示用户微博信息
     *
     * @param  User   $user [description]
     * @return [type]       [description]
     */
     public function show(User $user)
     {
         $statuses = $user->statuses()
                            ->orderBy('created_at', 'desc')
                            ->paginate(30);
         return view('users.show', compact('user', 'statuses'));
     }

    /**
     * 处理登录
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
     public function store(Request $request)
     {
         $this->validate($request, [
             'name' => 'required|max:50',
             'email' => 'required|email|unique:users|max:255',
             'password' => 'required|confirmed|min:6'
         ]);

         $user = User::create([
             'name' => $request->name,
             'email' => $request->email,
             'password' => bcrypt($request->password),
         ]);

         $this->sendEmailConfirmationTo($user);
         session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
         return redirect('/');
     }

    /**
     * 编辑用户
     *
     * @param  User   $user [description]
     * @return [type]       [description]
     */
    public function edit(User $user)
    {
        //验证用户授权策略
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    /**
     * 处理用户提交的个人信息
     *
     * @param  User    $user    [description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'required|confirmed|min:6'
        ]);

        //验证用户授权策略
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

    /**
     * 删除用户
     *
     * @param  User   $user [description]
     * @return [type]       [description]
     */
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

    /**
     * 邮件处理
     *
     * @param  [type] $user [description]
     * @return [type]       [description]
     */
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

    /**
     * 确认邮件
     *
     * @param  [type] $token [description]
     * @return [type]        [description]
     */
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

    /**
     * 关注的人
     *
     * @param  User   $user [description]
     * @return [type]       [description]
     */
    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    /**
     * 粉丝
     *
     * @param  User   $user [description]
     * @return [type]       [description]
     */
    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = '粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
