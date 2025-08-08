<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $admin;
    protected $userPost;
    protected $adminPost;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user and admin
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
        ]);

        $this->admin = Admin::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
        ]);

        // Create test posts
        $this->userPost = Post::factory()->create([
            'postable_type' => User::class,
            'postable_id' => $this->user->id,
            'title' => 'User Test Post',
            'content' => 'This is a user post',
            'status' => 'published'
        ]);

        $this->adminPost = Post::factory()->create([
            'postable_type' => Admin::class,
            'postable_id' => $this->admin->id,
            'title' => 'Admin Test Post',
            'content' => 'This is an admin post',
            'status' => 'published'
        ]);
    }

    // ========== AUTHENTICATION TESTS ==========

    /** @test */
    public function unauthenticated_user_cannot_access_posts()
    {
        $response = $this->getJson('/api/posts');
        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_access_posts()
    {
        Sanctum::actingAs($this->user);
        
        $response = $this->getJson('/api/posts');
        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_admin_can_access_posts()
    {
        Sanctum::actingAs($this->admin);
        
        $response = $this->getJson('/api/posts');
        $response->assertStatus(200);
    }

    // ========== USER PERMISSION TESTS ==========

    /** @test */
    public function user_can_only_see_their_own_posts()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200);
        $posts = $response->json('data');
        
        // User should only see their own post
        $this->assertCount(1, $posts);
        $this->assertEquals($this->userPost->id, $posts[0]['id']);
    }

    /** @test */
    public function user_can_create_post()
    {
        Sanctum::actingAs($this->user);

        $postData = [
            'title' => 'New User Post',
            'content' => 'This is a new post content',
            'status' => 'published'
        ];

        $response = $this->postJson('/api/posts', $postData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'title',
                        'content',
                        'status',
                        'author' => ['id', 'name', 'type'],
                        'created_at',
                        'updated_at'
                    ]
                ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'New User Post',
            'postable_type' => User::class,
            'postable_id' => $this->user->id
        ]);
    }

    /** @test */
    public function user_can_view_their_own_post()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/posts/{$this->userPost->id}");

        $response->assertStatus(200)
                ->assertJsonPath('data.id', $this->userPost->id)
                ->assertJsonPath('data.author.type', 'user');
    }

    /** @test */
    public function user_cannot_view_others_post()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/posts/{$this->adminPost->id}");

        $response->assertStatus(403)
                ->assertJson(['message' => 'You do not have permission to view this post.']);
    }

    /** @test */
    public function user_can_update_their_own_post()
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'title' => 'Updated User Post',
            'content' => 'Updated content',
            'status' => 'draft'
        ];

        $response = $this->putJson("/api/posts/{$this->userPost->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('posts', [
            'id' => $this->userPost->id,
            'title' => 'Updated User Post',
            'content' => 'Updated content',
            'status' => 'draft'
        ]);
    }

    /** @test */
    public function user_cannot_update_others_post()
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'title' => 'Trying to update admin post',
            'content' => 'This should fail',
            'status' => 'draft'
        ];

        $response = $this->putJson("/api/posts/{$this->adminPost->id}", $updateData);

        $response->assertStatus(403)
                ->assertJson(['message' => 'You do not have permission to update this post.']);
    }

    /** @test */
    public function user_can_delete_their_own_post()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson("/api/posts/{$this->userPost->id}");

        $response->assertStatus(200)
                ->assertJson(['message' => 'Post deleted successfully']);

        $this->assertDatabaseMissing('posts', ['id' => $this->userPost->id]);
    }

    /** @test */
    public function user_cannot_delete_others_post()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson("/api/posts/{$this->adminPost->id}");

        $response->assertStatus(403)
                ->assertJson(['message' => 'You do not have permission to delete this post.']);

        $this->assertDatabaseHas('posts', ['id' => $this->adminPost->id]);
    }

    // ========== ADMIN PERMISSION TESTS ==========

    /** @test */
    public function admin_can_see_all_posts()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200);
        $posts = $response->json('data');
        
        // Admin should see both posts
        $this->assertGreaterThanOrEqual(2, count($posts));
        
        $postIds = collect($posts)->pluck('id')->toArray();
        $this->assertContains($this->userPost->id, $postIds);
        $this->assertContains($this->adminPost->id, $postIds);
    }

    /** @test */
    public function admin_can_create_post()
    {
        Sanctum::actingAs($this->admin);

        $postData = [
            'title' => 'New Admin Post',
            'content' => 'This is an admin post',
            'status' => 'published'
        ];

        $response = $this->postJson('/api/posts', $postData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('posts', [
            'title' => 'New Admin Post',
            'postable_type' => Admin::class,
            'postable_id' => $this->admin->id
        ]);
    }


    /** @test */
    public function admin_can_view_any_post()
    {
        Sanctum::actingAs($this->admin);

        // View user post
        $response = $this->getJson("/api/posts/{$this->userPost->id}");
        $response->assertStatus(200)
                ->assertJsonPath('data.id', $this->userPost->id);

        // View admin post
        $response = $this->getJson("/api/posts/{$this->adminPost->id}");
        $response->assertStatus(200)
                ->assertJsonPath('data.id', $this->adminPost->id);
    }

    /** @test */
    public function admin_can_update_any_post()
    {
        Sanctum::actingAs($this->admin);

        $updateData = [
            'title' => 'Admin Updated User Post',
            'content' => 'Admin updated this user post',
            'status' => 'draft'
        ];

        $response = $this->putJson("/api/posts/{$this->userPost->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('posts', [
            'id' => $this->userPost->id,
            'title' => 'Admin Updated User Post',
            'content' => 'Admin updated this user post'
        ]);
    }

    /** @test */
    public function admin_can_delete_any_post()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/posts/{$this->userPost->id}");

        $response->assertStatus(200)
                ->assertJson(['message' => 'Post deleted successfully']);

        $this->assertDatabaseMissing('posts', ['id' => $this->userPost->id]);
    }

    // ========== SEARCH AND FILTER TESTS ==========

    /** @test */
    public function user_can_search_their_own_posts()
    {
        Sanctum::actingAs($this->user);

        // Create additional posts
        Post::factory()->create([
            'postable_type' => User::class,
            'postable_id' => $this->user->id,
            'title' => 'Laravel Tutorial',
            'content' => 'Learn Laravel'
        ]);

        $response = $this->getJson('/api/posts?search=Laravel');

        $response->assertStatus(200);
        $posts = $response->json('data');
        $this->assertTrue(collect($posts)->contains('title', 'Laravel Tutorial'));
    }

    /** @test */
    public function admin_can_search_all_posts()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/posts?search=Test');

        $response->assertStatus(200);
        $posts = $response->json('data');
        
        // Should find both test posts
        $titles = collect($posts)->pluck('title')->toArray();
        $this->assertContains('User Test Post', $titles);
        $this->assertContains('Admin Test Post', $titles);
    }



    // ========== VALIDATION TESTS ==========

    /** @test */
    public function post_creation_requires_title_and_content_and_status()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/posts', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'content', 'status']);
    }


}