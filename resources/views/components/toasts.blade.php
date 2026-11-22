{{-- Flash messages appear as toasts in the top-right corner and fade out by themselves. --}}
@if (session('status') || session('error'))
    <div class="pointer-events-none fixed inset-x-4 top-4 z-50 flex flex-col items-end gap-3 sm:left-auto sm:right-6 sm:top-6" aria-live="polite">
        @foreach (['status' => 'success', 'error' => 'error'] as $key => $type)
            @if (session($key))
                <div data-toast role="status"
                     class="toast pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-2xl bg-white p-4 shadow-lift ring-1 ring-slate-200">
                    <span class="grid size-8 shrink-0 place-items-center rounded-full {{ $type === 'success' ? 'bg-brand-100 text-brand-700' : 'bg-red-100 text-red-700' }}">
                        <x-icon :name="$type === 'success' ? 'check' : 'warning'" class="size-4" />
                    </span>
                    <p class="flex-1 pt-1 text-sm text-slate-700">{{ session($key) }}</p>
                    <button type="button" data-toast-close class="rounded-lg p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-600" aria-label="Dismiss">
                        <x-icon name="x" class="size-4" />
                    </button>
                </div>
            @endif
        @endforeach
    </div>
@endif
