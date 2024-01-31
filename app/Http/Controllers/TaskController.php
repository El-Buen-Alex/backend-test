<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;




class TaskController extends ProtectedController
{

    /**
         * @OA\Get(
         *     path="/api/v1/taks",
         *     summary="Get a list of taks that belongs the user logged in",
         *     tags={"taks"},
         * @OA\Parameter(
         *         description="parameter that allows filter by name task",
         *         in="query",
         *         name="s",
         *         required=false,
         *         @OA\Schema(type="string"),
         *         @OA\Examples(example="string", value="Important", summary="Hi this homework is important")
         *     ),
         *     @OA\Response(response=200, description="Show all Tasks"),
         *     @OA\Response(response=400, description="Show an object that containts reason, message and type error")
         * )
     */
    public function index(Request $request){
        try{
            $q=[
                's'=>$request->has('s')? $request->s:''
            ];

            $query=Task::query();
            $query->with(['user', 'status']);
            $query->where('user_id', $this->user->id);
            if(strlen($q['s'])>0){
                $query->where('name', 'like','%'.$q['s'].'%');
            }

            $data=$query->get();
            $this->apiResponse->addData('tasks', $data);


        }catch(Exception $e){
            $this->apiResponse->addErrorMessage('An error has been ocurred', $e->getMessage(),400);
            $this->apiResponse->addData('line', $e->getLine());
        }
        return $this->result();
    }

    /**
         * @OA\Post(
         *     path="/api/v1/taks",
         *     summary="Create a new task",
         *     tags={"taks"},
         * @OA\Parameter(
         *         description="set the task name",
         *         in="query",
         *         name="name",
         *         required=true,
         *         @OA\Schema(type="string"),
         *     ),
         *  @OA\Parameter(
         *         description="set the task description",
         *         in="query",
         *         name="description",
         *         required=true,
         *         @OA\Schema(type="string"),
         *     ),
         *  @OA\Parameter(
         *         description="set user that belongs the task",
         *         in="query",
         *         name="user_id",
         *         required=true,
         *         @OA\Schema(type="int"),
         *     ),
         *  @OA\Parameter(
         *         description="set status task",
         *         in="query",
         *         name="status_id",
         *         required=true,
         *         @OA\Schema(type="int"),
         *     ),
         *     @OA\Response(response=200, description="Task created succefully"),
         *     @OA\Response(response=400, description="Show an object that containts reason, message and type error")
         * )
     */

    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'name' => 'required',
                'description' => 'required',
                'user_id' => 'required|exists:users,id',
                'status_id' => 'required|exists:task_status,id'
            ], [
                'name.required'=>'The task name is required',
                'description.required'=>'The task description is required',
                'user_id.required'=>'The user is required',
                'user_id.exists'=>'The user doesnt find in database',
                'status_id.required'=>'The user is required',
                'status_id.exists'=>'The status doesnt find in database',
            ]);
            if($validator->fails()){
                throw new Exception($validator->messages()->first());
            }

            $data=$request->all();

            $validateRepeat=Task::where('name', $data['name'] )->first();
            if($validateRepeat){
                throw new Exception('The task name selected had been created before');
            }

            $task=Task::create([
                'name'=>$data['name'],
                'user_id'=>$data['user_id'],
                'status_id'=>$data['status_id'],
                'description'=>$data['description'],
            ]);

            $task->load('status');
            $task->load('user');

            $this->apiResponse->addSuccessMessage('Task created succefully', 'The task '.$task->name.' had been created without problem', );

            $this->apiResponse->addData('task', $task);

        }catch(Exception $e){
            $this->apiResponse->addErrorMessage('An error has been ocurred', $e->getMessage(),);
            $this->apiResponse->addData('line', $e->getLine());
        }
        return $this->result();
    }

        /**
         * @OA\Put(
         *     path="/api/v1/taks",
         *     summary="Update an existent task",
         *     tags={"taks"},
          * @OA\Parameter(
         *         description="the task identifier to update data",
         *         in="path",
         *         name="task_id",
         *         required=true,
         *         @OA\Schema(type="int"),
         *     ),
         * @OA\Parameter(
         *         description="set the task name",
         *         in="query",
         *         name="name",
         *         required=true,
         *         @OA\Schema(type="string"),
         *     ),
         *  @OA\Parameter(
         *         description="set the task description",
         *         in="query",
         *         name="description",
         *         required=true,
         *         @OA\Schema(type="string"),
         *     ),
         *  @OA\Parameter(
         *         description="set user that belongs the task",
         *         in="query",
         *         name="user_id",
         *         required=true,
         *         @OA\Schema(type="int"),
         *     ),
         *  @OA\Parameter(
         *         description="set status task",
         *         in="query",
         *         name="status_id",
         *         required=true,
         *         @OA\Schema(type="int"),
         *     ),
         *     @OA\Response(response=200, description="Task updated succefully"),
         *     @OA\Response(response=400, description="Show an object that containts reason, message and type error")
         * )
     */
    public function update(Request $request, $id){
        try{
            DB::beginTransaction();
            $validator = Validator::make($request->all(),[
                'name' => 'required',
                'user_id' => 'required|exists:users,id',
                'description' => 'required',
                'status_id' => 'required|exists:task_status,id'
            ], [
                'description.required'=>'The task description is required',
                'name.required'=>'The task name is required',
                'user_id.required'=>'The user is required',
                'user_id.exists'=>'The user doesnt find in database',
                'status_id.required'=>'The user is required',
                'status_id.exists'=>'The status doesnt find in database',
            ]);
            if($validator->fails()){
                throw new Exception($validator->messages()->first());
            }

            $data=$request->all();


            $task=Task::find($id);
            if(!$task){
                throw new Exception('The task name selected had not been found in database');
            }
            if($task->user_id!==$this->user->id){
                throw new Exception('The task doesnt belongs you');
            }


            $validateRepeat=Task::where('name', $data['name'] )->where('id', '!=', $id)->count();
            if($validateRepeat>0){
                throw new Exception('The task name selected had been created before');
            }

            $task->update($data);
            $task->refresh();
            $task->load('status');
            $task->load('user');

            $this->apiResponse->addSuccessMessage('Task updated succefully', 'The task '.$task->name.' had been updated without problem', );

            $this->apiResponse->addData('task', $task);
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
         *     path="/api/v1/taks",
         *     summary="Delete an existent task",
         *     tags={"taks"},
          * @OA\Parameter(
         *         description="the task identifier to delete",
         *         in="path",
         *         name="task_id",
         *         required=true,
         *         @OA\Schema(type="int"),
         *     ),
         *     @OA\Response(response=200, description="Task deleted succefully"),
         *     @OA\Response(response=400, description="Show an object that containts reason, message and type error")
         * )
     */
    public function destroy($id){
        try{
            DB::beginTransaction();

            $task=Task::find($id);
            if(!$task){
                throw new Exception('The task name selected had not been found in database');
            }
            if($task->user_id!==$this->user->id){
                throw new Exception('The task doesnt belongs you');
            }
            $task->delete();
            $this->apiResponse->addSuccessMessage('Task deleted succefully', 'The task '.$task->name.' had been deleted without problem', );

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            $this->apiResponse->addErrorMessage('An error has been ocurred', $e->getMessage(),);
            $this->apiResponse->addData('line', $e->getLine());
        }
        return $this->result();
    }
}
