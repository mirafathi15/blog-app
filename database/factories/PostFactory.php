<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        $postableTypes = [User::class, Admin::class];
        $postableType = $this->faker->randomElement($postableTypes);
        
        // Create a user or admin if none exist
        if ($postableType === User::class) {
            $postable = User::factory()->create();
        } else {
            $postable = Admin::factory()->create();
        }

        return [
            'title' => $this->faker->sentence(6),
            'content' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(['draft', 'published']),
            'postable_type' => $postableType,
            'postable_id' => $postable->id,
            'image' => null,
        ];
    }

    public function published()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'published',
            ];
        });
    }

    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
            ];
        });
    }
}