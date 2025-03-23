<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class PlatformRequest extends FormRequest
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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'character_limit' => ['required', 'integer'],
        ];
        \Log::info( $this->route('platform'));
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Update request - ensure name and type are unique, but ignore the current record
            $rules['name'][] = 'unique:platforms,name,' . $this->route('platform');
            $rules['type'][] = 'unique:platforms,type,' . $this->route('platform');
        }

        return $rules;
    }
}
