<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     //
    // }
    /**
     * 为默认生成的用户授权策略添加 update 方法，用于用户更新时的权限验证
     *
     * @param  User   $currentUser [description]
     * @param  User   $user        [description]
     * @return [type]              [description]
     */
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }

    /**
     * 删除用户时的相关授权
     * 
     * @param  User   $currentUser [description]
     * @param  User   $user        [description]
     * @return [type]              [description]
     */
    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->is_admin && $currentUser->id !== $user->id;//只有当前用户拥有管理员权限且删除的用户不是自己时才显示链接
    }
}
