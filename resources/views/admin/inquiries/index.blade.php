<x-layouts.admin title="Inquiries" subtitle="Messages sent through the contact form on your website.">
    <nav class="inline-flex gap-1 rounded-xl bg-white p-1 ring-1 ring-slate-200" aria-label="Filter inquiries">
        @foreach ([null => 'All', 'unread' => 'Unread'] as $key => $label)
            @php $active = $filter === ($key ?: null); @endphp
            <a href="{{ route('admin.inquiries.index', array_filter(['filter' => $key])) }}"
               class="rounded-lg px-3 py-1.5 text-sm font-medium transition {{ $active ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ $label }}</a>
        @endforeach
    </nav>

    <div class="card mt-6 overflow-hidden">
        <ul class="divide-y divide-slate-100">
            @forelse ($inquiries as $inquiry)
                <li>
                    <a href="{{ route('admin.inquiries.show', $inquiry) }}"
                       class="flex items-start gap-4 px-5 py-4 transition hover:bg-slate-50 {{ $inquiry->read_at ? '' : 'bg-brand-50/40' }}">
                        <span class="relative grid size-10 shrink-0 place-items-center rounded-full {{ $inquiry->read_at ? 'bg-slate-100 text-slate-500' : 'bg-brand-600 text-white' }} text-sm font-semibold">
                            {{ mb_strtoupper(mb_substr($inquiry->name, 0, 1)) }}
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                <span class="text-sm {{ $inquiry->read_at ? 'text-slate-700' : 'font-semibold text-slate-900' }}">{{ $inquiry->name }}</span>
                                <span class="text-xs text-slate-400">{{ $inquiry->email }}</span>
                                @if ($inquiry->service)
                                    <span class="badge bg-slate-100 text-slate-600">{{ $inquiry->service->title }}</span>
                                @endif
                                @unless ($inquiry->read_at) <span class="badge bg-brand-100 text-brand-700">New</span> @endunless
                            </span>
                            <span class="mt-1 line-clamp-2 block text-sm text-slate-500">{{ $inquiry->message }}</span>
                        </span>
                        <span class="shrink-0 text-xs text-slate-400" title="{{ $inquiry->created_at->format('j M Y, H:i') }}">{{ $inquiry->created_at->diffForHumans() }}</span>
                    </a>
                </li>
            @empty
                <li class="px-6 py-16 text-center">
                    <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-slate-100 text-slate-400"><x-icon name="inbox" class="size-7" /></span>
                    <h2 class="mt-4 font-semibold text-slate-900">{{ $filter === 'unread' ? 'All caught up!' : 'No inquiries yet' }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $filter === 'unread' ? 'You have read every message.' : 'New messages from the contact form will appear here.' }}</p>
                </li>
            @endforelse
        </ul>
    </div>
    <div class="mt-6">{{ $inquiries->links() }}</div>
</x-layouts.admin>
