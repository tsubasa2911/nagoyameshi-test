<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\Term;
use App\Models\User;
use App\Models\Admin;


class TermTest extends TestCase
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

    // indexアクション（利用規約ページ）
    //未ログインのユーザーは管理者側の利用規約ページにアクセスできない
    public function test_guest_cannot_access_admin_term_index_page()
    {
        $response = $this->get(route('admin.terms.index'));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の利用規約ページにアクセスできない
    public function test_user_cannot_access_admin_term_index_page()
        {
            $user = User::factory()->create();
            $response = $this->actingAs($user)->get(route('admin.terms.index'));
            $response->assertRedirect(route('admin.login'));

        }

    // ログイン済みの管理者は管理者側の利用規約ページにアクセスできる
    public function test_admin_user_can_access_admin_term_index_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $term = Term::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.terms.index', $term->id));
        $response->assertStatus(200);
    }
    
    // 未ログインのユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_guest_cannot_access_admin_term_edit_page()
    {
        $term = Term::factory()->create();
        $response = $this->get(route('admin.terms.edit', $term->id));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_user_cannot_access_admin_term_edit_page()
    {
        $term = Term::factory()->create();
        $user = User::factory()->create();
            
        $response = $this->actingAs($user)->get(route('admin.terms.index',  $term->id));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の利用規約編集ページにアクセスできる
    public function test_admin_user_can_access_admin_term_edit_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $term = Term::factory()->create();
        //$response = $this->actingAs($admin, 'admin')->get(route('admin.terms.edit',  $term->id));
        $response = $this->actingAs($admin, 'admin')->get(route('admin.terms.edit', ['term' => $term->id]));
        $response->assertStatus(200);
    }

    //updateアクション（利用規約更新機能）
    //未ログインのユーザーは利用規約を更新できない
    public function test_guest_cannot_access_admin_term_update()
    {
        $term = Term::factory()->create();

        $term_Data = [
            'content' => '更新'
        ];

        $response = $this->put(route('admin.terms.update', $term),  $term_Data);
        $response->assertRedirect(route('admin.login')); 

    }

    //ログイン済みの一般ユーザーは利用規約を更新できない
    public function test_user_cannot_access_admin_term_update()
    {
        $term = Term::factory()->create();
        $user = User::factory()->create();

        $term_Data = [
            'content' => '更新'
        ];

        $response = $this->actingAs($user)->put(route('admin.terms.update', $term),  $term_Data);
        $response->assertRedirect(route('admin.login')); 

    }

    //ログイン済みの管理者は利用規約を更新できる

    public function test_admin_user_can_access_admin_term_update()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $term = Term::factory()->create();

        $term_Data = [
            'content' => '更新'
        ];

        $response = $this->actingAs($admin, 'admin')->put(route('admin.terms.update', $term),  $term_Data);
        $response->assertStatus(302);
        $this->assertDatabaseHas('terms', $term_Data);
    }

}
