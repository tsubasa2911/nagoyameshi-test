<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;


class RestaurantController extends Controller
{
    public function index(Request $reqest) 
    {
        $keyword = $reqest->input('keyword');

        if ($keyword !== null) {
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}")->pagenate(15);
        } else {
            //　検索ワードが空の場合、全レコードを取得
            $restaurants = Restaurant::pagenate(15);
        }

        $total = $restaurants->total();

        return view('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
    }

    public function show(Restaurant $restaurant)
    {
        return view('admin.restaurants.show', compact(restaurant));
    }

    public function create() 
    {
        return view('admin.restaurants.create');
    }

    public function store(Request $reqest) 
    {
        //バリデーション設定
        $reqest->validate([
            'name' => 'required|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|regex:/^\d{3}-\d{4}$/|max:8',
            'address' => 'required|',
            'opening_time' => 'required|date_fomat:H:i',
            'closing_time' => 'required|date_fomat:H:i|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

        //入力内容をもとにテーブルにデータを追加
        $restaurant = new Restaurant();
        $restaurant->name = $reqest->input('name');
        //$restaurant ->image = $reqest->input('image');
        $restaurant->description = $reqest->input('description');
        $restaurant->lowest_price = $reqest->input('lowest_price');
        $restaurant->highest_price = $reqest->input('highest_price');
        $restaurant->postal_code = $reqest->input('postal_code');
        $restaurant->address = $reqest->input('address');
        $restaurant->opening_time = $reqest->input('opening_time');
        $restaurant->closing_time = $reqest->input('closing_time');
        $restaurant->seating_capacity = $reqest->input('seating_capacity');
    
        if ($reqest->hasFile('image')) {

            // アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存
            $image_path = $reqest->file('image')->store('public/restaurants');

            // ファイル名を取得
            $imageName = basename('$image_path');
            $restaurant->image = $imageName;

        } else {
            //アップロードされていない場合
            $restaurant -> image = '';
        }
        $restaurant->save();

        // 店舗一覧ページへリダイレクトし、フラッシュメッセージを設定
        return redirect()->route('restaurants.index')->with('flash_message', '店舗を登録しました。');
    }
    
    public function edit() {
        return view('admin.restaurants.edit');
    }

    public function update(Request $reqest) {

        //バリデーション設定
        $reqest->validate([
            'name' => 'required|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|regex:/^\d{3}-\d{4}$/|max:8',
            'address' => 'required|',
            'opening_time' => 'required|date_fomat:H:i',
            'closing_time' => 'required|date_fomat:H:i|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

            //入力内容をもとにテーブルにデータを追加
            $restaurant = new Restaurant();
            $restaurant->name = $reqest->input('name');
            //$restaurant ->image = $reqest->input('image');
            $restaurant->description = $reqest->input('description');
            $restaurant->lowest_price = $reqest->input('lowest_price');
            $restaurant->highest_price = $reqest->input('highest_price');
            $restaurant->postal_code = $reqest->input('postal_code');
            $restaurant->address = $reqest->input('address');
            $restaurant->opening_time = $reqest->input('opening_time');
            $restaurant->closing_time = $reqest->input('closing_time');
            $restaurant->seating_capacity = $reqest->input('seating_capacity');

            if ($reqest->hasFile('image')) {

                // アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存
                $image_path = $reqest->file('image')->store('public/restaurants');

                // ファイル名を取得
                $imageName = basename('$image_path');
                $restaurant->image = $imageName;
            }

        // 店舗詳細ページへリダイレクトし、フラッシュメッセージを設定
        return redirect()->route('restaurants.show', $restaurant)->with('flash_message', '店舗を編集しました。');
    }

    public function destroy(Restaurant $restaurant) {
    // レストランデータの削除
    $restaurant->delete(); 
    return redirect()->route('restaurants.index')->with('flash_message', '店舗を削除しました。');
    }
    
}

