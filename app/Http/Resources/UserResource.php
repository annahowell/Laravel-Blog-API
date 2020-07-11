<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(schema="UserResource")
 * {
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          format="int64",
 *          description="User id"
 *      ),
 *      @OA\Property(
 *          property="displayname",
 *          type="string",
 *          description="Display name of the user"
 *      )
 * }
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'displayname' => $this->displayname,
        ];
    }
}
