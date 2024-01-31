<?php

namespace App\Http\Controllers;

use App\Http\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use JWTAuth;
class ProtectedController extends Controller
{

    public  $user;
    use AuthorizesRequests, ValidatesRequests;

    public function __construct() {
        parent::__construct();
        $this->user = JWTAuth::user();
    }

    public function result(){
        return response($this->apiResponse->result(), $this->apiResponse->getStatusCode() );
    }
}
