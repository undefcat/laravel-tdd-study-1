<?php

namespace Tests\Feature\Article;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/api/articles';

    public function test_게시글_작성_성공()
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'title',
            'content' => 'content',
        ];

        $response = $this
            ->actingAs($user)
            ->postJson(self::URL, $data);

        $response->assertStatus(Response::HTTP_CREATED);

        $article = $user->articles()->first();

        $this->assertEquals($data['title'], $article->title);
        $this->assertEquals($data['content'], $article->content);
    }

    public function test_게시글_제목없음_실패()
    {
        $user = User::factory()->create();

        $data = [
            'title' => '',
            'content' => 'content',
        ];

        $response = $this
            ->actingAs($user)
            ->postJson(self::URL, $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_게시글_내용없음_실패()
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'title',
            'content' => '',
        ];

        $response = $this
            ->actingAs($user)
            ->postJson(self::URL, $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_게시글_작성_비회원_실패()
    {
        $data = [
            'title' => 'title',
            'content' => 'content',
        ];

        $response = $this->postJson(self::URL, $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_내_게시글_목록_가져오기()
    {
        $count = 10;

        $user = User::factory()
            ->has(Article::factory()->count($count))
            ->create();

        $response = $this
            ->actingAs($user)
            ->getJson(self::URL.'/my');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount($count, 'articles');
    }

    public function test_내_게시글_삭제_성공()
    {
        $user = User::factory()->create();
        $article = Article::factory()
            ->for($user)
            ->create();

        $response = $this
            ->actingAs($user)
            ->deleteJson(self::URL."/{$article->id}");

        $response->assertStatus(Response::HTTP_OK);

        $isDeleted = Article::where('id', '=', $article->id)
            ->count() === 0;

        $this->assertTrue($isDeleted);
    }

    public function test_내_게시글_아닌_게시글_삭제_403_오류()
    {
        $user1Article = Article::factory()
            ->for(User::factory()->create())
            ->create();

        $user2 = User::factory()->create();

        $response = $this
            ->actingAs($user2)
            ->deleteJson(self::URL."/{$user1Article->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_존재하지_않는_게시글_삭제_404_오류()
    {
        $user = User::factory()
            ->has(Article::factory()->count(5))
            ->create();

        $response = $this
            ->actingAs($user)
            ->deleteJson(self::URL.'/100');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
