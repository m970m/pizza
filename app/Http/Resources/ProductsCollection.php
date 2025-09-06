<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'ProductsCollection',
    description: 'Collection of products',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/ProductResource',
                type: 'object'
            )
        )
    ]
)]
class ProductsCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => ProductResource::collection($this->collection),
            'current_page' => $this->currentPage(),
            'from'=> $this->firstItem(),
            'last_page' => $this->lastPage(),
            'total' => $this->total(),
        ];
    }
}
