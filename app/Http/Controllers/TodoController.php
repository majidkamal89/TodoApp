<?php

namespace App\Http\Controllers;

use App\Category;
use App\Helpers\ApiHelper;
use App\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TodoController extends Controller
{
    /**
     * Load all Task of LoggedIn User.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  mixed
     */
    public function index(Request $request)
    {
        if(!empty($request->type) && in_array($request->type, ['day','month']) && !empty($request->search)){
            $function = ($request->type == 'day') ? 'TaskByDate':'TaskByMonth';
            $data = Todo::with(['category'])->$function(auth()->user()->id,$request->search)
                ->orderBy('date_time', 'DESC')
                ->select(['id','name','description','date_time','category_id','status'])
                ->latest()->paginate(Todo::PER_PAGE);
        } else {
            $data = Todo::with(['category'])->where('user_id', auth()->user()->id)
                ->orderBy('date_time', 'DESC')
                ->select(['id','name','description','date_time','category_id','status'])
                ->latest()->paginate(Todo::PER_PAGE);
        }

        $tasks = ApiHelper::filterData($data);
        return ApiHelper::GenerateApiResponse(true, 200, 'success', $tasks);
    }


    /**
     * Store new Task
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  mixed
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => Rule::unique('todos')->where(function ($query) use ($request) {
                return $query->where('name', $request->name)
                    ->where('user_id', auth()->user()->id);
            }).'|min:2|max:50',
            'description' => 'max:200',
            'date_time' => 'required|date_format:Y-m-d H:i:s',
            'category_id' => 'required|integer',
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return ApiHelper::GenerateApiResponse(false, 401, $validator->errors()->first());
        }
        try {
            $category_exist = Category::UserCategory(auth()->user()->id,$request->category_id)->first();
            if(empty($category_exist)){
                return ApiHelper::GenerateApiResponse(false, 401, 'Category does not exist');
            }
            $data = [
                'name' => trim($request->name),
                'user_id' => auth()->user()->id,
                'description' => trim($request->description),
                'date_time' => $request->date_time,
                'category_id' => $request->category_id,
            ];
            $store = Todo::create($data);
            $store->category;
            return ApiHelper::GenerateApiResponse(true, 200, 'success', $store);
        } catch (\Exception $e) {
            return ApiHelper::GenerateApiResponse(false, 400, $e->getMessage());
        }
    }



    /**
     * Update Task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  mixed
     */
    public function update(Request $request)
    {
        $task = Todo::UserTask(auth()->user()->id,$request->id)->first();
        if(!empty($task->id)){
            if($task->name == $request->name){
                $rules = [
                    'name' => 'required|min:2|max:50',
                    'description' => 'max:200',
                    'date_time' => 'required|date_format:Y-m-d H:i:s',
                    'category_id' => 'required|integer'
                ];
            } else {
                $rules = [
                    'name' => Rule::unique('todos')->where(function ($query) use ($request) {
                            return $query->where('name', $request->name)
                                ->where('user_id', auth()->user()->id);
                        }).'|min:2|max:50',
                    'description' => 'max:200',
                    'date_time' => 'required|date_format:Y-m-d H:i:s',
                    'category_id' => 'required|integer',
                ];
            }
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()){
                return ApiHelper::GenerateApiResponse(false, 401, $validator->errors()->first());
            }
            try {
                $data = [
                    'name' => $request->name,
                    'description' => $request->description,
                    'date_time' => $request->date_time,
                    'category_id' => $request->category_id,
                    'status' => $request->status ?? 0
                ];
                $task->update($data);
                $data['id'] = $task->id;
                $data['user_id'] = $task->user_id;
                return ApiHelper::GenerateApiResponse(true, 200, 'updated', $data);
            } catch (\Exception $e) {
                return ApiHelper::GenerateApiResponse(false, 400, $e->getMessage());
            }
        }
        return ApiHelper::GenerateApiResponse(false, 400, 'Record does not exist!');
    }

    /**
     * Delete task.
     *
     * @param  int  $id
     * @return  mixed
     */
    public function destroy($id)
    {
        try {
            $task = Todo::UserTask(auth()->user()->id,$id)->first();
            if(!empty($task->id)){
                $task->delete();
                return ApiHelper::GenerateApiResponse(true, 200, 'Deleted');
            }
            return ApiHelper::GenerateApiResponse(false, 400, 'Task does not exist');
        } catch (\Exception $e) {
            return ApiHelper::GenerateApiResponse(false, 400, $e->getMessage());
        }
    }
}
