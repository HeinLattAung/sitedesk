<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Service;
use App\Notifications\NewInquiryReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return [
            'name' => 'Somchai',
            'email' => 'somchai@example.com',
            'phone' => '0812345678',
            'message' => 'We need a new website for our restaurant.',
            ...$overrides,
        ];
    }

    public function test_contact_form_fields_start_empty(): void
    {
        // Regression: a $value variable from the page leaked into the form inputs.
        $html = $this->get('/contact')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<input id="name" name="name" type="text" value=""/', $html);
        $this->assertMatchesRegularExpression('/<textarea id="message"[^>]*>\s*<\/textarea>/', $html);
    }

    public function test_visitor_can_send_an_inquiry_and_owner_is_notified(): void
    {
        Notification::fake();
        config(['sitedesk.notify_email' => 'owner@example.com']);
        $service = Service::factory()->create();

        $this->post('/contact', $this->validData(['service_id' => $service->id]))
            ->assertRedirect('/contact')
            ->assertSessionHas('status');

        $inquiry = Inquiry::sole();
        $this->assertSame('Somchai', $inquiry->name);
        $this->assertSame($service->id, $inquiry->service_id);
        $this->assertNull($inquiry->read_at);

        Notification::assertSentTo(
            new AnonymousNotifiable,
            NewInquiryReceived::class,
            fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'owner@example.com',
        );
    }

    public function test_invalid_input_is_rejected(): void
    {
        $this->from('/contact')
            ->post('/contact', $this->validData(['email' => 'not-an-email', 'message' => 'short']))
            ->assertRedirect('/contact')
            ->assertSessionHasErrors(['email', 'message']);

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_hidden_services_cannot_be_selected(): void
    {
        $hidden = Service::factory()->create(['is_active' => false]);

        $this->post('/contact', $this->validData(['service_id' => $hidden->id]))
            ->assertSessionHasErrors('service_id');
    }

    public function test_honeypot_blocks_bots(): void
    {
        $this->post('/contact', $this->validData(['website' => 'http://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_submissions_are_rate_limited(): void
    {
        Notification::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/contact', $this->validData())->assertRedirect();
        }

        $this->post('/contact', $this->validData())->assertStatus(429);
        $this->assertDatabaseCount('inquiries', 5);
    }
}
