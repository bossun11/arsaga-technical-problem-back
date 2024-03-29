<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
    return [
      "title" => ["required", "string", "max:255"],
      "content" => ["required", "string", "max:1000"],
      "image" => ["nullable", "file", "mimes:jpeg,png,jpg,webp"],
      "tags" => ["nullable", "array", "max:3"],
      "tags.*" => ["string", "max:255"],
    ];
  }
}
