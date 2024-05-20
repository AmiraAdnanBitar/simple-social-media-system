<?php

namespace App\Http\Resources;

use App\Models\Post;
use App\Models\Comments;
use Illuminate\Http\Request;
use App\Http\Resources\PostResourse;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'comment_body'=>$this->comment_body,
            'post_id'=>new PostResourse(Post::find($this->post_id)),
        ];
    }
}
