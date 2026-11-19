@props(['title' => 'Admin'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title }} · Admin · {{ config('sitedesk.company') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-100 text-stone-800 antialiased">
    @php
        $nav = [
            'admin.dashboard' => 'Dashboard',
            'admin.posts.index' => 'Posts',
            'admin.services.index' => 'Services',
            'admin.inquiries.index' => 'Inquiries',
        ];
        $unread = \App\Models\Inquiry::unread()->count();
    @endphp

    <div class="md:flex">
        <aside class="bg-stone-900 text-stone-300 md:min-h-screen md:w-60 md:shrink-0">
            <div class="flex items-center justify-between px-5 py-4 md:block">
                <a href="{{ route('admin.dashboard') }}" class="font-semibold text-white">{{ config('sitedesk.company') }}</a>
                <a href="{{ route('home') }}" class="text-xs text-stone-400 hover:text-white md:mt-1 md:block">View site &rarr;</a>
            </div>
            <nav class="flex gap-1 overflow-x-auto px-3 pb-3 md:flex-col md:pb-0">
                @foreach ($nav as $route => $label)
                    @php $active = request()->routeIs(str_replace('.index', '.*', $route)); @endphp
                    <a href="{{ route($route) }}"
                       class="flex items-center justify-between whitespace-nowrap rounded-lg px-3 py-2 text-sm {{ $active ? 'bg-stone-800 text-white' : 'hover:bg-stone-800 hover:text-white' }}">
                        {{ $label }}
                        @if ($route === 'admin.inquiries.index' && $unread)
                            <span class="ml-2 rounded-full bg-teal-500 px-2 text-xs font-semibold text-white">{{ $unread }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="flex items-center justify-between border-b border-stone-200 bg-white px-4 py-3 md:px-8">
                <h1 class="text-lg font-semibold text-stone-900">{{ $title }}</h1>
                <form method="POST" action="{{ route('logout') }}" class="flex items-center gap-3 text-sm">
                    @csrf
                    <span class="hidden text-stone-500 sm:inline">{{ auth()->user()->name }}</span>
                    <button class="rounded-md border border-stone-300 px-3 py-1 hover:bg-stone-50">Log out</button>
                </form>
            </header>

            <main class="p-4 md:p-8">
                @if (session('status'))
                    <div class="mb-6 rounded-lg border border-teal-200 bg-teal-50 p-3 text-sm text-teal-800" role="status">{{ session('status') }}</div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
