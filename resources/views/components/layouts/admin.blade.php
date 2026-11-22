@props(['title' => 'Admin', 'subtitle' => null, 'back' => null])
<!DOCTYPE html>
<html lang="en" class="bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title }} · Admin · {{ config('sitedesk.company') }}</title>
    @include('partials.head-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-700 antialiased">
    @php
        $nav = [
            ['admin.dashboard', 'admin.dashboard', 'Dashboard', 'squares'],
            ['admin.posts.index', 'admin.posts.*', 'Posts', 'document'],
            ['admin.services.index', 'admin.services.*', 'Services', 'briefcase'],
            ['admin.inquiries.index', 'admin.inquiries.*', 'Inquiries', 'inbox'],
        ];
        $unread = \App\Models\Inquiry::unread()->count();
        $user = auth()->user();
        $initials = Str::of($user->name)->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
    @endphp

    {{-- Sidebar (desktop) --}}
    <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col bg-slate-950 lg:flex">
        <div class="flex h-16 items-center px-5">
            <a href="{{ route('admin.dashboard') }}"><x-logo dark /></a>
        </div>
        @include('admin._nav', ['nav' => $nav, 'unread' => $unread])
        <div class="mt-auto border-t border-white/10 p-3">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-slate-400 transition hover:bg-white/5 hover:text-white">
                <x-icon name="external" class="size-5" /> View website
            </a>
            <div class="mt-2 flex items-center gap-3 rounded-xl bg-white/5 p-3">
                <span class="grid size-9 place-items-center rounded-full bg-brand-600 text-sm font-semibold text-white">{{ $initials }}</span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-white">{{ $user->name }}</p>
                    <p class="truncate text-xs text-slate-400">{{ $user->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-lg p-1.5 text-slate-400 transition hover:bg-white/10 hover:text-white" title="Log out" aria-label="Log out">
                        <x-icon name="logout" class="size-5" />
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Top bar (mobile) --}}
    <div class="sticky top-0 z-30 bg-slate-950 lg:hidden">
        <div class="flex h-14 items-center justify-between px-4">
            <a href="{{ route('admin.dashboard') }}"><x-logo dark /></a>
            <button type="button" data-menu-toggle aria-controls="admin-menu" aria-expanded="false" aria-label="Open menu"
                    class="group rounded-lg p-2 text-slate-300 hover:bg-white/10">
                <x-icon name="menu" class="size-6 group-aria-expanded:hidden" />
                <x-icon name="x" class="hidden size-6 group-aria-expanded:block" />
            </button>
        </div>
        <div id="admin-menu" class="mobile-menu">
            <div>
                @include('admin._nav', ['nav' => $nav, 'unread' => $unread])
                <div class="flex items-center justify-between border-t border-white/10 px-4 py-3">
                    <a href="{{ route('home') }}" class="text-sm text-slate-400">View website</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-sm text-slate-400">Log out</button></form>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:pl-64">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                <div class="min-w-0">
                    @if ($back)
                        <a href="{{ $back }}" class="mb-1 inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-brand-700">
                            <x-icon name="arrow-left" class="size-4" /> Back
                        </a>
                    @endif
                    <h1 class="truncate text-2xl font-bold tracking-tight text-slate-900">{{ $title }}</h1>
                    @if ($subtitle) <p class="mt-0.5 text-sm text-slate-500">{{ $subtitle }}</p> @endif
                </div>
                @isset($actions)
                    <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
                @endisset
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-8">
            {{ $slot }}
        </main>
    </div>

    <x-toasts />
    <x-confirm-modal />
</body>
</html>
