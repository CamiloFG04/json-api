<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ArticleController extends Controller
{

    function index(Request $request): ArticleCollection
    {
        $articles = Article::query();
        if($request->filled('sort')){
            $sortFields = explode(',',$request->input('sort'));
            $allowedSortFields = ['title','content'];

            foreach ($sortFields as $sortField) {
                $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';

                $sortField = ltrim($sortField,'-');

                abort_unless(in_array($sortField,$allowedSortFields),400);

                $articles->orderBy($sortField,$sortDirection);
            }
        }

        return ArticleCollection::make($articles->get());
    }

    function store(SaveArticleRequest $request) :ArticleResource
    {
        $article = Article::create($request->validated());
        return ArticleResource::make($article);
    }

    function show(Article $article): ArticleResource
    {
        return ArticleResource::make($article);
    }

    function update(Article $article, SaveArticleRequest $request): ArticleResource {
        $article->update($request->validated());
        return ArticleResource::make($article);
    }

    function destroy(Article $article): Response {
        $article->delete();
        return response()->noContent();
    }
}
