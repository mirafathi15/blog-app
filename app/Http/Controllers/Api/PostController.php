<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('postable');

        if ($request->user() instanceof \App\Models\User) {
            $query->where('postable_type', \App\Models\User::class)
                  ->where('postable_id', $request->user()->id);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $posts = $query->latest()->get();

        return PostResource::collection($posts);
    }

    public function store(PostRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $data['image'] = $path;
        }

        if ($user instanceof Admin && $request->has('author_type')) {
            $authorType = $request->get('author_type', 'admin');
            
            if ($authorType === 'user') {
                $userId = $request->get('user_id', $user->id);
                $author = User::find($userId) ?: $user;
            } else {
                $author = $user;
            }
        } else {
            $author = $user;
        }

        $post = $author->posts()->create($data);

        return new PostResource($post->load('postable'));
    }

    public function show($id)
    {
        $post = Post::with('postable')->find($id);
    
        if (!$post) {
            return response()->json(['message' => 'There is no post with this id'], 404);
        }
    
        $user = request()->user();
    
        if ($user instanceof Admin) {
            return new PostResource($post);
        }
    
        if ($user instanceof User) {
            if ($post->postable_type === User::class && $post->postable_id === $user->id) {
                return new PostResource($post);
            }
        }
    
        return response()->json([
            'message' => 'You do not have permission to view this post.'
        ], 403);
    }
    

    public function update(PostRequest $request, $id)
    {
        $post = Post::find($id);
    
        if (!$post) {
            return response()->json(['message' => 'There is no post with this id'], 404);
        }
    
        $user = $request->user();
    
        if (!$this->canModifyPost($user, $post)) {
            return response()->json([
                'message' => 'You do not have permission to update this post.'
            ], 403);
        }
    
        $data = $request->validated();
    
        if ($request->hasFile('image')) {
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
    
            $path = $request->file('image')->store('posts', 'public');
            $data['image'] = $path;
        }
    
        $post->update($data);
    
        return new PostResource($post->load('postable'));
    }
    

    public function destroy($id)
    {
        $post = Post::find($id);
    
        if (!$post) {
            return response()->json(['message' => 'There is no post with this id'], 404);
        }
    
        $user = request()->user();
    
        if (!$this->canModifyPost($user, $post)) {
            return response()->json([
                'message' => 'You do not have permission to delete this post.'
            ], 403);
        }
    
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }
    
        $post->delete();
    
        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }
    

    private function canModifyPost($user, Post $post)
    {
        if ($user instanceof Admin) {
            return true;
        }

        if ($user instanceof User) {
            return $post->postable_type === User::class && 
                   $post->postable_id === $user->id;
        }

        return false;
    }

    private function canViewPost($user, Post $post)
    {
        if ($user instanceof Admin) {
            return true;
        }

        if ($user instanceof User) {
            return $post->postable_type === User::class && 
                   $post->postable_id === $user->id;
        }

        return false;
    }
}