<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlatformResource extends JsonResource
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
            'type' => 'platform',
            'attributes' => [
                'name' => $this->name,
                'type' => $this->type,
                'characterLimit' => $this->character_limit,
                $this->mergeWhen($request->routeIs('platforms.show'), [
                    'createdAt' => $this->created_at,
                    'updatedAt' => $this->updated_at,
                ]),
            ],
            'links' => [
                'self' => route('platforms.show', ['platform' => $this->id]),
            ],
            'relationship' => [],
            'includes' => [
                'postCount' => $this->when(
                    request()->has('includePostsCount') == 'true',
                    $this->posts->count()
                ),
                'posts' => $this->whenLoaded('posts', function () {
                    return PostResource::collection($this->posts);
                }),
            ],
        ];
    }
}
