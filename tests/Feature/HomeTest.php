<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;

class HomeTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('home');

        $response->assertStatus(200);
    }

    // 未ログインのユーザーは会員側のトップページにアクセスできる
    public function test_guest_can_accsce_home_page()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    // ログイン済みの一般ユーザーは会員側のトップページにアクセスできる
    public function test_user_can_accsce_home_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('home'));
        $response->assertStatus(200);
    }

    //ログイン済みの管理者は会員側のトップページにアクセスできない
    Public function test_admin_user_can_accsce_home_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('home'));
        $response->assertStatus(302);
    }


}
