<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Platform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $disallowedPlatforms = auth()->user()->disallowedPlatforms->pluck('id')->toArray();

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif', 'max:4096'],
            'image_url' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:draft,published,scheduled'],
            'scheduled_at' => ['nullable', 'date', 'after:now', 'required_if:status,scheduled'],
            'platforms' => ['required', 'array', 'min:1'],
            'platforms.*' => ['integer', 'exists:platforms,id', Rule::notIn($disallowedPlatforms)],
        ];

        if ($this->has('platforms') && is_array($this->platforms)) {
            $maxCharLimit = 0;
            foreach ($this->platforms as $platformId) {
                $platform = Platform::find($platformId);
                if ($platform && $platform->character_limit > $maxCharLimit) {
                    $maxCharLimit = $platform->character_limit;
                }
            }
            $rules['content'][] = 'max:' . $maxCharLimit;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'content.max' => 'The content exceeds the character limit for one or more of the selected platforms',
            'platforms.*.not_in' => 'You are not allowed to post to one or more of the selected platform',
        ];
    }
}
