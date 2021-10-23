<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function destroy(User $user, Article $article)
    {
        return (int)$user->id === (int)$article->user_id;
    }
}
