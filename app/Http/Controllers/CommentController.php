<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CommentResource;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;


class CommentController extends Controller
{
    /**
     * @OA\Get(
     *      path="/comments/{id}",
     *      operationId="show",
     *      tags={"Comments"},
     *      summary="Gets a single comment by comment id",
     *      description="Returns a single comment by comment id, including the id of the post it relates to and the user that made it",
     *      @OA\Parameter(
     *          name="id",
     *          description="Comment id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *                  ref="#/components/schemas/CommentResource",
     *         )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Resource not found"
     *     )
     * )
     * @param $id
     */
    public function show($id): JsonResponse
    {
        $response = new CommentResource(Comment::findOrFail($id));

        return response()->json($response, 200);
    }


    /**
     * @OA\Post(
     *      path="/comments",
     *      operationId="store",
     *      tags={"Comments"},
     *      summary="Creates a new comment",
     *      description="Creates a new comment and then returns the comment, including the id of the post it relates to
            and the user that made it",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CommentStoreRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/CommentResource")
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
     * @param CommentStoreRequest $request
     */
    public function store(CommentStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Comment::class);

        $request->merge(['user_id' => auth()->id()]);

        $comment = Comment::create($request->all());

        return response()->json(new CommentResource($comment), 201);
    }



    /**
     * @OA\Put(
     *      path="/comments/{id}",
     *      operationId="update",
     *      tags={"Comments"},
     *      summary="Updates an existing comment by comment id",
     *      description="Updates an existing comment by comment id and then returns the comment, including the id of the
    post it relates to and the user that made it",
     *      @OA\Parameter(
     *          name="id",
     *          description="Comment id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CommentUpdateRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/CommentResource")
     *       ),
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
     * @param CommentUpdateRequest $request
     * @param Comment $comment
     */
    public function update(CommentUpdateRequest $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $comment->update($request->all());

        return response()->json(new CommentResource($comment), 200);
    }


    /**
     * @OA\Delete(
     *      path="/comments/{id}",
     *      operationId="destroy",
     *      tags={"Comments"},
     *      summary="Deletes a comment by comment id",
     *      description="Deletes an existing comment by comment id and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="Comment id",
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
     *       ),
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
     * @param Comment $comment
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(null, 204);
    }
}
