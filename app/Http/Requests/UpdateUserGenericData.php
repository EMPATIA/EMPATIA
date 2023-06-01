<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class UpdateUserGenericData extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (auth()->user()->hasAnyRole(['laravel-user', 'laravel-admin'])) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required|max:255',
            'firstName' => 'required|max:255',
            'lastName' => 'required|max:255',
        ];
    }
}