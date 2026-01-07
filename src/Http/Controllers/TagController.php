<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Taqie\LaravelArticleReceiver\Actions\Tag\CreateTagAction;
use Taqie\LaravelArticleReceiver\Actions\Tag\DeleteTagAction;
use Taqie\LaravelArticleReceiver\Actions\Tag\UpdateTagAction;
use Taqie\LaravelArticleReceiver\Http\Requests\Tag\StoreTagRequest;
use Taqie\LaravelArticleReceiver\Http\Requests\Tag\UpdateTagRequest;
use Taqie\LaravelArticleReceiver\Http\Resources\TagResource;
use Taqie\LaravelArticleReceiver\Models\Tag;

class TagController
{
    public function index(Request $request): ResourceCollection
    {
        $tagClass = config('article-receiver.models.tag', Tag::class);

        $tags = $tagClass::query()
            ->withCount('articles')
            ->paginate(15);

        $resourceClass = $this->tagResourceClass();

        return $resourceClass::collection($tags);
    }

    public function store(StoreTagRequest $request, CreateTagAction $action): Response
    {
        $tag = $action->execute($request->toDto());

        $resourceClass = $this->tagResourceClass();

        return (new $resourceClass($tag))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Tag $tag): JsonResource
    {
        $tag->loadCount('articles');

        $resourceClass = $this->tagResourceClass();

        return new $resourceClass($tag);
    }

    public function update(UpdateTagRequest $request, Tag $tag, UpdateTagAction $action): JsonResource
    {
        $tag = $action->execute($tag, $request->toDto());
        $tag->loadCount('articles');

        $resourceClass = $this->tagResourceClass();

        return new $resourceClass($tag);
    }

    public function destroy(Tag $tag, DeleteTagAction $action): Response
    {
        $action->execute($tag);

        return response()->noContent();
    }

    private function tagResourceClass(): string
    {
        $resource = config('article-receiver.response.resources.tag', TagResource::class);

        return is_string($resource) && class_exists($resource) ? $resource : TagResource::class;
    }
}
