<?php

namespace App\Http\Controllers;

use App\Http\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Task Controller",
 *      description="Here you can found the main methods that TaskController has.",
 *     
 *      @OA\Contact(
 *          email="alexispincay005@gmail.com"
 *      )
 * )
 */
class Controller extends BaseController
{

    protected $apiResponse;
    use AuthorizesRequests, ValidatesRequests;

    public function __construct() {
        $this->apiResponse = new ApiResponse();
    }

    public function result(){
        return response($this->apiResponse->result(), $this->apiResponse->getStatusCode() );
    }
}
