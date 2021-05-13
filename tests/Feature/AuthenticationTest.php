<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{   
    protected $baseUrl = '/api/auth';

    /** @test */
    public function authentication_successful()
    {
        $data = [
            'email' => Config::get('api.email'),
            'password' => Config::get('api.password')
        ];
        $this->json('post', $this->baseUrl.'/login', $data)
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }
    
    /** @test */
    public function authentication_invalid()
    {
        $data = [
            'email' => Config::get('api.email'),
            'password' => 'senha_invalida'
        ];
        $this->json('POST', $this->baseUrl.'/login', $data)
        ->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJsonStructure([
            'message'
        ]);
    }

    /** @test */
    public function authentication_validated_request()
    {
        $this->json('POST', $this->baseUrl.'/login')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure([
            'errors'
        ]);
    }

    /** @test */
    public function get_user_auth()
    {
        $user = User::where('email', Config::get('api.email'))->first();
        $token = JWTAuth::fromUser($user);
        $this->withToken($token)
        ->json('POST', $this->baseUrl.'/me')
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'id',
            'name',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at'
        ]);
    }

    /** @test */
    public function logout()
    {
        $user = User::where('email', Config::get('api.email'))->first();
        $token = JWTAuth::fromUser($user);
        $this->withToken($token)
        ->json('POST', $this->baseUrl.'/logout')
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'message'
        ]);
    }

    /** @test */
    public function refresh()
    {
        $user = User::where('email', Config::get('api.email'))->first();
        $token = JWTAuth::fromUser($user);
        $this->withToken($token)
        ->json('POST', $this->baseUrl.'/refresh')
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }
}
