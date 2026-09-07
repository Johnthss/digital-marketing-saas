<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SocialPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => 'required|string|in:facebook,instagram,twitter,linkedin,tiktok,pinterest',
            'content' => 'required|string|max:5000',
            'social_account_id' => 'required|exists:social_accounts,id',
            'media' => 'nullable|array',
            'hashtags' => 'nullable|array',
            'scheduled_at' => 'nullable|date',
            'status' => 'nullable|in:draft,scheduled',
        ];
    }
}
