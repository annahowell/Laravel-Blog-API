<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="TagWithPostsResource")
 * {
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          format="int64",
 *          description="Tag id"
 *      ),
 *      @OA\Property(
 *          property="title",
 *          type="string",
 *          description="Tag title"
 *      ),
 *      @OA\Property(
 *          property="color",
 *          type="string",
 *          description="Tag color"
 *      ),
 *      @OA\Property(
 *          property="posts",
 *          type="array",
 *          description="Posts associated with the tag",
 *          @OA\Items(
 *              ref="#/components/schemas/PostResource",
 *          ),
 *      )
 * }
 */

class TagWithPostsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id'     => $this->id,
            'title'  => $this->title,
            'color'  => $this->color,
            'posts'  => PostResource::collection($this->posts->sortBy('created_at')),
        ];
    }
}
