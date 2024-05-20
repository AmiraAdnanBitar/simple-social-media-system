<?php

namespace App\Http\Controllers\api;

use App\Models\Post;
use App\Models\Comments;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResourse;
use App\Http\Resources\CommentResourse;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{
    use ApiResponceTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user =auth()->user();
        $comments = $user->comments;
        return $this->ApiResponse(['user'=> new UserResourse($user), 'comment'=>CommentResourse::collection($comments) ],'ok',200);
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
            'post_id' => 'required|string|max:255|exists:posts,id',
            'comment_body'=>'required|string|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $user =auth()->user();
        $post = Post::find($request->post_id);
        $comment = new Comments([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'comment_body' => $request->comment_body,
        ]);
    
        $comment->save();
    
        return response()->json(['message' => 'Comment saved successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($post_id)
    {  
        $post = Post::find($post_id);
        $comments = $post->comments;
        return $this->ApiResponse([ CommentResourse::collection($comments) ],'ok',201);
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comments $comments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $comment_id)
    {
        $validator = Validator::make($request->all(), [

            'comment_body'=>'required|string|max:255'
        ]);
        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $comment = Comments::find($comment_id); 

        if (!$comment)
            return response()->json(['message' => 'Comment not  found'], 404);

        $user = $comment->user; // جلب المستخدم المرتبط بالتعليق 
        $user2 =auth()->user();
        if( $user2->id== $user->id)
        {    
            $comment->comment_body=$request->comment_body?? $comment->comment_body;
            $comment->update();
            return response()->json(['message' => 'Comment updated'], 200);
        }
        else
            return response()->json(['message' =>'You do not have the permission to update this comment'],401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $comment_id)
    { 
        $comment = Comments::find($comment_id); 
        if (!$comment)
            return response()->json(['message' => 'Comment not  found'], 404);
    

        $user = $comment->user; // جلب المستخدم المرتبط بالتعليق 
        $user2 =auth()->user();
        if( $user2->id== $user->id)
        {
            $comment->delete();
            return response()->json(['message' => 'The Comment deleted'],200);
        }
        else 
            return response()->json(['message' =>'you are not the auther,You do not have the permission to delete this comment'],401);

    }
}
