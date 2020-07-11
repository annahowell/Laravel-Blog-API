<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\TagRequest;
use App\Http\Resources\TagResource;
use App\Http\Resources\TagWithPostsResource;

class TagController extends Controller
{
    /**
     * @OA\Get(
     *      path="/tags",
     *      operationId="index",
     *      tags={"Tags"},
     *      summary="Gets all tags",
     *      description="Returns all tags",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  ref="#/components/schemas/TagResource",
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
        $response = TagResource::collection(Tag::all()->sortBy('title'));

        return response()->json($response, 200);
    }


    /**
     * @OA\Get(
     *      path="/tags/{id}",
     *      operationId="show",
     *      tags={"Tags"},
     *      summary="Gets a single tag by tag id",
     *      description="Returns a single tag by tag id",
     *      @OA\Parameter(
     *          name="id",
     *          description="Tag id",
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
     *              ref="#/components/schemas/TagResource",
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
    public function show($id): JsonResponse
    {
        $response = new TagResource(Tag::findOrFail($id));

        return response()->json($response, 200);
    }


    /**
     * @OA\Get(
     *      path="/tags/{id}/posts",
     *      operationId="showWithPosts",
     *      tags={"Tags"},
     *      summary="Gets a single tag by tag id and any posts related to it",
     *      description="Gets a single tag by tag id and any posts related to it",
     *      @OA\Parameter(
     *          name="id",
     *          description="Tag id",
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
     *              ref="#/components/schemas/TagWithPostsResource",
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
    public function showWithPosts($id): JsonResponse
    {
        $response = new TagWithPostsResource(Tag::findOrFail($id));

        return response()->json($response, 200);
    }


    /**
     * @OA\Post(
     *      path="/tags",
     *      operationId="store",
     *      tags={"Tags"},
     *      summary="Creates a new tag",
     *      description="Creates a new tag and then returns the tag",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/TagRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/TagResource")
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
     * @param TagRequest $request
     */
    public function store(TagRequest $request): JsonResponse
    {
        $this->authorize('create', Tag::class);

        $response = new TagResource(Tag::create($request->all()));

        return response()->json($response, 201);
    }


    /**
     * @OA\Put(
     *      path="/tags/{id}",
     *      operationId="update",
     *      tags={"Tags"},
     *      summary="Updates an existing tag by tag id",
     *      description="Updates an existing tag by tag id and returns the result",
     *      @OA\Parameter(
     *          name="id",
     *          description="Tag id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/TagRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/TagResource")
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
     * @param TagRequest $request
     * @param Tag $tag
     */
    public function update(TagRequest $request, Tag $tag): JsonResponse
    {
        $this->authorize('update', $tag);

        $tag->update($request->all());

        return response()->json(new TagResource($tag), 200);
    }


    /**
     * @OA\Delete(
     *      path="/tags/{id}",
     *      operationId="destroy",
     *      tags={"Tags"},
     *      summary="Deletes a tag by tag id",
     *      description="Deletes a tag by tag id and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="Tag id",
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
     * @param Tag $tag
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $this->authorize('delete', $tag);

        $tag->delete();

        return response()->json(null, 204);
    }
}
