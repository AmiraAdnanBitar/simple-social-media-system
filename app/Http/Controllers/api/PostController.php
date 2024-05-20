<?php

namespace App\Http\Controllers\api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResourse;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{ 
    use ApiResponceTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
            
            $posts=PostResourse::collection(Post::all());
            return $this->ApiResponse($posts,'ok',200);
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
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:posts,title',
            'body' => 'required|string|max:2550',
            'category_id' => 'required|string|exists:categories,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $post=new Post();
        $user =auth()->user();
        $post->title=$request->title;
        $post->body=$request->body;
        $post->category_id=$request->category_id;
        $post->user_id=$user->id;

        $post->save();
    
        if($post)
            return $this->ApiResponse(new PostResourse($post),'stored',201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)//Post $post)
    {

        $post=Post::find($id);

        if($post)
        {
            $post->title=$request->title??$post->title;
            $post->body=$request->body??$post->body;
            $post->category_id=$request->category_id??$post->category_id;
            $post->update();
            return $this->ApiResponse(new PostResourse($post),'updated',200);
        }
        else
            return $this->ApiResponse(null,'not updated',404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $post=Post::find($id);
        if(!$post)
            return $this->ApiResponse(null,'this Post is not found',404);


        if ($post->comments->isEmpty())
        {
            $user =auth()->user();
            
            if($user->id==$post->user_id)
            {
                $post->delete();
                return response()->json(['message' => 'the post is deleted'], 200);
            }
            else
                return response()->json(['message' => 'you can not delete this post ,you are not the auther'], 200);
        }
        else
            return $this->ApiResponse(null,'This Post has comments,You must delete the comments related to this post first',200);
    
    }
}
