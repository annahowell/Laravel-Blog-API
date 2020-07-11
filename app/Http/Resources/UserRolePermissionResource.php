<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(schema="UserRolePermissionResource")
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
 *      ),
 *      @OA\Property(
 *          property="enabled",
 *          type="boolean",
 *          description="ADMIN VISIBLE ONLY: Whether or not the user is enabled"
 *      ),
 *      @OA\Property(
 *          property="roles",
 *          type="array",
 *          description="All roles assigned to the user",
 *          @OA\Items(
 *              ref="#/components/schemas/RolePermissionResource"
 *          )
 *      ),
 * }
 */

class UserRolePermissionResource extends JsonResource
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
            'enabled' => $this->when(
                Auth::user()->hasRole('admin'), // If true
                (bool) $this->enabled           // Expose this
            ),
            'roles'       => RolePermissionResource::collection($this->roles->sortBy('name')),
        ];
    }
}
