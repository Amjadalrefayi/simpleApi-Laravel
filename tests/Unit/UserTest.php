<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_Successful_register(){
        $response = $this->post('api/auth/register',[
            'name' =>'test',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);

        $response->assertStatus(200);
    }

    public function test_Unsuccessful_register(){
        $response = $this->post('api/auth/register',[
            'name' =>'te',
            'email' => 'test.com', //not an email
            'password' => Hash::make('1') // less than 8 character
        ]);

        $response->assertStatus(404);
    }

    public function test_Successful_login(){
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);
        $this->withoutExceptionHandling();
        $response = $this->post('api/auth/login',[
            'email' => 'test@test.com',
            'password' => 'password'
        ]);
        $response->assertStatus(200);
    }

    public function test_Unsuccessful_login(){
        User::factory()->create([
            'email' => 'Unsuccessful@test.com',
            'password' => Hash::make('password')
        ]);
        $response = $this->post('api/auth/login',[
            'email' => 'Unsuccessful@test.com',
            'password' => 'wrongPassword'
        ]);
        $response->assertStatus(404);
    }

    public function test_Must_enter_email_and_password_to_login(){
        $response = $this->post('api/auth/login',[/* Empty Request */]);
        $response->assertStatus(404);
    }

    public function test_faild_to_user_duplication_email(){

        User::factory()->create([
            'name' =>'test',
            'email' => 'duplicate@email.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->post('api/auth/register',[
            'name' =>'test',
            'email' => 'duplicate@email.com',
            'password' => Hash::make('password')
        ]);

        $response->assertStatus(404);
    }

    public function test_Get_user_profile(){

        $this->post('api/auth/register',[
            'name' =>'test',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);
        Sanctum::actingAs(
            User::all()->first()
        );

        $response=$this->get('api/auth/me');
        $response->assertStatus(200);
    }

    public function test_Update_user_profile_Successful(){

        $this->post('api/auth/register',[
            'name' =>'test',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);
        Sanctum::actingAs(
            User::all()->first()
        );

        $response=$this->put('api/auth/me',[
            'name' => 'testtest',
            'password' => 'passwordpassword',
            'gender' => 'male' ,
            'city' => 'Damas',
            'bio'=> 'bio' ,
        ]);
        $response->assertStatus(200);
    }

    public function test_Update_user_profile_Unsuccessful(){

        $this->post('api/auth/register',[
            'name' =>'test',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);
        Sanctum::actingAs(
            User::all()->first()
        );

        //UnValid Data
        $response=$this->put('api/auth/me',[
            'name' => 't',
            'password' => '2',
            'gender' => 'm' ,
            'city' => 'Damas',
            'bio'=> 'bio' ,
        ]);
        $response->assertStatus(404);
    }

    public function test_refresh_token(){
        $this->post('api/auth/register',[
            'name' =>'test',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);
        Sanctum::actingAs(
            User::all()->first()
        );
        $response=$this->get('api/auth/refresh');

        $response->assertStatus(200);
    }

    public function test_user_logout(){

        $this->post('api/auth/register',[
            'name' =>'test',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);
        Sanctum::actingAs(
            User::all()->first()
        );
        $response=$this->get('api/auth/logout');

        $response->assertStatus(200);
    }

}
