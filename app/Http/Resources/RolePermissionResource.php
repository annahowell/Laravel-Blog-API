<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(schema="RolePermissionResource")
 * {
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          format="int64",
 *          description="User id"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          description="Name of the role"
 *      ),
 *      @OA\Property(
 *          property="permissions",
 *          type="array",
 *          description="ADMIN VISIBLE ONLY: Permissions assigned to the role",
 *          @OA\Items(
 *              type="string",
 *              description="Permission name",
 *          )
 *      )
 * }
 */
class RolePermissionResource extends JsonResource
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
            'name'        => $this->name,
            'permissions' => $this->when(
                Auth::user()->hasRole('admin'),                   // If true
                $this->permissions->sortBy('name')->pluck('name') // Expose this
            )
        ];
    }
}
