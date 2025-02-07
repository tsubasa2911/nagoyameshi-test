<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');  //検索ボックスに入力されたキーワード

        if($keyword !== null) {
            $categories = Category::where('name', 'like', "%{$keyword}%")->paginate(15);
        } else {
            $categories = Category::paginate(15);
        }

        $total = $categories->total(); //全レコード取得
        return view ('admin.categories.index', compact('keyword', 'categories', 'total'));
    }

    public function store(Request $request) //カテゴリ登録
    {
        $request->validate([
            'name' => 'required',
        ]);

        // HTTPリクエストから上記のパラメータの値を取得し、その値をもとにcategoriesテーブルに新しくデータを追加する処理
        $category = new Category();
        $category->name = $request->input('name');

        $category->save();

        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを登録しました。');
    }

    public function update(Request $request, Category $category)
    {
        // バリデーション設定
        $request->validate([
            'name' => 'required',
        ]);

        //HTTPリクエストから上記のパラメータの値を取得しcategoriesテーブルのデータを更新する処理
        $category->name = $request->input('name');
        $category->save();

        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを編集しました。');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを削除しました。');

    }
}
