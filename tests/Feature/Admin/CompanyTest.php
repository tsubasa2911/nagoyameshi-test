<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin; 
use App\Models\Company;

class CompanyTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    use RefreshDatabase;

    // 会社概要
    // indexアクション（会社概要ページ） 未ログインのユーザーは管理者側の会社概要ページにアクセスできない
    public function test_guest_cannot_access_admin_company_index_page()
    {
        $response = $this->get(route('admin.company.index'));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会社概要ページにアクセスできない
    public function test_user_cannot_access_admin_company_index_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.company.index')); 
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の会社概要ページにアクセスできる
    public function test_admin_user_can_access_admin_company_index_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $company = Company::factory()->create();


        $response = $this->actingAs($admin, 'admin')->get(route('admin.company.index')); // 管理者ユーザーで会社概要ページにアクセス
        $response->assertStatus(200); // 200 OK
    }

    // editアクション（会社概要編集ページ）
    // 未ログインのユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_guest_cannot_access_admin_company_edit_page()
    {
        $company = Company::factory()->create();
        $response = $this->get(route('admin.company.edit', $company)); 
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_user_cannot_access_admin_company_edit_page()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.company.edit', $company)); 
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の会社概要編集ページにアクセスできる
    public function test_admin_user_can_access_admin_company_edit_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $company = Company::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.company.edit', $company)); // 管理者ユーザーで会社概要編集ページにアクセス
        $response->assertStatus(200); // 200 OK
    }

    // updateアクション（会社概要更新機能）
    // 未ログインのユーザーは会社概要を更新できない
    public function test_guest_cannot_access_admin_company_update()
    {
        $company = Company::factory()->create();

        $response = $this->put(route('admin.company.update', $company)); //put メソッドで会社概要更新のリクエストを送信
        $response->assertRedirect(route('admin.login')); 
    }

    // ログイン済みの一般ユーザーは会社概要を更新できない
    public function test_user_cannot_access_admin_company_update()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $company_Data = [
            'name' => '更新テスト',
            'postal_code' => '0000000',
            'address' => 'テスト',
            'representative' => 'テスト',
            'establishment_date' => 'テスト',
            'capital' => 'テスト',
            'business' => 'テスト',
            'number_of_employees' => 'テスト'
        ];

        $response = $this->actingAs($user)->put(route('admin.company.update', $company), $company_Data); //put メソッドで会社概要更新のリクエストを送信
        $response->assertRedirect(route('admin.login'));

    }

    // ログイン済みの管理者は会社概要を更新できる
    public function test_admin_user_can_access_admin_company_update()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $company = Company::factory()->create();

        $company_Data = [
            'name' => '更新テスト',
            'postal_code' => '0000000',
            'address' => 'テスト',
            'representative' => 'テスト',
            'establishment_date' => 'テスト',
            'capital' => 'テスト',
            'business' => 'テスト',
            'number_of_employees' => 'テスト'
        ];

        $response = $this->actingAs($admin, 'admin')->put(route('admin.company.update', $company), $company_Data);

        // リダイレクトされたことを確認
        $response->assertStatus(302);

    }
}
