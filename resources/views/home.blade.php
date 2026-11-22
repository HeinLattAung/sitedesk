<x-layouts.site>
    @php $serviceIcons = ['desktop', 'shopping-bag', 'server', 'wrench', 'chart', 'sparkles']; @endphp

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="bg-grid absolute inset-0 [mask-image:radial-gradient(ellipse_at_top,black_30%,transparent_75%)]" aria-hidden="true"></div>
        <div class="absolute -top-40 left-1/2 h-[32rem] w-[60rem] -translate-x-1/2 rounded-full bg-gradient-to-br from-brand-200/60 via-brand-100/40 to-accent-400/20 blur-3xl" aria-hidden="true"></div>

        <div class="relative mx-auto grid max-w-6xl items-center gap-14 px-4 pb-20 pt-16 sm:px-6 md:pt-24 lg:grid-cols-2 lg:pb-28">
            <div class="animate-fade-up">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/80 px-3 py-1 text-xs font-medium text-brand-800 ring-1 ring-brand-200 backdrop-blur">
                    <span class="relative flex size-2"><span class="absolute inline-flex size-full animate-ping rounded-full bg-brand-400 opacity-75"></span><span class="relative inline-flex size-2 rounded-full bg-brand-500"></span></span>
                    Taking new projects for 2027
                </span>
                <h1 class="mt-6 text-4xl font-bold leading-[1.08] tracking-tight text-slate-900 text-balance sm:text-5xl lg:text-6xl">
                    Fast, secure websites for <span class="bg-gradient-to-r from-brand-600 to-brand-400 bg-clip-text text-transparent">Bangkok businesses</span>
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-slate-600">
                    We design, build and look after websites on our own Linux servers, so you get one team for the whole job,
                    from the first sketch to SSL renewals.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Start a project <x-icon name="arrow-right" class="size-5" /></a>
                    <a href="#services" class="btn btn-secondary btn-lg">Explore services</a>
                </div>
                <ul class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate-600">
                    @foreach (['Fixed-price quotes', 'Launch in 4–6 weeks', 'Thai & English support'] as $point)
                        <li class="flex items-center gap-1.5"><x-icon name="check-circle" class="size-5 text-brand-500" /> {{ $point }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- Product mock-up: a mini dashboard inside a browser window --}}
            <div class="relative mx-auto w-full max-w-lg animate-fade-up [animation-delay:150ms] lg:max-w-none" aria-hidden="true">
                <div class="rounded-2xl bg-white p-2 shadow-lift ring-1 ring-slate-200">
                    <div class="flex items-center gap-1.5 px-3 py-2">
                        <span class="size-2.5 rounded-full bg-red-300"></span><span class="size-2.5 rounded-full bg-amber-300"></span><span class="size-2.5 rounded-full bg-green-300"></span>
                        <span class="ml-3 flex-1 rounded-md bg-slate-100 px-3 py-1 text-[11px] text-slate-400">yourbusiness.co.th/admin</span>
                    </div>
                    <div class="grid grid-cols-[7rem_1fr] overflow-hidden rounded-xl bg-slate-50 ring-1 ring-slate-100">
                        <div class="space-y-2 bg-slate-900 p-3">
                            <div class="mb-4 h-4 w-14 rounded bg-brand-500"></div>
                            @foreach ([1, 0, 0, 0] as $on)
                                <div class="h-3 rounded {{ $on ? 'bg-white/40' : 'bg-white/10' }}"></div>
                            @endforeach
                        </div>
                        <div class="space-y-3 p-4">
                            <div class="grid grid-cols-3 gap-2">
                                @foreach ([['Visitors', '12.4k', 'text-brand-600'], ['Inquiries', '38', 'text-accent-500'], ['Uptime', '99.9%', 'text-slate-900']] as [$l, $v, $c])
                                    <div class="rounded-lg bg-white p-2.5 ring-1 ring-slate-100">
                                        <p class="text-[10px] text-slate-400">{{ $l }}</p>
                                        <p class="text-sm font-bold {{ $c }}">{{ $v }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="rounded-lg bg-white p-3 ring-1 ring-slate-100">
                                <div class="flex h-24 items-end gap-1.5">
                                    @foreach ([35, 50, 42, 65, 55, 78, 70, 88, 76, 95] as $h)
                                        <div class="flex-1 rounded-t bg-gradient-to-t from-brand-600 to-brand-300" style="height: {{ $h }}%"></div>
                                    @endforeach
                                </div>
                            </div>
                            @foreach ([1, 2] as $row)
                                <div class="flex items-center gap-2 rounded-lg bg-white p-2 ring-1 ring-slate-100">
                                    <span class="size-6 rounded-full bg-brand-100"></span>
                                    <span class="h-2 flex-1 rounded bg-slate-100"></span>
                                    <span class="h-2 w-10 rounded bg-brand-100"></span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="absolute -left-4 top-1/3 hidden animate-float items-center gap-2.5 rounded-xl bg-white px-3.5 py-2.5 shadow-lift ring-1 ring-slate-200 sm:flex">
                    <span class="grid size-8 place-items-center rounded-lg bg-brand-100 text-brand-700"><x-icon name="bolt" class="size-4" /></span>
                    <div><p class="text-[11px] text-slate-500">PageSpeed</p><p class="text-sm font-bold text-slate-900">98 / 100</p></div>
                </div>
                <div class="absolute -bottom-5 right-6 hidden animate-float items-center gap-2.5 rounded-xl bg-white px-3.5 py-2.5 shadow-lift ring-1 ring-slate-200 [animation-delay:1.5s] sm:flex">
                    <span class="grid size-8 place-items-center rounded-lg bg-accent-400/20 text-accent-500"><x-icon name="shield" class="size-4" /></span>
                    <div><p class="text-[11px] text-slate-500">SSL & backups</p><p class="text-sm font-bold text-slate-900">Automatic</p></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Services --}}
    <section id="services" class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
        <div class="max-w-2xl" data-reveal>
            <p class="eyebrow">What we do</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Everything your website needs, in one place</h2>
            <p class="mt-4 text-lg text-slate-600">Clear packages and honest prices. Every site comes with an easy admin panel, so your team can update it without calling us.</p>
        </div>
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($services as $i => $service)
                <article data-reveal data-reveal-delay="{{ $i * 80 }}"
                         class="group relative flex flex-col rounded-2xl bg-white p-6 ring-1 ring-slate-200 transition duration-300 hover:-translate-y-1 hover:shadow-lift hover:ring-brand-200">
                    <span class="grid size-12 place-items-center rounded-xl bg-brand-50 text-brand-600 ring-1 ring-brand-100 transition group-hover:bg-brand-600 group-hover:text-white">
                        <x-icon :name="$serviceIcons[$i % count($serviceIcons)]" class="size-6" />
                    </span>
                    <h3 class="mt-5 text-lg font-semibold text-slate-900">{{ $service->title }}</h3>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600">{{ $service->description }}</p>
                    @if ($service->price_from)
                        <p class="mt-5 border-t border-slate-100 pt-4 text-sm text-slate-500">
                            From <span class="font-semibold text-slate-900">{{ $service->price_from }}</span>
                        </p>
                    @endif
                </article>
            @empty
                <p class="text-slate-500">Services are coming soon.</p>
            @endforelse
        </div>
    </section>

    {{-- Process --}}
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <p class="eyebrow">How we work</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">From idea to launch in four steps</h2>
            </div>
            <ol class="mt-14 grid gap-8 md:grid-cols-4">
                @foreach ([
                    ['Discover', 'A free call to understand your business, goals and budget.', 'chat'],
                    ['Design', 'Wireframes and a visual design you approve before any code.', 'sparkles'],
                    ['Build', 'A fast, responsive site with an admin panel, tested on every device.', 'desktop'],
                    ['Launch & care', 'We deploy to our servers and handle SSL, backups and updates.', 'server'],
                ] as $i => [$step, $text, $icon])
                    <li class="relative" data-reveal data-reveal-delay="{{ $i * 100 }}">
                        @if (! $loop->last)
                            <span class="absolute left-14 right-0 top-6 hidden h-px bg-gradient-to-r from-brand-300 to-transparent md:block" aria-hidden="true"></span>
                        @endif
                        <span class="relative grid size-12 place-items-center rounded-2xl bg-white text-brand-600 shadow-soft ring-1 ring-slate-200">
                            <x-icon :name="$icon" class="size-6" />
                        </span>
                        <p class="mt-5 text-xs font-semibold text-brand-600">Step {{ $i + 1 }}</p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ $step }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Managed hosting --}}
    <section class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
        <div class="grid items-center gap-12 overflow-hidden rounded-3xl bg-slate-950 p-8 sm:p-12 lg:grid-cols-2" data-reveal>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-400">Managed hosting</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-white sm:text-4xl">Your site runs on servers we manage ourselves</h2>
                <p class="mt-4 text-slate-400">No shared hosting surprises. We run each site on a hardened Ubuntu server and watch it for you.</p>
                <a href="{{ route('contact') }}" class="btn btn-primary mt-8">Ask about hosting <x-icon name="arrow-right" class="size-4" /></a>
            </div>
            <ul class="grid gap-4 sm:grid-cols-2">
                @foreach ([
                    ['shield', 'Free SSL', 'HTTPS on every site, renewed automatically.'],
                    ['server', 'Nightly backups', 'Database and files saved every night.'],
                    ['bolt', 'Zero-downtime updates', 'New versions go live without errors.'],
                    ['lock', 'Hardened servers', 'Firewall, fail2ban and security updates.'],
                ] as [$icon, $title, $text])
                    <li class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 transition hover:bg-white/10">
                        <x-icon :name="$icon" class="size-6 text-brand-400" />
                        <p class="mt-3 font-semibold text-white">{{ $title }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ $text }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Latest posts --}}
    @if ($posts->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
            <div class="flex items-end justify-between gap-4" data-reveal>
                <div>
                    <p class="eyebrow">From the blog</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">Tips for running a better website</h2>
                </div>
                <a href="{{ route('blog.index') }}" class="hidden shrink-0 items-center gap-1 text-sm font-semibold text-brand-700 hover:gap-2 sm:inline-flex transition-all">
                    All posts <x-icon name="arrow-right" class="size-4" />
                </a>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach ($posts as $i => $post)
                    <div data-reveal data-reveal-delay="{{ $i * 80 }}">@include('blog._card', ['post' => $post])</div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Call to action --}}
    <section class="mx-auto max-w-6xl px-4 pt-16 sm:px-6">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 to-brand-800 px-8 py-14 text-center sm:px-16" data-reveal>
            <div class="bg-grid absolute inset-0 opacity-20 [mask-image:radial-gradient(ellipse,black,transparent_70%)]" aria-hidden="true"></div>
            <h2 class="relative text-3xl font-bold tracking-tight text-white text-balance sm:text-4xl">Ready for a website that works as hard as you do?</h2>
            <p class="relative mx-auto mt-4 max-w-xl text-brand-100">Tell us about your project. We reply within one business day with a clear, fixed-price quote.</p>
            <a href="{{ route('contact') }}" class="btn btn-lg relative mt-8 bg-white text-brand-800 hover:bg-brand-50">
                Get your free quote <x-icon name="arrow-right" class="size-5" />
            </a>
        </div>
    </section>
</x-layouts.site>
