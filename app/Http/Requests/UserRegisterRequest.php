<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRegisterRequest extends FormRequest
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
        return [
            'name' => 'required|string|min:2|max:60',
            'email' => 'required|string|email|unique:users,email',
            'phone' => 'required|string|regex:/^[\+]{1}380([0-9]{9})$/|unique:users,phone',
            'position_id' => 'required|integer|exists:positions,id',
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120|dimensions:min_width=70,min_height=70',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'name.string' => 'Username should contain 2-60 characters.',
            'name.min' => 'Username must be at least 2 characters.',
            'name.max' => 'Username may not be greater than 60 characters.',

            'email.required' => 'Email is required.',
            'email.string' => 'The email must be a valid email address according to RFC2822.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already taken.',

            'phone.required' => 'Phone number is required.',
            'phone.string' => 'The phone number must be a string.',
            'phone.regex' => 'User phone number should start with the code of Ukraine +380. and contain 9 characters after 0',
            'phone.unique' => 'This phone number is already taken.',

            'position_id.required' => 'Position ID is required.',
            'position_id.integer' => 'Position ID must be an integer.',
            'position_id.exists' => 'The position with the provided ID does not exist.',

            'photo.required' => 'Photo is required.',
            'photo.image' => 'The photo must be a valid image.',
            'photo.mimes' => 'The photo must be a JPEG/JPG file.',
            'photo.dimensions' => 'The minimum size of the photo must be 70x70px.',
            'photo.max' => 'The photo size must not exceed 5 MB.',
        ];
    }


}
