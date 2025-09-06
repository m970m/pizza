<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductResource',
    properties: [
        new OA\Property(property: 'id', type: 'bigint', example: 'Product id'),
        new OA\Property(property: 'name', type: 'string', example: 'Product name'),
        new OA\Property(property: 'type', type: 'Product type', example: 'Product type'),
        new OA\Property(property: 'price', type: 'integer', example: 'Product price'),
        new OA\Property(property: 'description', type: 'string', example: 'Product string'),
        new OA\Property(property: 'image', type: 'string', example: 'Product image'),
    ],
    type: 'object'
)]
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'price' => $this->price,
            'description' => $this->description,
            'image' => $this->image,
        ];
    }
}
