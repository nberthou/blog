<?php

namespace App\Http\Requests\Auth;

use App\Models\EmailVerificationCode;
use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'size:'.EmailVerificationCode::CODE_LENGTH,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => __('verification.code_required'),
            'code.size' => __('verification.code_invalid'),
        ];
    }
}
