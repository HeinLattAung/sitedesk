@props(['title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('sitedesk.company') }}</title>
    <meta name="description" content="{{ $description ?? config('sitedesk.tagline') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 text-stone-800 antialiased">
    <header class="sticky top-0 z-30 border-b border-stone-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold text-stone-900">
                <span class="grid size-8 place-items-center rounded-lg bg-teal-700 text-sm text-white">{{ mb_substr(config('sitedesk.company'), 0, 1) }}</span>
                {{ config('sitedesk.company') }}
            </a>

            <input id="nav-toggle" type="checkbox" class="peer hidden">
            <label for="nav-toggle" class="cursor-pointer rounded-md p-2 text-stone-600 md:hidden" aria-label="Menu">
                <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </label>

            <nav class="absolute inset-x-0 top-full hidden flex-col gap-1 border-b border-stone-200 bg-white px-4 pb-4 peer-checked:flex
                        md:static md:flex md:flex-row md:items-center md:gap-6 md:border-0 md:bg-transparent md:p-0">
                @foreach (['home' => 'Home', 'blog.index' => 'Blog', 'contact' => 'Contact'] as $route => $label)
                    <a href="{{ route($route) }}"
                       class="py-2 text-sm font-medium {{ request()->routeIs($route === 'blog.index' ? 'blog.*' : $route) ? 'text-teal-700' : 'text-stone-600 hover:text-stone-900' }}">
                        {{ $label }}
                    </a>
                @endforeach
                <a href="{{ route('contact') }}" class="mt-2 rounded-lg bg-teal-700 px-4 py-2 text-center text-sm font-medium text-white hover:bg-teal-800 md:mt-0">
                    Get a quote
                </a>
            </nav>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="mt-20 border-t border-stone-200 bg-white">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 text-sm text-stone-600 md:grid-cols-3">
            <div>
                <p class="font-semibold text-stone-900">{{ config('sitedesk.company') }}</p>
                <p class="mt-2">{{ config('sitedesk.tagline') }}</p>
            </div>
            <div>
                <p class="font-semibold text-stone-900">Visit us</p>
                <p class="mt-2">{{ config('sitedesk.address') }}</p>
                <p>{{ config('sitedesk.phone') }}</p>
            </div>
            <div class="md:text-right">
                <p>&copy; {{ date('Y') }} {{ config('sitedesk.company') }}</p>
                <a href="{{ route('login') }}" class="mt-2 inline-block text-stone-400 hover:text-stone-700">Staff login</a>
            </div>
        </div>
    </footer>
</body>
</html>
