<x-layouts.admin title="Inquiry">
    <a href="{{ route('admin.inquiries.index') }}" class="text-sm text-teal-700 hover:underline">&larr; All inquiries</a>

    <article class="mt-4 max-w-3xl rounded-xl border border-stone-200 bg-white p-6">
        <dl class="grid gap-4 text-sm sm:grid-cols-2">
            <div><dt class="text-stone-500">Name</dt><dd class="font-medium text-stone-900">{{ $inquiry->name }}</dd></div>
            <div><dt class="text-stone-500">Email</dt><dd><a href="mailto:{{ $inquiry->email }}" class="text-teal-700 hover:underline">{{ $inquiry->email }}</a></dd></div>
            <div><dt class="text-stone-500">Phone</dt><dd>{{ $inquiry->phone ?? '—' }}</dd></div>
            <div><dt class="text-stone-500">Service</dt><dd>{{ $inquiry->service?->title ?? 'Not specified' }}</dd></div>
            <div><dt class="text-stone-500">Received</dt><dd>{{ $inquiry->created_at->format('j M Y, H:i') }}</dd></div>
            <div><dt class="text-stone-500">IP address</dt><dd>{{ $inquiry->ip_address ?? '—' }}</dd></div>
        </dl>
        <div class="mt-6 whitespace-pre-line border-t border-stone-100 pt-6 text-stone-800">{{ $inquiry->message }}</div>
    </article>

    <div class="mt-6 flex items-center gap-4">
        <a href="mailto:{{ $inquiry->email }}?subject={{ rawurlencode('Re: your inquiry') }}" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white hover:bg-teal-800">Reply by email</a>
        <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}" onsubmit="return confirm('Delete this inquiry?')">
            @csrf @method('DELETE')
            <button class="text-sm text-red-600 hover:underline">Delete</button>
        </form>
    </div>
</x-layouts.admin>
