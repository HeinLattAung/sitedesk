<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        foreach (['/admin', '/admin/posts', '/admin/services', '/admin/inquiries'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_admin_can_log_in_and_out(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass']);

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => $user->email, 'password' => 'secret-pass'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_dashboard_shows_counts(): void
    {
        Inquiry::factory(2)->create();

        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertOk()
            ->assertSee('Unread inquiries');
    }

    public function test_admin_can_create_a_post_with_cover_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->post('/admin/posts', [
            'title' => 'Our new office in Sathorn',
            'excerpt' => 'We moved.',
            'body' => "First paragraph.\n\nSecond paragraph.",
            'publish_now' => '1',
            'cover_image' => UploadedFile::fake()->image('cover.jpg', 1200, 675),
        ])->assertRedirect();

        $post = Post::sole();
        $this->assertSame('our-new-office-in-sathorn', $post->slug);
        $this->assertTrue($post->isPublished());
        $this->assertTrue($post->author->is($user));
        Storage::disk('public')->assertExists($post->cover_image);

        $this->get('/blog/our-new-office-in-sathorn')->assertOk()->assertSee('Second paragraph.');
    }

    public function test_admin_can_save_a_draft_and_update_it(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/admin/posts', ['title' => 'Draft idea', 'body' => 'Not ready.']);

        $post = Post::sole();
        $this->assertNull($post->published_at);

        $this->actingAs($user)->put("/admin/posts/{$post->id}", [
            'title' => 'Finished idea',
            'body' => 'Ready now.',
            'publish_now' => '1',
        ])->assertRedirect();

        $post->refresh();
        $this->assertSame('finished-idea', $post->slug);
        $this->assertTrue($post->isPublished());
    }

    public function test_replacing_and_deleting_cover_removes_old_files(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $post = Post::factory()->for($user, 'author')->create(['cover_image' => UploadedFile::fake()->image('a.jpg')->store('covers', 'public')]);
        $old = $post->cover_image;

        $this->actingAs($user)->put("/admin/posts/{$post->id}", [
            'title' => $post->title,
            'body' => $post->body,
            'cover_image' => UploadedFile::fake()->image('b.jpg'),
        ]);

        Storage::disk('public')->assertMissing($old);
        $new = $post->refresh()->cover_image;
        Storage::disk('public')->assertExists($new);

        $this->actingAs($user)->delete("/admin/posts/{$post->id}")->assertRedirect('/admin/posts');
        Storage::disk('public')->assertMissing($new);
        $this->assertModelMissing($post);
    }

    public function test_cover_upload_must_be_an_image(): void
    {
        $this->actingAs(User::factory()->create())->post('/admin/posts', [
            'title' => 'Bad upload',
            'body' => 'Body',
            'cover_image' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php'),
        ])->assertSessionHasErrors('cover_image');

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_admin_can_manage_services_and_home_page_updates(): void
    {
        $user = User::factory()->create();
        $this->get('/'); // warm the services cache

        $this->actingAs($user)->post('/admin/services', [
            'title' => 'SEO audits',
            'description' => 'We check your site.',
            'sort_order' => 1,
            'is_active' => '1',
        ])->assertRedirect('/admin/services');

        $this->get('/')->assertSee('SEO audits');

        $service = Service::sole();
        $this->actingAs($user)->put("/admin/services/{$service->id}", [
            'title' => 'SEO audits',
            'description' => 'We check your site.',
            'sort_order' => 1,
        ]);

        $this->assertFalse($service->refresh()->is_active);
        $this->get('/')->assertDontSee('SEO audits');
    }

    public function test_opening_an_inquiry_marks_it_read(): void
    {
        $inquiry = Inquiry::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get("/admin/inquiries/{$inquiry->id}")
            ->assertOk()
            ->assertSee($inquiry->email);

        $this->assertNotNull($inquiry->refresh()->read_at);
    }

    public function test_renaming_a_published_post_keeps_its_url(): void
    {
        // Found in QA: renaming changed the slug, so old links returned 404.
        $user = User::factory()->create();
        $post = Post::factory()->for($user, 'author')->create(['title' => 'Original title']);

        $this->actingAs($user)->put("/admin/posts/{$post->id}", ['title' => 'Better title', 'body' => $post->body, 'publish_now' => '1']);

        $this->assertSame('original-title', $post->refresh()->slug);
        $this->get('/blog/original-title')->assertOk()->assertSee('Better title');
    }

    public function test_switching_published_off_unpublishes_the_post(): void
    {
        // Found in QA: when the empty schedule field was not sent, the post stayed public.
        $user = User::factory()->create();

        foreach ([[], ['published_at' => '']] as $extra) {
            $post = Post::factory()->for($user, 'author')->create();
            $this->assertTrue($post->isPublished());

            $this->actingAs($user)->put("/admin/posts/{$post->id}", ['title' => $post->title, 'body' => $post->body, ...$extra]);

            $this->assertNull($post->refresh()->published_at);
            $this->get("/blog/{$post->slug}")->assertNotFound();
        }
    }

    public function test_admin_post_filters_ignore_malformed_input(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/posts?q[]=x&status[]=draft')
            ->assertOk();
    }

    public function test_admin_pages_escape_user_input(): void
    {
        $inquiry = Inquiry::factory()->create(['name' => '<script>alert(1)</script>', 'message' => '<img src=x onerror=alert(1)>']);

        foreach (['/admin', '/admin/inquiries', "/admin/inquiries/{$inquiry->id}"] as $url) {
            $this->actingAs(User::factory()->create())->get($url)
                ->assertOk()
                ->assertDontSee('<script>alert(1)</script>', false)
                ->assertDontSee('<img src=x onerror', false);
        }
    }
}
