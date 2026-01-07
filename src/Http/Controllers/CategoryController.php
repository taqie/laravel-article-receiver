<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Taqie\LaravelArticleReceiver\Actions\Category\CreateCategoryAction;
use Taqie\LaravelArticleReceiver\Actions\Category\DeleteCategoryAction;
use Taqie\LaravelArticleReceiver\Actions\Category\UpdateCategoryAction;
use Taqie\LaravelArticleReceiver\Http\Requests\Category\StoreCategoryRequest;
use Taqie\LaravelArticleReceiver\Http\Requests\Category\UpdateCategoryRequest;
use Taqie\LaravelArticleReceiver\Http\Resources\CategoryResource;
use Taqie\LaravelArticleReceiver\Models\Category;

class CategoryController
{
    public function index(Request $request): ResourceCollection
    {
        $categoryClass = config('article-receiver.models.category', Category::class);

        $categories = $categoryClass::query()
            ->withCount('articles')
            ->paginate(15);

        $resourceClass = $this->categoryResourceClass();

        return $resourceClass::collection($categories);
    }

    public function store(StoreCategoryRequest $request, CreateCategoryAction $action): Response
    {
        $category = $action->execute($request->toDto());

        $resourceClass = $this->categoryResourceClass();

        return (new $resourceClass($category))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Category $category): JsonResource
    {
        $category->loadCount('articles');

        $resourceClass = $this->categoryResourceClass();

        return new $resourceClass($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category, UpdateCategoryAction $action): JsonResource
    {
        $category = $action->execute($category, $request->toDto());
        $category->loadCount('articles');

        $resourceClass = $this->categoryResourceClass();

        return new $resourceClass($category);
    }

    public function destroy(Category $category, DeleteCategoryAction $action): Response
    {
        $action->execute($category);

        return response()->noContent();
    }

    private function categoryResourceClass(): string
    {
        $resource = config('article-receiver.response.resources.category', CategoryResource::class);

        return is_string($resource) && class_exists($resource) ? $resource : CategoryResource::class;
    }
}
