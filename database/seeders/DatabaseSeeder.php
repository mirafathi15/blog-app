<?php
namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $admin = Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $admin->posts()->create([
                'title' => "Admin Post {$i}",
                'content' => "This is the content for admin post {$i}",
                'status' => $i % 2 === 0 ? 'published' : 'draft',
            ]);
        }

        for ($i = 1; $i <= 3; $i++) {
            $user->posts()->create([
                'title' => "User Post {$i}",
                'content' => "This is the content for user post {$i}",
                'status' => 'published',
            ]);
        }
    }
}