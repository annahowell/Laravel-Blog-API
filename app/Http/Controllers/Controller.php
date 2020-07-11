<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Blog API",
     *      description="Open API v3.0 documentation for the Blog API",
     *      @OA\Contact(
     *          email="git@ahowell.io"
     *      ),
     *      @OA\License(
     *          name="MIT",
     *          url="https://opensource.org/licenses/MIT"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Blog API Server"
     * )
     */

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
