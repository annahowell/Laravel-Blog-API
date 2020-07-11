<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(schema="LoginResource")
 * {
 *      @OA\Property(
 *          property="userid",
 *          type="integer",
 *          format="int64",
 *          description="User id"
 *      ),
 *      @OA\Property(
 *          property="displayname",
 *          type="string",
 *          description="The logged in user's displayname"
 *      ),
 *      @OA\Property(
 *          property="roles",
 *          type="array",
 *          description="All roles assigned to the logged in user",
 *          @OA\Items(
 *              ref="#/components/schemas/RolePermissionResource"
 *          )
 *      ),
 *      @OA\Property(
 *          property="access_token",
 *          type="string",
 *          description="Access token for logged in user"
 *      ),
 *      @OA\Property(
 *          property="token_type",
 *          type="string",
 *          description="Type of the token"
 *      ),
 *      @OA\Property(
 *          property="expires_at",
 *          type="string",
 *          description="Date and time the token will expire"
 *      ),
 * }
 */
class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return [
            'userid'       => Auth::user()->id,
            'displayname'  => Auth::user()->displayname,
            'roles'        => RolePermissionResource::collection(Auth::user()->roles->sortBy('name')),
            'access_token' => $this->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse($this->token->expires_at)->toAtomString()
        ];
    }
}
