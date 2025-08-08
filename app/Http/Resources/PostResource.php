<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'author' => [
                'id' => $this->postable->id,
                'name' => $this->postable->name,
                'type' => $this->postable instanceof \App\Models\Admin ? 'admin' : 'user',
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}