<x-layouts.admin :title="'Message from '.$inquiry->name" :back="route('admin.inquiries.index')"
                 :subtitle="'Received '.$inquiry->created_at->format('j F Y \a\t H:i').' · '.$inquiry->created_at->diffForHumans()">
    <x-slot:actions>
        <a href="mailto:{{ $inquiry->email }}?subject={{ rawurlencode('Re: your inquiry to '.config('sitedesk.company')) }}" class="btn btn-primary">
            <x-icon name="reply" class="size-4" /> Reply by email
        </a>
    </x-slot:actions>

    <div class="grid gap-6 lg:grid-cols-3">
        <article class="card p-6 sm:p-8 lg:col-span-2">
            <div class="flex items-center gap-4">
                <span class="grid size-12 place-items-center rounded-full bg-brand-600 text-lg font-semibold text-white">{{ mb_strtoupper(mb_substr($inquiry->name, 0, 1)) }}</span>
                <div>
                    <p class="font-semibold text-slate-900">{{ $inquiry->name }}</p>
                    <p class="text-sm text-slate-500">{{ $inquiry->email }}</p>
                </div>
            </div>
            <div class="mt-6 whitespace-pre-line rounded-xl bg-slate-50 p-5 leading-relaxed text-slate-800 ring-1 ring-slate-100">{{ $inquiry->message }}</div>
        </article>

        <aside class="space-y-6">
            <div class="card p-6">
                <h2 class="font-semibold text-slate-900">Details</h2>
                <dl class="mt-4 space-y-4 text-sm">
                    @foreach ([
                        ['mail', 'Email', $inquiry->email],
                        ['phone', 'Phone', $inquiry->phone ?? '—'],
                        ['briefcase', 'Service', $inquiry->service?->title ?? 'Not specified'],
                        ['calendar', 'Received', $inquiry->created_at->format('j M Y, H:i')],
                        ['desktop', 'IP address', $inquiry->ip_address ?? '—'],
                    ] as [$icon, $label, $value])
                        <div class="flex gap-3">
                            <x-icon :name="$icon" class="mt-0.5 size-4 text-slate-400" />
                            <div class="min-w-0">
                                <dt class="text-xs text-slate-500">{{ $label }}</dt>
                                <dd class="break-words font-medium text-slate-800">{{ $value }}</dd>
                            </div>
                        </div>
                    @endforeach
                </dl>
            </div>
            <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}"
                  data-confirm="The message from {{ $inquiry->name }} will be permanently deleted." data-confirm-title="Delete this inquiry?" data-confirm-button="Delete inquiry">
                @csrf @method('DELETE')
                <button class="btn btn-danger-ghost w-full ring-1 ring-red-200"><x-icon name="trash" class="size-4" /> Delete inquiry</button>
            </form>
        </aside>
    </div>
</x-layouts.admin>
