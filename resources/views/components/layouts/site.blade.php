@props(['title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="en" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('sitedesk.company') }}</title>
    <meta name="description" content="{{ $description ?? config('sitedesk.tagline') }}">
    <meta property="og:title" content="{{ $title ?? config('sitedesk.company') }}">
    <meta property="og:description" content="{{ $description ?? config('sitedesk.tagline') }}">
    @include('partials.head-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-sans text-slate-700 antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow-lift">Skip to content</a>

    @php
        $links = ['home' => 'Home', 'blog.index' => 'Blog', 'contact' => 'Contact'];
        $isActive = fn ($route) => request()->routeIs($route === 'blog.index' ? 'blog.*' : $route);
    @endphp

    <header data-sticky-header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/80 backdrop-blur-lg">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
            <a href="{{ route('home') }}" class="rounded-lg" aria-label="{{ config('sitedesk.company') }} home">
                <x-logo />
            </a>

            <nav class="hidden items-center gap-1 md:flex" aria-label="Main">
                @foreach ($links as $route => $label)
                    <a href="{{ route($route) }}" @if ($isActive($route)) aria-current="page" @endif
                       class="relative rounded-lg px-3 py-2 text-sm font-medium transition {{ $isActive($route) ? 'text-brand-700' : 'text-slate-600 hover:text-slate-900' }}">
                        {{ $label }}
                        @if ($isActive($route))
                            <span class="absolute inset-x-3 -bottom-[13px] h-0.5 rounded-full bg-brand-600"></span>
                        @endif
                    </a>
                @endforeach
                <a href="{{ route('contact') }}" class="btn btn-primary ml-3">
                    Get a free quote <x-icon name="arrow-right" class="size-4" />
                </a>
            </nav>

            <button type="button" data-menu-toggle aria-controls="mobile-menu" aria-expanded="false" aria-label="Open menu"
                    class="group rounded-lg p-2 text-slate-700 hover:bg-slate-100 md:hidden">
                <x-icon name="menu" class="size-6 group-aria-expanded:hidden" />
                <x-icon name="x" class="hidden size-6 group-aria-expanded:block" />
            </button>
        </div>

        <div id="mobile-menu" class="mobile-menu md:hidden">
            <div>
                <nav class="space-y-1 border-t border-slate-100 px-4 pb-5 pt-3" aria-label="Mobile">
                    @foreach ($links as $route => $label)
                        <a href="{{ route($route) }}"
                           class="flex items-center justify-between rounded-xl px-3 py-3 text-base font-medium {{ $isActive($route) ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}">
                            {{ $label }} <x-icon name="chevron-right" class="size-4 text-slate-400" />
                        </a>
                    @endforeach
                    <a href="{{ route('contact') }}" class="btn btn-primary btn-lg mt-3 w-full">Get a free quote</a>
                </nav>
            </div>
        </div>
    </header>

    <main id="main">
        {{ $slot }}
    </main>

    <footer class="mt-24 bg-slate-950 text-slate-400">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
            <div class="grid gap-10 md:grid-cols-4">
                <div class="md:col-span-2">
                    <x-logo dark />
                    <p class="mt-4 max-w-sm text-sm leading-relaxed">{{ config('sitedesk.tagline') }}. Design, development and managed hosting from one team in Bangkok.</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">Explore</p>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}#services" class="transition hover:text-white">Services</a></li>
                        <li><a href="{{ route('blog.index') }}" class="transition hover:text-white">Blog</a></li>
                        <li><a href="{{ route('contact') }}" class="transition hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">Get in touch</p>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li class="flex gap-2"><x-icon name="map-pin" class="size-4 mt-0.5 text-brand-400" /> {{ config('sitedesk.address') }}</li>
                        <li class="flex gap-2"><x-icon name="phone" class="size-4 mt-0.5 text-brand-400" /> {{ config('sitedesk.phone') }}</li>
                        <li class="flex gap-2"><x-icon name="clock" class="size-4 mt-0.5 text-brand-400" /> Mon–Fri, 9:00–18:00</li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 flex flex-col gap-3 border-t border-white/10 pt-6 text-xs sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ config('sitedesk.company') }}. All rights reserved.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 transition hover:text-white">
                    <x-icon name="lock" class="size-3.5" /> Staff login
                </a>
            </div>
        </div>
    </footer>
</body>
</html>
