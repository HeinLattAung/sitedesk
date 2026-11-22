<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Staff login · {{ config('sitedesk.company') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-sans text-slate-700 antialiased">
    <div class="grid min-h-screen lg:grid-cols-2">
        {{-- Brand panel --}}
        <div class="relative hidden overflow-hidden bg-gradient-to-br from-brand-700 via-brand-800 to-brand-950 p-12 lg:flex lg:flex-col">
            <div class="bg-grid absolute inset-0 opacity-20" aria-hidden="true"></div>
            <div class="absolute -bottom-32 -right-32 size-96 rounded-full bg-accent-400/20 blur-3xl" aria-hidden="true"></div>
            <a href="{{ route('home') }}" class="relative"><x-logo dark /></a>
            <div class="relative mt-auto">
                <h2 class="text-3xl font-bold leading-tight text-white">Manage your website<br>in one place.</h2>
                <ul class="mt-8 space-y-4 text-brand-100">
                    @foreach (['Publish and schedule blog posts', 'Update services and prices', 'Reply to customer inquiries'] as $point)
                        <li class="flex items-center gap-3"><span class="grid size-7 place-items-center rounded-full bg-white/10"><x-icon name="check" class="size-4 text-brand-300" /></span>{{ $point }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Form --}}
        <div class="flex flex-col justify-center px-6 py-12 sm:px-12">
            <div class="mx-auto w-full max-w-sm animate-fade-up">
                <a href="{{ route('home') }}" class="lg:hidden"><x-logo /></a>
                <h1 class="mt-10 text-2xl font-bold tracking-tight text-slate-900 lg:mt-0">Welcome back</h1>
                <p class="mt-2 text-sm text-slate-500">Sign in to the {{ config('sitedesk.company') }} admin panel.</p>

                <form method="POST" action="{{ route('login') }}" data-loading="Signing in…" class="mt-8 space-y-5">
                    @csrf
                    @include('partials.field', ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true, 'autocomplete' => 'username', 'placeholder' => 'you@company.com'])
                    @include('partials.field', ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'required' => true, 'autocomplete' => 'current-password', 'value' => ''])
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"> Keep me signed in
                    </label>
                    <button type="submit" class="btn btn-primary btn-lg w-full">Sign in <x-icon name="arrow-right" class="size-5" /></button>
                </form>

                <a href="{{ route('home') }}" class="mt-10 inline-flex items-center gap-1.5 text-sm text-slate-500 transition hover:text-slate-900">
                    <x-icon name="arrow-left" class="size-4" /> Back to website
                </a>
            </div>
        </div>
    </div>
</body>
</html>
