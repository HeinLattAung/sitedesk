<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_active_services_and_published_posts(): void
    {
        Service::factory()->create(['title' => 'Visible service']);
        Service::factory()->create(['title' => 'Hidden service', 'is_active' => false]);
        Post::factory()->create(['title' => 'Live post']);
        Post::factory()->draft()->create(['title' => 'Secret draft']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Visible service')
            ->assertDontSee('Hidden service')
            ->assertSee('Live post')
            ->assertDontSee('Secret draft');

        // Second request is served from the services cache.
        $this->get('/')->assertOk()->assertSee('Visible service');
    }

    public function test_blog_lists_and_searches_published_posts(): void
    {
        Post::factory()->create(['title' => 'Deploying Laravel with Nginx']);
        Post::factory()->create(['title' => 'Choosing a colour palette']);

        $this->get('/blog')->assertOk()->assertSee('Deploying Laravel with Nginx')->assertSee('Choosing a colour palette');

        $this->get('/blog?q=nginx')->assertOk()->assertSee('Deploying Laravel with Nginx')->assertDontSee('Choosing a colour palette');
    }

    public function test_published_post_is_shown_by_slug(): void
    {
        $post = Post::factory()->create(['title' => 'Hello Bangkok']);

        $this->assertSame('hello-bangkok', $post->slug);
        $this->get('/blog/hello-bangkok')->assertOk()->assertSee('Hello Bangkok');
    }

    public function test_drafts_and_scheduled_posts_return_404(): void
    {
        $draft = Post::factory()->draft()->create();
        $scheduled = Post::factory()->create(['published_at' => now()->addWeek()]);

        $this->get(route('blog.show', $draft))->assertNotFound();
        $this->get(route('blog.show', $scheduled))->assertNotFound();
    }

    public function test_post_body_html_is_escaped(): void
    {
        $post = Post::factory()->create(['body' => '<script>alert(1)</script>']);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_duplicate_titles_get_unique_slugs(): void
    {
        $a = Post::factory()->create(['title' => 'Same title']);
        $b = Post::factory()->create(['title' => 'Same title']);

        $this->assertSame('same-title', $a->slug);
        $this->assertSame('same-title-2', $b->slug);
    }

    public function test_health_check_endpoint_responds(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_malformed_query_strings_do_not_crash(): void
    {
        // Regression (found in QA): ?q[]=x caused a 500 error.
        $this->get('/blog?q[]=x')->assertOk();
        $this->get('/blog?page=99')->assertOk()->assertSee('There are no articles on this page.');
    }
}
