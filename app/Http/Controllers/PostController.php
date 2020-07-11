<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostWithCommentsResource;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *      path="/posts",
     *      operationId="index",
     *      tags={"Posts"},
     *      summary="Gets all posts",
     *      description="Returns all posts along with user that created it and any associated tags",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  ref="#/components/schemas/PostResource",
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      )
     * )
     */
    public function index(): JsonResponse
    {
        $posts = Post::with(['user', 'tags', 'comments'])->get();

        $response = PostResource::collection($posts);

        return response()->json($response, 200);
    }


    /**
     * @OA\Get(
     *      path="/posts/{id}",
     *      operationId="show",
     *      tags={"Posts"},
     *      summary="Gets a single post by post id",
     *      description="Returns a single post by post id along with user that created it and any associated tags",
     *      @OA\Parameter(
     *          name="id",
     *          description="Post id",
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
     *              ref="#/components/schemas/PostResource",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource not found"
     *          )
     * )
     * @param $id
     */
    public function show($id): JsonResponse
    {
        $response = new PostResource(Post::findOrFail($id));

        return response()->json($response, 200);
    }


    /**
     * @OA\Get(
     *      path="/posts/{id}/comments",
     *      operationId="showWithComments",
     *      tags={"Posts"},
     *      summary="Gets a single post by post id and any comments",
     *      description="Returns a single post by post id along with user that created it, any associated tags and its comments",
     *      @OA\Parameter(
     *          name="id",
     *          description="Post id",
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
     *              ref="#/components/schemas/PostWithCommentsResource",
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
     * @param $id
     */
    public function showWithComments($id): JsonResponse
    {
        $response = new PostWithCommentsResource(Post::findOrFail($id));

        return response()->json($response, 200);
    }


    /**
     * @OA\Post(
     *      path="/posts",
     *      operationId="store",
     *      tags={"Posts"},
     *      summary="Creates a new post and establishes links to associated tags",
     *      description="Creates a new post, establishes links to associated tags and then returns the post",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/PostRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/PostResource")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable entity"
     *      )
     * )
     * @param PostRequest $request
     */
    public function store(PostRequest $request): JsonResponse
    {
        $this->authorize('create', Post::class);

        $request->merge(['user_id' => Auth::id()]);

        $post = Post::create($request->all());
        $post->tags()->sync($request->tags, false);

        return response()->json(new PostResource($post), 201);
    }



    /**
     * @OA\Put(
     *      path="/posts/{id}",
     *      operationId="update",
     *      tags={"Posts"},
     *      summary="Updates an existing post by post id",
     *      description="Updates an existing post by post id and any links to associated tags and returns the result. If
    a tag id is not included in the update request, its association with the post will be removed",
     *      @OA\Parameter(
     *          name="id",
     *          description="Post id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/PostRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/PostResource")
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
     * @param PostRequest $request
     * @param Post $post
     */
    public function update(PostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $post->update($request->all());

        // Detaching so if the tags are not present in the update they're no longer associated with the post
        $post->tags()->sync($request->tags, true);

        return response()->json(new PostResource($post), 200);
    }



    /**
     * @OA\Delete(
     *      path="/posts/{id}",
     *      operationId="destroy",
     *      tags={"Posts"},
     *      summary="Deletes a post by post id",
     *      description="Deletes a post by post id and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="Post id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
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
     *      )
     * )
     * @param Post $post
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(null, 204);
    }
}
