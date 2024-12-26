<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserPaginatedCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'success' => true,
            'page' => $this->currentPage(),
            'total_pages' => $this->lastPage(),
            'total_users' => $this->total(),
            'count' => $this->perPage(),
            'links' => [
                'next_url' => $this->nextPageUrl(),
                'prev_url' => $this->previousPageUrl(),
            ],
            'users' => $this->collection->isEmpty()
                ? []
                : UserResource::collection($this->collection),
        ];

    }
}
