<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class UserController extends ProtectedController
{
    /**
         * @OA\Get(
         *     path="/api/v1/users",
         *     summary="Get an users list that belongs the user logged in",
         *     tags={"users"},
         * @OA\Parameter(
         *         description="parameter that allows filter by name task",
         *         in="query",
         *         name="s",
         *         required=false,
         *         @OA\Schema(type="string"),
         *         @OA\Examples(example="string", value="alfred", summary="Alfred toni")
         *     ),
         *     @OA\Response(response=200, description="Show all users"),
         *     @OA\Response(response=400, description="Show an object that containts reason, message and type error")
         * )
     */
    public function index(Request $request){
        try{
            $q=[
                's'=>$request->has('s')? $request->s:''
            ];

            $query=User::query();
            if(strlen($q['s'])>0){
                $query->where('name', 'like','%'.$q['s'].'%');
            }

            $data=$query->get();
            $this->apiResponse->addData('User', $data);


        }catch(Exception $e){
            $this->apiResponse->addErrorMessage('An error has been ocurred', $e->getMessage(),);
            $this->apiResponse->addData('line', $e->getLine());
        }
        return $this->result();
    }

    /**
         * @OA\Post(
         *     path="/api/v1/users",
         *     summary="Create a new user",
         *     tags={"users"},
         * @OA\Parameter(
         *         description="set the user name",
         *         in="query",
         *         name="name",
         *         required=true,
         *         @OA\Schema(type="string"),
         *     ),
         *  @OA\Parameter(
         *         description="set the user email",
         *         in="query",
         *         name="email",
         *         required=true,
         *         @OA\Schema(type="string"),
         *     ),
         *     @OA\Response(response=200, description="User created succefully"),
         *     @OA\Response(response=400, description="Show an object that containts reason, message and type error")
         * )
     */
    public function store(Request $request){
        try{
            DB::beginTransaction();
            $validator = Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
            ], [
                'name.required'=>'The name is required',
                'email.required'=>'The email is required',
                'email.unique'=>'The email have to be unique',
                'email.email'=>'The email must to be email and have an email format',
            ]);
            if($validator->fails()){
                throw new Exception($validator->messages()->first());
            }

            $data=[
                'name'=>$request->name,
                'email'=>$request->email,
                //'password'=,
            ];
            if($request->has('password')){
                $data['password']=Hash::make($request->password);
            }

            $user=User::create($data);

            if(!$user){
                throw new Exception('The user selected had not been found in database');
            }

            $token = JWTAuth::fromUser($user);
            $this->apiResponse->addSuccessMessage('User created succefully', 'The task '.$user->name.' had been created without problem');
            $this->apiResponse->addData('user', $user);
            $this->apiResponse->addData('token', $token);



            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            $this->apiResponse->addErrorMessage('An error has been ocurred', $e->getMessage(),);
            $this->apiResponse->addData('line', $e->getLine());
        }
        return $this->result();
    }


    /**
         * @OA\Put(
         *     path="/api/v1/users",
         *     summary="Update an existing user",
         *     tags={"users"},
         * @OA\Parameter(
         *         description="the user identifier to update data",
         *         in="path",
         *         name="user_id",
         *         required=true,
         *         @OA\Schema(type="int"),
         *     ),
         * @OA\Parameter(
         *         description="set the user name",
         *         in="query",
         *         name="name",
         *         required=true,
         *         @OA\Schema(type="string"),
         *     ),
         *  @OA\Parameter(
         *         description="set the user email",
         *         in="query",
         *         name="email",
         *         required=true,
         *         @OA\Schema(type="string"),
         *     ),
         *     @OA\Response(response=200, description="User created succefully"),
         *     @OA\Response(response=400, description="Show an object that containts reason, message and type error")
         * )
     */

    public function update(Request $request, $id){
        try{
            DB::beginTransaction();
            $validator = Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'name.required'=>'The name is required',
                'email.required'=>'The email is required',
                'email.email'=>'The email must to be email and have an email format',
                'password.required'=>'The password is required',
            ]);
            if($validator->fails()){
                throw new Exception($validator->messages()->first());
            }

            $data=[
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
            ];

            $data=$request->all();
            $user=User::find($id);



            $user->update($data);
            $user->refresh();
            $token = JWTAuth::fromUser($user);
            $this->apiResponse->addSuccessMessage('User updated succefully', 'The task '.$user->name.' had been updated without problem');
            $this->apiResponse->addData('user', $user);
            $this->apiResponse->addData('token', $token);



            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            $this->apiResponse->addErrorMessage('An error has been ocurred', $e->getMessage(),);
            $this->apiResponse->addData('line', $e->getLine());
        }
        return $this->result();
    }
    /**
         * @OA\Delete(
         *     path="/api/v1/users",
         *     summary="Delete an existent user",
         *     tags={"users"},
          * @OA\Parameter(
         *         description="the user identifier to delete",
         *         in="path",
         *         name="task_id",
         *         required=true,
         *         @OA\Schema(type="int"),
         *     ),
         *     @OA\Response(response=200, description="User deleted succefully"),
         *     @OA\Response(response=400, description="Show an object that containts reason, message and type error")
         * )
     */

    public function destroy($id){
        try{
            DB::beginTransaction();

            $user=User::find($id);
            if(!$user){
                throw new Exception('The user selected had not been found in database');
            }

            $user->delete();
            $this->apiResponse->addSuccessMessage('User deleted succefully', 'The user '.$user->name.' had been deleted without problem', );

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            $this->apiResponse->addErrorMessage('An error has been ocurred', $e->getMessage(),);
            $this->apiResponse->addData('line', $e->getLine());
        }
        return $this->result();
    }
}
