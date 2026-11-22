<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:40'],
            'service_id' => ['nullable', Rule::exists('services', 'id')->where('is_active', true)],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            // Honeypot: hidden from humans, bots tend to fill it in.
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return ['website.prohibited' => 'Your message could not be sent.'];
    }

    public function attributes(): array
    {
        return ['service_id' => 'service', 'name' => 'your name'];
    }
}
