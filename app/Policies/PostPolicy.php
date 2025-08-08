<?php
namespace App\Policies;

use App\Models\Admin;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function view($user, Post $post)
    {
        return $user instanceof Admin || 
               ($user instanceof User && $post->postable_id === $user->id);
    }

    public function update($user, Post $post)
    {
        return $user instanceof Admin || 
               ($user instanceof User && $post->postable_id === $user->id);
    }

    public function delete($user, Post $post)
    {
        return $user instanceof Admin || 
               ($user instanceof User && $post->postable_id === $user->id);
    }
}