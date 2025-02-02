<?php

namespace Tests\Feature\Admin;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    //店舗一覧ページ
    public function test_guest_cannot_access_admin_restaurant_index_page()
    {
        $responce = $this->get(route('admin.restaurants.index'));
        $responce->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_access_admin_restaurant_index_page()
    {
        $user = User::factory()->create(); //一般ユーザー作成
        $responce = $this->actingAs($user)->get(route('admin.restaurants.index'));
        $responce->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_admin_restaurant_index_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.index')); // 管理者ユーザーで店舗一覧ページにアクセス
        $response->assertStatus(200); // 200 OK
    }

    //店舗詳細ページ
    public function test_guest_cannot_access_admin_restaurant_show_page()
    {
        $restaurant = Restaurant::factory()->create(); // 店舗を作成
        $responce = $this->get(route('admin.restaurants.show', $restaurant));
        $responce->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_access_admin_restaurant_show_page()
    {
        $user = User::factory()->create(); // ユーザーを作成
        $restaurant = Restaurant::factory()->create(); // 店舗を作成
        $response = $this->actingAs($user)->get( route('admin.restaurants.show', $restaurant)); // ログイン済みユーザーで店舗詳細ページにアクセス
        $response->assertRedirect(route('admin.login')); // ログインページにリダイレクト
    }

    public function test_admin_user_can_access_admin_restaurant_show_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.show', $restaurant)); // 管理者ユーザーで店舗詳細ページにアクセス
        $response->assertStatus(200); // 200 OK
    }

    // 店舗登録ページ
    public function test_guest_cannot_access_admin_restaurant_create_page()
    {
        $responce = $this->get(route('admin.restaurants.create', $restaurant));
        $responce->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_access_admin_restaurant_create_page()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get( route('admin.restaurants.create', $restaurant)); // ログイン済みユーザーで店舗登録ページにアクセス
        $response->assertRedirect(route('admin.login')); // ログインページにリダイレクト
    }

    public function test_admin_user_can_access_admin_restaurant_create_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.show', $restaurant)); // 管理者ユーザーで店舗登録ページにアクセス
        $response->assertStatus(200); // 200 OK
    }

    //店舗登録機能
    public function test_guest_cannot_restaurant_create()
    {
        $responce = $this->post(route('restaurants.store')); //post メソッドで店舗登録のリクエストを送信
        $response->assertRedirect(route('login')); // ログインページにリダイレクト
    }

    public function test_user_cannot_restaurant_create()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('restaurants.store'));
        $response->assertForbidden(); //アクセス禁止
    }

    public function test_admin_user_can_restaurant_create()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        
        $restaurantData = [
            'name' => '新店舗',
            'address' => '名古屋市',
            'lowest' => 1000,
            'highest' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => '50',
            
        ];
        $response = $this->actingAs($admin)->post(route('restaurants.store'), $restaurantData);
        // リダイレクトされたことを確認
        $response->assertStatus(302);
        // 送信したデータがproductsテーブルに保存されていることを検証する
        $this->assertDatabaseHas('restaurants', $restaurantData);
    }

    //店舗編集ページ
    public function test_guest_cannot_access_admin_restaurant_edit_page()
    {
        $restaurant = Restaurant::factory()->create();
        $responce = $this->get(route('admin.restaurants.edit', $restaurant));
        $responce->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_access_admin_restaurant_edit_page()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get( route('admin.restaurants.edit', $restaurant)); // ログイン済みユーザーで店舗編集ページにアクセス
        $response->assertForbidden();
    }

    public function test_admin_user_can_access_admin_restaurant_edit_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $responce = $this->actingAs($admin, 'admin')->get( route('admin.restaurants.edit', $restaurant));
        $response->assertStatus(200);
    }

    //店舗更新機能
    public function test_guest_cannot_restaurant_update()
    {
        $restaurant = Restaurant::factory()->create();
        $responce = $this->put(route('restaurants.edit', $restaurant)); //put メソッドで店舗編集のリクエストを送信
        $response->assertRedirect(route('login')); 
    }

    public function test_user_cannot_restaurant_updata()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $responce = $this->actingAs($user)->put(route('restaurants.edit',  $restaurant));

        $response->assertForbidden(); //アクセス禁止
    }

    public function test_admin_user_can_restaurant_updata()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $responce = $this->actingAs($admin)->put(route('restaurants.edit',  $restaurant));

        // リダイレクトされたことを確認
        $response->assertStatus(302);

        // データベースが更新されていることを確認
        $this->assertDatabaseHas('restaurants', $restaurant);
    }

    //店舗削除機能
    public function test_guest_cannot_restaurant_delete()
    {
        $restaurant = Restaurant::factory()->create();
        $responce = $this->delete(route('restaurants.destroy',  $restaurant));
        $response->assertRedirect(route('login')); 
    }

    public function test_user_cannot_estaurant_delete()
{
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();

    $response = $this->actingAs($user)->delete(route('restaurants.destroy', $restaurant));

    $response->assertForbidden();
}

    public function test_admin_user_can_restaurant_delete()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $responce = $this->actingAs($admin)->put(route('restaurants.destroy',  $restaurant));

        // リダイレクトされたことを確認
        $response->assertStatus(302);

        // データベースが更新されていることを確認
        $this->assertDatabaseMissing('restaurants', $restaurant);
    }

}
