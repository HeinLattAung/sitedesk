<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('code') · @yield('title') · {{ config('sitedesk.company') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-white font-sans text-slate-700 antialiased">
    <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-6 py-16 text-center">
        <div class="bg-grid absolute inset-0 [mask-image:radial-gradient(ellipse,black_20%,transparent_70%)]" aria-hidden="true"></div>
        <a href="{{ url('/') }}" class="relative mb-12"><x-logo /></a>
        <p class="relative bg-gradient-to-br from-brand-500 to-brand-800 bg-clip-text text-8xl font-bold tracking-tight text-transparent sm:text-9xl">@yield('code')</p>
        <h1 class="relative mt-4 text-2xl font-bold text-slate-900 sm:text-3xl">@yield('title')</h1>
        <p class="relative mx-auto mt-3 max-w-md text-slate-600">@yield('message')</p>
        <div class="relative mt-8 flex flex-col gap-3 sm:flex-row">
            @yield('actions')
            <a href="{{ url('/') }}" class="btn btn-primary">Go to home page</a>
        </div>
    </div>
</body>
</html>
