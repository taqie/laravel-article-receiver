<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Taqie\LaravelArticleReceiver\Actions\Media\DeleteMediaAction;
use Taqie\LaravelArticleReceiver\Actions\Media\UploadMediaAction;
use Taqie\LaravelArticleReceiver\Http\Requests\Media\UploadMediaRequest;
use Taqie\LaravelArticleReceiver\Http\Resources\MediaResource;
use Taqie\LaravelArticleReceiver\Models\Media;

class MediaController
{
    public function store(UploadMediaRequest $request, UploadMediaAction $action): Response
    {
        $media = $action->execute(
            $request->file('file'),
            $request->input('alt_text'),
            null,
            $request->input('folder')
        );

        $resourceClass = $this->mediaResourceClass();

        return (new $resourceClass($media))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Media $media, DeleteMediaAction $action): Response
    {
        $action->execute($media);

        return response()->noContent();
    }

    private function mediaResourceClass(): string
    {
        $resource = config('article-receiver.response.resources.media', MediaResource::class);

        return is_string($resource) && class_exists($resource) ? $resource : MediaResource::class;
    }
}
