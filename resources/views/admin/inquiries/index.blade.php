<x-layouts.admin title="Inquiries">
    <div class="flex gap-2 text-sm">
        <a href="{{ route('admin.inquiries.index') }}" class="rounded-full px-3 py-1 {{ $filter !== 'unread' ? 'bg-stone-900 text-white' : 'bg-white text-stone-600 hover:bg-stone-200' }}">All</a>
        <a href="{{ route('admin.inquiries.index', ['filter' => 'unread']) }}" class="rounded-full px-3 py-1 {{ $filter === 'unread' ? 'bg-stone-900 text-white' : 'bg-white text-stone-600 hover:bg-stone-200' }}">Unread</a>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-stone-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                <tr><th class="px-4 py-3">From</th><th class="px-4 py-3">Service</th><th class="px-4 py-3">Message</th><th class="px-4 py-3">Received</th></tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($inquiries as $inquiry)
                    <tr class="{{ $inquiry->read_at ? '' : 'bg-teal-50/50' }}">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="text-teal-700 hover:underline {{ $inquiry->read_at ? '' : 'font-semibold' }}">{{ $inquiry->name }}</a>
                            <span class="block text-xs text-stone-500">{{ $inquiry->email }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $inquiry->service?->title ?? '—' }}</td>
                        <td class="max-w-xs truncate px-4 py-3 text-stone-600">{{ $inquiry->message }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-stone-500">{{ $inquiry->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-stone-500">No inquiries.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $inquiries->links() }}</div>
</x-layouts.admin>
