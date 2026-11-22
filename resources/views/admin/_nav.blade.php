<nav class="space-y-1 px-3 py-3" aria-label="Admin">
    <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Manage</p>
    @foreach ($nav as [$route, $pattern, $label, $icon])
        @php $active = request()->routeIs($pattern); @endphp
        <a href="{{ route($route) }}" @if ($active) aria-current="page" @endif
           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                  {{ $active ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
            <x-icon :name="$icon" class="size-5 {{ $active ? 'text-brand-400' : 'text-slate-500 group-hover:text-slate-300' }}" />
            <span class="flex-1">{{ $label }}</span>
            @if ($route === 'admin.inquiries.index' && $unread)
                <span class="rounded-full bg-brand-500 px-2 py-0.5 text-xs font-semibold text-white">{{ $unread }}</span>
            @endif
        </a>
    @endforeach
</nav>
