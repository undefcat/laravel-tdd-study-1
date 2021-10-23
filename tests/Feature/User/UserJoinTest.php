<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserJoinTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/api/users';

    public function test_회원가입_성공()
    {
        $data = [
            'name' => 'user',
            'email' => 'email@root.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson(self::URL, $data);

        $response->assertStatus(Response::HTTP_CREATED);

        $isUserExist = User::where('email', '=', $data['email'])
            ->count() > 0;

        $this->assertTrue($isUserExist);
    }

    public function test_회원가입_이메일중복_실패()
    {
        $user = User::factory()->create();

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson(self::URL, $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_회원가입_올바르지_않은_이메일형식_실패()
    {
        $data = [
            'name' => 'user',
            'email' => 'email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson(self::URL, $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_회원가입_비밀번호_확인_실패()
    {
        $data = [
            'name' => 'user',
            'email' => 'email@email.com',
            'password' => 'password',
            'password_confirmation' => 'password2',
        ];

        $response = $this->postJson(self::URL, $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
