<?php

namespace App\Http\Controllers;

use App\Category;
use App\Helpers\ApiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return  mixed
     */
    public function index()
    {
        $data = Category::where('user_id', auth()->user()->id)->get(['id','name']);
        return ApiHelper::GenerateApiResponse(true, 200, 'success', $data);
    }


    /**
     * Store new Category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  mixed
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => Rule::unique('categories')->where(function ($query) use ($request) {
                return $query->where('name', $request->name)
                    ->where('user_id', auth()->user()->id);
            })
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return ApiHelper::GenerateApiResponse(false, 401, $validator->errors()->first());
        }
        try {
            $data = [
                'name' => trim($request->name),
                'user_id' => auth()->user()->id,
            ];
            $store = Category::create($data);
            return ApiHelper::GenerateApiResponse(true, 200, 'success', $store);
        } catch (\Exception $e) {
            return ApiHelper::GenerateApiResponse(false, 400, $e->getMessage());
        }
    }


    /**
     * Update Category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  mixed
     */
    public function update(Request $request)
    {
        $category = Category::UserCategory(auth()->user()->id,$request->id)->first();
        if(!empty($category->id)){
            if($category->name == $request->name){
                $rules = [
                    'name' => 'required|max:50'
                ];
            } else {
                $rules = [
                    'name' => Rule::unique('categories')->where(function ($query) use ($request) {
                        return $query->where('name', $request->name)
                            ->where('user_id', auth()->user()->id);
                    })
                ];
            }
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()){
                return ApiHelper::GenerateApiResponse(false, 401, $validator->errors()->first());
            }
            try {
                $category->update(['name' => $request->name]);
                return ApiHelper::GenerateApiResponse(true, 200, 'updated');
            } catch (\Exception $e) {
                return ApiHelper::GenerateApiResponse(false, 400, $e->getMessage());
            }
        }
        return ApiHelper::GenerateApiResponse(false, 400, 'Record does not exist!');
    }

    /**
     * Delete Category.
     *
     * @param  int  $id
     * @return  mixed
     */
    public function destroy($id)
    {
        try {
            $category = Category::UserCategory(auth()->user()->id,$id)->first();
            if(!empty($category->id)){
                $category->delete();
                return ApiHelper::GenerateApiResponse(true, 200, 'Deleted');
            }
            return ApiHelper::GenerateApiResponse(false, 400, 'Category does not exist');
        } catch (\Exception $e) {
            return ApiHelper::GenerateApiResponse(false, 400, $e->getMessage());
        }
    }
}
