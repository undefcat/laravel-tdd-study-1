<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/api/auth/login';

    public function test_로그인_성공()
    {
        $user = User::factory()->create();

        $data = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson(self::URL, $data);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_로그인_유효하지_않은_이메일_실패()
    {
        $data = [
            'email' => 'email',
            'password' => 'password',
        ];

        $response = $this->postJson(self::URL, $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_로그인_존재하지_않는_사용자_실패()
    {
        $data = [
            'email' => 'no_user@email.com',
            'password' => 'password',
        ];

        $response = $this->postJson(self::URL, $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_로그인_비밀번호_틀림_실패()
    {
        $user = User::factory()->create();

        $data = [
            'email' => $user->email,
            'password' => 'invalid_password',
        ];

        $response = $this->postJson(self::URL, $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
