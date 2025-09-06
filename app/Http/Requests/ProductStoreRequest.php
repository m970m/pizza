<?php

namespace App\Http\Requests;

use App\Enums\ProductType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductStoreRequest',
    required: [
        'name', 'type', 'price', 'description', 'image'
    ],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Product name'),
        new OA\Property(property: 'type', type: 'string', enum: ['pizza', 'drink'], example: 'Product type'),
        new OA\Property(property: 'price', type: 'integer', example: 'Product price'),
        new OA\Property(property: 'description', type: 'string', example: 'Product string'),
        new OA\Property(property: 'image', type: 'string', example: 'Product image'),
    ],
    type: 'object'
)]
class ProductStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::enum(ProductType::class)],
            'price' => 'required|integer|min:0',
            'description' => 'required|string|max:255',
            'image' => 'required|string|max:255'
        ];
    }
}
