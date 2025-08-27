<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id'              => $this->id,
            'commentable_type'=> $this->commentable_type,
            'commentable_id'  => $this->commentable_id,
            'user_id'         => $this->user_id,
            'profile_id'      => $this->profile_id,
            'parent_id'       => $this->parent_id,
            'content'         => $this->content,
            'likes_count'     => $this->likes_count,
            'replies_count'   => $this->replies_count,
            'is_edited'       => $this->is_edited,
            'status'          => $this->status,
            'edited_at'       => $this->edited_at,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
