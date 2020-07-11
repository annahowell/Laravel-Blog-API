<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use App\User;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRoleRequest;
use App\Http\Requests\UserSignupRequest;
use App\Http\Resources\LoginResource;
use App\Http\Resources\RolePermissionResource;
use App\Http\Resources\UserRolePermissionResource;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *      path="/user",
     *      operationId="index",
     *      tags={"User"},
     *      summary="Allows an admin user to return all users, their roles and associated permissions",
     *      description="Allows an admin user to return all users, their roles and associated permissions",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/UserRolePermissionResource",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource not found"
     *      )
     * )
     * @param User $user
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny');

        $users = User::with(['roles'])->get();

        $response = UserRolePermissionResource::collection($users);

        return response()->json($response, 200);
    }



    /**
     * @OA\Get(
     *      path="/user/{id}",
     *      operationId="show",
     *      tags={"User"},
     *      summary="Allows a user to get their own details, and admins to get other users details (including the
            permissions of the users role)",
     *      description="Allows a user to get their own details, and admins to get other users details (including the
            permissions of the users role)",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/UserRolePermissionResource",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource not found"
     *      )
     * )
     * @param User $user
     */
    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        $response = new UserRolePermissionResource($user);

        return response()->json($response, 200);
    }



    /**
     * @OA\Get(
     *      path="/user/roles",
     *      operationId="showRoles",
     *      tags={"User"},
     *      summary="Allows an admin to return all available roles and their permissions",
     *      description="Allows an admin to return all available roles and their permissions",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/RolePermissionResource",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     */
    public function showRoles(): JsonResponse
    {
        $this->authorize('viewRoles');

        $response = RolePermissionResource::collection(Role::all());

        return response()->json($response, 200);
    }



    /**
     * @OA\Get(
     *      path="/user/logout",
     *      operationId="logout",
     *      tags={"User"},
     *      summary="Logs out the currently logged in user",
     *      description="Logs out the currently logged in user",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     * @param Request $request
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->token();

        if (!is_null($token)) {
            $token->revoke();
        }

        return response()->json(['message' => 'Successfully logged out.'], 200);
    }



    /**
     * @OA\Post(
     *      path="/user",
     *      operationId="store",
     *      tags={"User"},
     *      summary="Signs a user up",
     *      description="Signs a user up, does not automatically log them in",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UserSignupRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable entity"
     *      )
     * )
     * @param UserSignupRequest $request
     */
    public function store(UserSignupRequest $request): JsonResponse
    {
        $userData = [
            'displayname' => $request->displayname,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ];

        $user = new User($userData);
        $user->save();

        if (User::all()->count() === 1) {
            $user->refresh()->assignRole('admin');
        } else {
            $user->refresh()->assignRole('commenter');
        }

        return response()->json(['message' => 'User successfully created.'], 201);
    }


    /**
     * @OA\Post(
     *      path="/user/login",
     *      operationId="login",
     *      tags={"User"},
     *      summary="Logs a user in",
     *      description="Logs a user in after they have successfully signed up",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UserLoginRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/LoginResource")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable entity"
     *      )
     * )
     * @param UserLoginRequest $request
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials) || !$request->user()->enabled) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $tokenResult = $request->user()->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addDays(30);
        } else {
            $token->expires_at = Carbon::now()->addDays(1);
        }

        $token->save();

        $response = new LoginResource($tokenResult);

        return response()->json($response, 200);
    }



    /**
     * @OA\Put(
     *      path="/user/{user}",
     *      operationId="update",
     *      tags={"User"},
     *      summary="Allows a user to update their own details, and admins to update other users details (including the
            role)",
     *      description="Allows a user to update their own details, and admins to update other users details (including
            the role. If a non-admin user includes a roles array in the request the roles entry in the request is ignored)",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UserRoleRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/UserRolePermissionResource")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable entity"
     *      )
     * )
     * @param UserRoleRequest $request
     * @param User            $user
     */
    public function update(UserRoleRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        if (Auth::user()->hasRole('admin') && $request->roles) {
            $user->syncRoles($request->roles);
        }

        if ($request['password']) {
            $request['password'] = Hash::make($request->password);
        }

        if ($request['enabled'] !== null && !$request['enabled']) {
            Passport::token()->where('user_id', $user->id)->update(['revoked' => true]);
        }

        $user->update($request->all());

        $response = new UserRolePermissionResource($user);

        return response()->json($response, 200);
    }



    /**
     * @OA\Delete(
     *      path="/user/{id}",
     *      operationId="destroyUser",
     *      tags={"User"},
     *      summary="Allows an admin user to disable a user's account and then logs them out",
     *      description="Allows an admin to disable a user's account and then logs them out",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="Conflict"
     *      )
     * )
     * @param User $user
     */
    public function destroy(User $user = null): JsonResponse
    {
        $this->authorize('delete', $user);

        $userToDestroy = User::findOrFail($user->id);

        // If there's one admin user, and we're trying to disable that user
        if (Role::findByName('admin', 'web')->users->count() == 1 && $userToDestroy->hasRole('admin')) {
            return response()->json([
                'message' => 'The given data was invalid.',
                "errors"  => [
                    "roles" => ["As the only admin, you may not disable your account."]
                ],
            ], 409);
        }

        $userToDestroy->enabled = false;
        $userToDestroy->save();
        $userToDestroy->token() ? $userToDestroy->token->revoke() : null;

        return response()->json(null, 204);
    }
}
