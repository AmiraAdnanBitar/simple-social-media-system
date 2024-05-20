<?php

namespace App\Http\Controllers\api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResourse;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{    
    use ApiResponceTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cate=CategoryResourse::collection(Category::all());
        return $this->ApiResponse($cate,'ok',200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    { 
        // return $request;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
        
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        $cate=new Category();
        $cate->name=$request->name;
        $cate->save();
    
        if($cate)
            return $this->ApiResponse(new CategoryResourse($cate),'stored',201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {        
        $cate=Category::find($id);

        if($cate)
        {
            $cate->name=$request->name??$cate->name;
            $cate->update();
            return $this->ApiResponse(new CategoryResourse($cate),'updated',200);
        }
        else
            return $this->ApiResponse(null,'not updated',404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $cate=Category::find($id);
        if (!$cate)
            return response()->json(['message' => 'Category not  found'], 404);
    
        if ($cate->posts->isEmpty())
        {
            $cate->delete();
            return response()->json(['message' => 'category deleted'], 200);
        }
        else
            return $this->ApiResponse(null,'This category has posts,You must delete the posts related to this category first',200);
    }
}
