<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'type' => 'post',
            'attributes' => [
                'title' => $this->title,
                'scheduledAt' => $this->scheduled_at,
                'createdAt' => $this->created_at,
                'status' => $this->status,
                'content' => $this->content,
                'imageUrl' => $this->image_url,
                'updatedAt' => $this->updated_at,
            ],
            'links' => [
                'self' => route('posts.show', ['post' => $this->id]),
            ],
            'relationship' => [
                'poster' => [
                    'data' => [
                        'id' => $this->user_id,
                        'type' => 'user',
                    ],
                    'links' => [
                        'self' => route('users.show', ['user' => $this->user_id]),
                    ],
                ],
            ],
            'includes' => [
                'poster' => $this->whenLoaded('poster', function () {
                    return new UserResource($this->poster);
                }),
                'platforms' => $this->whenLoaded('platforms', function () {
                    return PlatformResource::collection($this->platforms);
                }),
            ],
        ];
    }
}
