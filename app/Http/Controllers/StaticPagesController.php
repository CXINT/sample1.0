<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Status;
use Auth;


/**
 * StaticPagesController 静态页面
 */
class StaticPagesController extends Controller
{
    /**
     * 首页
     *
     * @return [type] [description]
     */
    public function home()
    {
         $feed_items = [];
         if (Auth::check()) {
             $feed_items = Auth::user()->feed()->paginate(30);
         }

         return view('static_pages/home', compact('feed_items'));
    }

    /**
     * 帮助页
     *
     * @return [type] [description]
     */
    public function help()
    {
        return view('static_pages/help');
    }

    /**
     * 关于页
     *
     * @return [type] [description]
     */
    public function about()
    {
        return view('static_pages/about');
    }
}
