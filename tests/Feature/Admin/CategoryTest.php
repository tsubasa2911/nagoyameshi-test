<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\CategoryController;
use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;


class CategoryTest extends TestCase
{
    use RefreshDatabase;

    // カテゴリ一覧ページ
    // 未ログインのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_categories_index_page()
    {
        $response = $this->get(route('admin.categories.index'));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_user_cannot_access_admin_categories_index_page()
    {
        $user = User::factory()->create(); //一般ユーザー作成
        $response = $this->actingAs($user)->get(route('admin.categories.index'));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側のカテゴリ一覧ページにアクセスできる
    public function test_admin_user_can_access_admin_categories_index_page()
    {
        $admin = new Admin(); 
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.categories.index'));
        $response->assertStatus(200);
    }

    // カテゴリ登録機能
    // 未ログインのユーザーはカテゴリを登録できない
    public function test_guest_cannot_category_store()
    {
        $categoryData = [
            'name' => 'テスト'
        ];

        $response = $this->post(route('admin.categories.store'), $categoryData);

        // データベースに同じものがないかチェック
        $this->assertDatabaseMissing('categories', $categoryData);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーはカテゴリを登録できない
    public function test_user_cannot_category_store()
    {
        $user = User::factory()->create();
        $categoryData = [
            'name' => '新テスト'
        ];

        $response = $this->actingAs($user)->post(route('admin.categories.store'), $categoryData);

        // データベースに同じものがないかチェック
        $this->assertDatabaseMissing('categories', $categoryData);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者はカテゴリを登録できる
    public function test_admin_user_cann_category_store()
    {
        $admin = new Admin(); 
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $categoryData = [
            'name' => '新テスト'
        ];

        $response = $this->actingAs($admin, 'admin')->post(route('admin.categories.store'), $categoryData);

         // データベースに同じものがないかチェック
        $this->assertDatabaseHas('categories', ['name' => '新テスト']);

        $response->assertRedirect(route('admin.categories.index'));
    }

    // カテゴリ更新機能
    // 未ログインのユーザーはカテゴリを更新できない
    public function test_guest_cannot_category_update()
    {
        $category = Category::factory()->create();
        $categoryData = [
            'name' => '更新テスト'
        ];

        $response = $this->put(route('admin.categories.update',  $category), $categoryData);
        
        $this->assertDatabaseMissing('categories', $categoryData);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーはカテゴリを更新できない
    public function test_user_cannot_category_update()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $categoryDate = [
            'name' => '更新テスト'
        ];

        $response = $this->actingAs($user)->put(route('admin.categories.update', $category),  $categoryDate);

        $this->assertDatabaseMissing('categories', $categoryDate);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者はカテゴリを更新できる
    public function test_admin_user_can_category_update()
    {
        $admin = new Admin(); 
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $category = Category::factory()->create();

        $categoryDate = [
            'name' => '更新テスト'
        ];

        $response = $this->actingAs($admin, 'admin')->put(route('admin.categories.update', $category),  $categoryDate);

        $this->assertDatabaseHas('categories', ['name' => '更新テスト']);
        $response->assertRedirect(route('admin.categories.index'));
    }

    // 未ログインのユーザーはカテゴリを削除できない
    public function test_guest_cannot_access_admin_category_destroy()
    {
        $category = Category::factory()->create();

        $response = $this->delete(route('admin.categories.destroy', $category));

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーはカテゴリを削除できない
    public function test_user_cannot_category__destroy()
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.categories.destroy', $category));

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者はカテゴリを削除できる
    public function test_admin_user_can_category_destroy()
    {
        $admin = new Admin(); 
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.categories.destroy', $category));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);

        $response->assertRedirect(route('admin.categories.index'));

    }
}
