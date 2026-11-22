<?php

namespace Database\Seeders;

use App\Models\Inquiry;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the admin account plus demo content.
     *
     * Admin login comes from ADMIN_EMAIL / ADMIN_PASSWORD in .env.
     * In production a password must be set — the seeder refuses to fall back to a default.
     */
    public function run(): void
    {
        $password = config('sitedesk.admin.password');

        if (blank($password)) {
            if (app()->isProduction()) {
                throw new RuntimeException('Set ADMIN_PASSWORD in .env before seeding production.');
            }
            $password = 'password'; // local development only
        }

        $admin = User::updateOrCreate(
            ['email' => config('sitedesk.admin.email')],
            ['name' => config('sitedesk.admin.name'), 'password' => $password],
        );

        $services = collect([
            ['Business websites', 'Responsive company websites with a simple admin panel, so your team can update content without calling a developer.', '25,000 THB'],
            ['E-commerce stores', 'Online shops with product management, cart, checkout and order tracking, built to load fast on mobile.', '60,000 THB'],
            ['Hosting & maintenance', 'We host your site on a managed Linux VPS: SSL, daily backups, security updates and uptime monitoring included.', '1,500 THB / month'],
            ['Website rescue', 'Slow, hacked or broken site? We find the problem, fix it, and harden the server so it does not happen again.', '8,000 THB'],
        ])->map(fn ($s, $i) => Service::updateOrCreate(
            ['title' => $s[0]],
            ['description' => $s[1], 'price_from' => $s[2], 'sort_order' => $i + 1, 'is_active' => true],
        ));

        foreach ($this->posts() as $i => [$title, $excerpt, $body]) {
            $admin->posts()->updateOrCreate(
                ['title' => $title],
                ['excerpt' => $excerpt, 'body' => $body, 'published_at' => now()->subDays(($i + 1) * 9)],
            );
        }

        // Example inquiries for local development and demos (SITEDESK_DEMO=true on a demo server).
        if ((! app()->isProduction() || config('sitedesk.demo')) && Inquiry::count() === 0) {
            $this->demoInquiries($services);
        }
    }

    private function demoInquiries($services): void
    {
        $examples = [
            ['Nattaya Srisuk', 'nattaya@example.com', '081 234 5678', 0, 'Hi, we run a small dental clinic in Silom and need a new bilingual (Thai/English) website with online appointment requests. Could you send a quote and timeline?', 2, false],
            ['Daniel Chen', 'daniel.chen@example.com', null, 1, 'We sell handmade ceramics and want to move from Instagram to our own online store with card and PromptPay payments. Around 80 products to start.', 6, false],
            ['Somchai Rattanakul', 'somchai@example.com', '089 765 4321', 3, 'Our WordPress site has become very slow and Google shows a security warning. Can you take a look this week?', 26, true],
            ['Emily Parker', 'emily@example.com', null, 2, 'Our company website is hosted overseas and keeps going down. Do you offer hosting with backups and SSL included?', 75, true],
        ];

        foreach ($examples as [$name, $email, $phone, $serviceIndex, $message, $hoursAgo, $read]) {
            $inquiry = Inquiry::create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'service_id' => $services[$serviceIndex]->id,
                'message' => $message,
                'ip_address' => '203.0.113.'.rand(2, 250), // documentation IP range
            ]);
            $inquiry->forceFill([
                'created_at' => now()->subHours($hoursAgo),
                'updated_at' => now()->subHours($hoursAgo),
                'read_at' => $read ? now()->subHours($hoursAgo - 1) : null,
            ])->save();
        }
    }

    private function posts(): array
    {
        return [
            [
                'How we deploy Laravel to a VPS without downtime',
                'Our deploy script builds each release in its own folder, then switches one symlink. If anything fails, the live site never notices.',
                "Every deploy creates a new folder under releases/, installs Composer dependencies, builds the frontend assets and runs migrations. Shared things like the .env file and uploaded images live in a shared/ folder and are linked into each release.\n\nOnly when every step has succeeded do we point the current symlink at the new release and reload PHP-FPM. Visitors are served by the old release until that instant, so there is no broken half-deployed state.\n\nIf a release turns out to be bad, rolling back is just pointing the symlink at the previous folder. We keep the last five releases on the server for exactly that reason.",
            ],
            [
                'Five server settings every small-business site needs',
                'A firewall, automatic security updates, SSH keys only, HTTPS everywhere and off-server backups. None of them are optional.',
                "Close every port except SSH, HTTP and HTTPS with UFW. Most attacks on small sites are automated scans; a closed port gives them nothing to talk to.\n\nTurn on unattended-upgrades so security patches install themselves overnight. Disable password logins for SSH and use keys only, then add fail2ban to block repeated attempts.\n\nServe everything over HTTPS with a free Let's Encrypt certificate that renews automatically. Finally, back up the database every night and copy the backup off the server: a backup on the same disk is not a backup.",
            ],
            [
                'Why your website is slow (and how to fix it)',
                'Most slow sites we rescue have the same three problems: huge images, no caching and too many database queries.',
                "Start with images. A 4 MB photo from a phone camera can usually become a 150 KB WebP file that looks the same on screen. Lazy-load anything below the fold.\n\nNext, cache what rarely changes. Laravel can cache config, routes and views at deploy time, and query results with a single Cache::remember call.\n\nFinally, look for N+1 queries: a page that loads 20 posts and then runs one extra query per post for its author. Eager loading with with('author') turns 21 queries into 2.",
            ],
            [
                'Keeping a contact form free of spam',
                'Rate limiting, a hidden honeypot field and server-side validation stop almost all bot submissions without annoying real visitors.',
                "CAPTCHAs work, but they cost you real inquiries from people who give up. We start with cheaper defences that visitors never see.\n\nA honeypot is a form field hidden with CSS. People never fill it in; simple bots fill in everything. Any submission with that field filled is rejected.\n\nRate limiting caps how many messages one IP address can send per minute, and strict server-side validation rejects anything malformed. Together these block the vast majority of spam on our clients' sites.",
            ],
        ];
    }
}
