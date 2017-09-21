<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    /**
     * 允许微博更新内容
     *
     * @var [type]
     */
    protected $fillable = ['content'];

    /**
     *
     * 
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
