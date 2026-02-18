<?php

namespace App\Http\Requests\Clip;

use Illuminate\Foundation\Http\FormRequest;

class StoreClipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string'],
            'url' => ['required', 'url', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
