<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;

class TermController extends Controller
{
    public function index(Request $request)
    {
        $term = Term::first();// 最初のデータを取得
        return view('admin.terms.index', compact($term));
    }

    public function edit(Request $request)
    {
        $term = Term::all();
        return view('admin.terms.edit', compact($term));
    }

    public function update(Request $request, Term $term)
    {
        // バリデーション設定
        $request->validate([
            'content' => 'required'
        ]);

        $term->content = $request->input('content');
        return redirect()->route('admin.terms.index')->with('flash_message', '利用規約を編集しました。');
    }
}