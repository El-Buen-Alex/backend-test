<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
class LoginController extends Controller
{
    public function login(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'email'=>'required|email',
                'password'=>'required',
            ])->stopOnFirstFailure(true);

            if($validator->fails()){
                throw new Exception($validator->messages()->first());
            }

            $credentials = request(['email', 'password']);
            $token = auth()->attempt($credentials);
            if (! $token) {
                throw new Exception('The user doesnt exists on database');
            }
            $user = JWTAuth::user();
            $this->apiResponse->addData('token', $token);
            $this->apiResponse->addData('user', $user);

        }catch(Exception $e){
            $this->apiResponse->addErrorMessage('An error has been ocurred', $e->getMessage(),);
            $this->apiResponse->addData('line', $e->getLine());
        }
        return $this->apiResponse->result();
    }
}
