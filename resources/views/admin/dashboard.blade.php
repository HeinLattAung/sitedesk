<x-layouts.admin title="Dashboard">
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($stats as $label => $value)
            <div class="rounded-xl border border-stone-200 bg-white p-5">
                <p class="text-sm text-stone-500">{{ $label }}</p>
                <p class="mt-1 text-3xl font-bold text-stone-900">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('admin.posts.create') }}" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white hover:bg-teal-800">New post</a>
        <a href="{{ route('admin.services.create') }}" class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium hover:bg-stone-50">New service</a>
    </div>

    <section class="mt-8 rounded-xl border border-stone-200 bg-white">
        <div class="flex items-center justify-between border-b border-stone-200 px-5 py-3">
            <h2 class="font-semibold text-stone-900">Latest inquiries</h2>
            <a href="{{ route('admin.inquiries.index') }}" class="text-sm text-teal-700 hover:underline">View all</a>
        </div>
        <ul class="divide-y divide-stone-100">
            @forelse ($inquiries as $inquiry)
                <li>
                    <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="flex items-center justify-between gap-4 px-5 py-3 hover:bg-stone-50">
                        <span class="min-w-0">
                            <span class="{{ $inquiry->read_at ? '' : 'font-semibold' }}">{{ $inquiry->name }}</span>
                            <span class="block truncate text-sm text-stone-500">{{ $inquiry->message }}</span>
                        </span>
                        <span class="shrink-0 text-xs text-stone-400">{{ $inquiry->created_at->diffForHumans() }}</span>
                    </a>
                </li>
            @empty
                <li class="px-5 py-6 text-sm text-stone-500">No inquiries yet.</li>
            @endforelse
        </ul>
    </section>
</x-layouts.admin>
