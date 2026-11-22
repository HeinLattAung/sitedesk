@php
    $hour = (int) now()->timezone('Asia/Bangkok')->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $firstName = auth()->user()->name;
    $statMeta = [
        'Published posts' => ['document', 'bg-brand-50 text-brand-600', route('admin.posts.index', ['status' => 'published'])],
        'Drafts' => ['pencil', 'bg-amber-50 text-amber-600', route('admin.posts.index', ['status' => 'draft'])],
        'Active services' => ['briefcase', 'bg-sky-50 text-sky-600', route('admin.services.index')],
        'Unread inquiries' => ['inbox', 'bg-rose-50 text-rose-600', route('admin.inquiries.index', ['filter' => 'unread'])],
    ];
@endphp
<x-layouts.admin title="Dashboard" :subtitle="$greeting.', '.$firstName.'. Here is what is happening on your website.'">
    <x-slot:actions>
        <a href="{{ route('admin.services.create') }}" class="btn btn-secondary"><x-icon name="plus" class="size-4" /> Service</a>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary"><x-icon name="plus" class="size-4" /> New post</a>
    </x-slot:actions>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($stats as $label => $value)
            @php [$icon, $tone, $url] = $statMeta[$label]; @endphp
            <a href="{{ $url }}" class="card group p-5 transition hover:-translate-y-0.5 hover:shadow-lift">
                <div class="flex items-center justify-between">
                    <span class="grid size-10 place-items-center rounded-xl {{ $tone }}"><x-icon :name="$icon" /></span>
                    <x-icon name="arrow-right" class="size-4 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-slate-500" />
                </div>
                <p class="mt-4 text-3xl font-bold tracking-tight text-slate-900 tabular-nums">{{ $value }}</p>
                <p class="mt-0.5 text-sm text-slate-500">{{ $label }}</p>
            </a>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-5">
        <section class="card lg:col-span-3">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="font-semibold text-slate-900">Latest inquiries</h2>
                <a href="{{ route('admin.inquiries.index') }}" class="text-sm font-medium text-brand-700 hover:underline">View all</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($inquiries as $inquiry)
                    <li>
                        <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="flex items-center gap-4 px-5 py-4 transition hover:bg-slate-50">
                            <span class="relative grid size-10 shrink-0 place-items-center rounded-full bg-slate-100 text-sm font-semibold text-slate-600">
                                {{ mb_strtoupper(mb_substr($inquiry->name, 0, 1)) }}
                                @unless ($inquiry->read_at)
                                    <span class="absolute -right-0.5 -top-0.5 size-3 rounded-full bg-brand-500 ring-2 ring-white"></span>
                                @endunless
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="flex items-center gap-2">
                                    <span class="truncate text-sm {{ $inquiry->read_at ? 'text-slate-700' : 'font-semibold text-slate-900' }}">{{ $inquiry->name }}</span>
                                    @if ($inquiry->service)
                                        <span class="badge hidden bg-slate-100 text-slate-600 sm:inline-flex">{{ $inquiry->service->title }}</span>
                                    @endif
                                </span>
                                <span class="block truncate text-sm text-slate-500">{{ $inquiry->message }}</span>
                            </span>
                            <span class="shrink-0 text-xs text-slate-400">{{ $inquiry->created_at->diffForHumans(short: true) }}</span>
                        </a>
                    </li>
                @empty
                    <li class="px-5 py-12 text-center">
                        <x-icon name="inbox" class="mx-auto size-8 text-slate-300" />
                        <p class="mt-2 text-sm text-slate-500">No inquiries yet. They will appear here.</p>
                    </li>
                @endforelse
            </ul>
        </section>

        <section class="card lg:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="font-semibold text-slate-900">Recent posts</h2>
                <a href="{{ route('admin.posts.index') }}" class="text-sm font-medium text-brand-700 hover:underline">Manage</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($posts as $post)
                    <li>
                        <a href="{{ route('admin.posts.edit', $post) }}" class="flex items-center justify-between gap-3 px-5 py-3.5 transition hover:bg-slate-50">
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-medium text-slate-800">{{ $post->title }}</span>
                                <span class="text-xs text-slate-400">Updated {{ $post->updated_at->diffForHumans() }}</span>
                            </span>
                            @include('admin.posts._status', ['post' => $post])
                        </a>
                    </li>
                @empty
                    <li class="px-5 py-12 text-center text-sm text-slate-500">No posts yet.</li>
                @endforelse
            </ul>
        </section>
    </div>
</x-layouts.admin>
