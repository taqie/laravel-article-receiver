<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Taqie\LaravelArticleReceiver\Actions\Article\CreateArticleAction;
use Taqie\LaravelArticleReceiver\Actions\Article\DeleteArticleAction;
use Taqie\LaravelArticleReceiver\Actions\Article\UpdateArticleAction;
use Taqie\LaravelArticleReceiver\Http\Requests\Article\StoreArticleRequest;
use Taqie\LaravelArticleReceiver\Http\Requests\Article\UpdateArticleRequest;
use Taqie\LaravelArticleReceiver\Http\Resources\ArticleResource;
use Taqie\LaravelArticleReceiver\Models\Article;

class ArticleController
{
    public function index(Request $request): ResourceCollection
    {
        $articleClass = config('article-receiver.models.article', Article::class);

        $articles = $articleClass::query()
            ->with(['author', 'category', 'tags', 'media'])
            ->latest()
            ->paginate(15);

        $resourceClass = $this->articleResourceClass();

        return $resourceClass::collection($articles);
    }

    public function store(StoreArticleRequest $request, CreateArticleAction $action): Response
    {
        $article = $action->execute($request->toDto());
        $article->load(['author', 'category', 'tags', 'media']);

        $resourceClass = $this->articleResourceClass();

        return (new $resourceClass($article))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Article $article): JsonResource
    {
        $article->load(['author', 'category', 'tags', 'media']);

        $resourceClass = $this->articleResourceClass();

        return new $resourceClass($article);
    }

    public function update(UpdateArticleRequest $request, Article $article, UpdateArticleAction $action): JsonResource
    {
        $article = $action->execute($article, $request->toDto());
        $article->load(['author', 'category', 'tags', 'media']);

        $resourceClass = $this->articleResourceClass();

        return new $resourceClass($article);
    }

    public function destroy(Article $article, DeleteArticleAction $action): Response
    {
        $action->execute($article);

        return response()->noContent();
    }

    private function articleResourceClass(): string
    {
        $resource = config('article-receiver.response.resources.article')
            ?? config('article-receiver.response.resource', ArticleResource::class);

        return is_string($resource) && class_exists($resource) ? $resource : ArticleResource::class;
    }
}
