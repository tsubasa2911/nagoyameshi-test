<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;




class UserTest extends TestCase
{
    use RefreshDatabase;

    // 会員一覧ページ
    public function test_guest_cannot_access_admin_user_index_page()
    {
        $response = $this->get(route('admin.users.index'));  //未ログインで会員一覧ページにアクセス
        $response->assertRedirect(route('admin.login'));  //ログインページにリダイレクト
    }

    public function test_user_cannot_ccess_admin_user_index_page()
    {
        $user = User::factory()->create(); //一般ユーザーを作成
        $response = $this->actingAs($user)->get(route('admin.users.index')); // 一般ユーザーで会員一覧ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_admin_user_index_page()
    {
        
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.index')); // 管理者ユーザーで会員一覧ページにアクセス
        $response->assertStatus(200); // 200 OK
    }

    // 会員詳細ページ
    public function test_guest_cannot_access_admin_user_show_page()
    {
        $user = User::factory()->create(); // ユーザーを作成
        $response = $this->get( route('admin.users.show', $user)); // 未ログインで会員詳細ページにアクセス
        $response->assertRedirect(route('admin.login')); // ログインページにリダイレクト
    }

    public function test_normal_user_cannot_access_admin_user_show_page()
    {
        $user = User::factory()->create(); // 一般ユーザーを作成
        $response = $this->actingAs($user)->get(route('admin.users.show', $user)); // 一般ユーザーで会員詳細ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_admin_user_show_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $user = User::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.show', $user)); // 管理者ユーザーで会員詳細ページにアクセス
        $response->assertStatus(200); // 200 OK
    }
}
