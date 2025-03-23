<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'type' => 'user',
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'createdAt' => $this->created_at,
                'acceptsTerms' => $this->accepts_terms,
                'emailVerifiedAt' => $this->email_verified_at,
                'roles' => $this->getRoleNames(),
                $this->mergeWhen($request->routeIs('users.*'), [
                    'updatedAt' => $this->updated_at,
                ]),
            ],
            'links' => [
                'self' => route('users.show', ['user' => $this->id]),
            ],
            'relationship' => [],
            'includes' => [
                'posts' => $this->whenLoaded('posts', function () {
                    return PostResource::collection($this->posts);
                }),
                'postCount' => $this->when($request->routeIs('users.index'), $this->posts()->count()),
                'disallowedPlatforms' => PlatformResource::collection($this->disallowedPlatforms),
            ],
        ];
    }
}
