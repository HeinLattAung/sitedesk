<x-layouts.admin title="Services">
    <div class="flex justify-end">
        <a href="{{ route('admin.services.create') }}" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white hover:bg-teal-800">New service</a>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-stone-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                <tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Service</th><th class="px-4 py-3">Price from</th><th class="px-4 py-3">Inquiries</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($services as $service)
                    <tr>
                        <td class="px-4 py-3 text-stone-500">{{ $service->sort_order }}</td>
                        <td class="px-4 py-3 font-medium text-stone-900">{{ $service->title }}</td>
                        <td class="px-4 py-3">{{ $service->price_from ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $service->inquiries_count }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs {{ $service->is_active ? 'bg-teal-100 text-teal-800' : 'bg-stone-200 text-stone-700' }}">
                                {{ $service->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('admin.services.edit', $service) }}" class="text-teal-700 hover:underline">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-stone-500">No services yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
