<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxSize = (int) config('article-receiver.media.max_size', 10240);
        $allowedTypes = config('article-receiver.media.allowed_types', []);

        $fileRules = array_filter([
            'required',
            'file',
            $maxSize > 0 ? 'max:'.$maxSize : null,
            ! empty($allowedTypes) ? 'mimetypes:'.implode(',', $allowedTypes) : null,
        ]);

        return array_replace_recursive([
            'file' => [
                ...$fileRules,
            ],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'folder' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9_\/-]+$/',
                'not_regex:/\.\./',
            ],
        ], config('article-receiver.validation.upload_media', []));
    }
}
