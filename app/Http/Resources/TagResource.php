<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="TagResource")
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
 *      )
 * }
 */
class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'title'    => $this->title,
            'color'    => $this->color,
        ];
    }
}
