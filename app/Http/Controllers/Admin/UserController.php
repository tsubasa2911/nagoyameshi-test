<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $reqest) 
    {
        $keyword = $reqest->input('keyword');

        if ($keyword !== null) {
            $users = User::where('name', 'like', "%{$keyword}%")
            ->orwhere('kana', 'like', "%{$keyword}%")->paginate(15);
        } else {
            // キーワードが空の場合は、全レコードを取得
            $users = User::paginate(15); 
        }    

        // 全レコード取得
        $total = $users->total();

        return view('admin.users.index', compact('users', 'keyword', 'total'));
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

}
