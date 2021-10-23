<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    public function my()
    {
        $articles = Article::where('user_id', '=', Auth::id())
            ->get();

        return response()->json([
            'articles' => $articles,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required'],
            'content' => ['required'],
        ]);

        $article = new Article();

        $article->user_id = Auth::id();
        $article->title = $data['title'];
        $article->content = $data['content'];

        $article->save();

        return response()->json(null, Response::HTTP_CREATED);
    }

    public function destroy(Request $request, int $id)
    {
        $article = Article::select(['id', 'user_id'])
            ->findOrFail($id);

        if (!$request->user()->can('destroy', $article)) {
            return response()->json(null, Response::HTTP_FORBIDDEN);
        }

        $article->delete();

        return response()->json(null, Response::HTTP_OK);
    }
}
