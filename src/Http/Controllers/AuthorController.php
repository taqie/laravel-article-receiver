<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Taqie\LaravelArticleReceiver\Actions\Author\CreateAuthorAction;
use Taqie\LaravelArticleReceiver\Actions\Author\DeleteAuthorAction;
use Taqie\LaravelArticleReceiver\Actions\Author\UpdateAuthorAction;
use Taqie\LaravelArticleReceiver\Http\Requests\Author\StoreAuthorRequest;
use Taqie\LaravelArticleReceiver\Http\Requests\Author\UpdateAuthorRequest;
use Taqie\LaravelArticleReceiver\Http\Resources\AuthorResource;
use Taqie\LaravelArticleReceiver\Models\Author;

class AuthorController
{
    public function index(Request $request): ResourceCollection
    {
        $authorClass = config('article-receiver.models.author', Author::class);

        $authors = $authorClass::query()
            ->withCount('articles')
            ->paginate(15);

        $resourceClass = $this->authorResourceClass();

        return $resourceClass::collection($authors);
    }

    public function store(StoreAuthorRequest $request, CreateAuthorAction $action): Response
    {
        $author = $action->execute($request->toDto());

        $resourceClass = $this->authorResourceClass();

        return (new $resourceClass($author))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Author $author): JsonResource
    {
        $author->loadCount('articles');

        $resourceClass = $this->authorResourceClass();

        return new $resourceClass($author);
    }

    public function update(UpdateAuthorRequest $request, Author $author, UpdateAuthorAction $action): JsonResource
    {
        $author = $action->execute($author, $request->toDto());
        $author->loadCount('articles');

        $resourceClass = $this->authorResourceClass();

        return new $resourceClass($author);
    }

    public function destroy(Author $author, DeleteAuthorAction $action): Response
    {
        $action->execute($author);

        return response()->noContent();
    }

    private function authorResourceClass(): string
    {
        $resource = config('article-receiver.response.resources.author', AuthorResource::class);

        return is_string($resource) && class_exists($resource) ? $resource : AuthorResource::class;
    }
}
