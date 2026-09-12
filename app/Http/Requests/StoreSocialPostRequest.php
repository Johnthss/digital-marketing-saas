<?php

namespace App\Http\Requests;

use App\Http\Requests\Social\StorePostRequest;

class StoreSocialPostRequest extends StorePostRequest
{
    public function messages(): array
    {
        return [
            'social_account_id.required' => 'Please select a social account.',
            'content.required' => 'Post content is required.',
        ];
    }
}
