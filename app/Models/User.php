<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * 指定数据表
     *
     * @var [type]
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 监听事件
     *
     * @return [type] [description]
     */
    public static function boot()
    {
         parent::boot();

         static::creating(function ($user) {
             $user->activation_token = str_random(30);
         });
    }

    /**
     * 展示用户头像
     *
     * @param  string $size [description]
     * @return [type]       [description]
     */
    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * 指明一个用户拥有多条微博
     *
     * @return [type] [description]
     */
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    /**
     * 取出用户所发布的数据并倒序排序
     *
     * @return [type] [description]
     */
    public function feed()
    {
        $user_ids = Auth::user()->followings->pluck('id')->toArray();
        array_push($user_ids, Auth::user()->id);
        return Status::whereIn('user_id', $user_ids)
                                ->with('user')
                                ->orderBy('created_at', 'desc');
    }

    /**
     * 获取粉丝列表
     *
     * @return [type] [description]
     */
    public function followers()
    {
        return $this->belongsToMany(User::Class, 'followers', 'user_id', 'follower_id');
    }

    /**
     * 获取用户关注人列表
     *
     * @return [type] [description]
     */
    public function followings()
    {
        return $this->belongsToMany(User::Class, 'followers', 'follower_id', 'user_id');
    }

    /**
     * 关注
     *
     * @param  [type] $user_ids [description]
     * @return [type]           [description]
     */
    public function follow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }

    /**
     * 取消关注
     *
     * @param  [type] $user_ids [description]
     * @return [type]           [description]
     */
    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    /**
     * 判断用户关注的人
     *
     * @param  [type]  $user_id [description]
     * @return boolean          [description]
     */
    public function isFollowing($user_id)
    {
        return $this->followings()->allRelatedIds()->contains($user_id);
    }

}
