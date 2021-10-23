<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $isArticleManager = $user
                ->roles
                ->where('name', '=', 'article_manager')
                ->count() > 0;

        if ($isArticleManager) {
            return true;
        }

        return null;
    }

    public function destroy(User $user, Article $article)
    {
        return (int)$user->id === (int)$article->user_id;
    }
}
