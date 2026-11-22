@props(['dark' => false])
{{-- Brand mark: a stylised "K" in a rounded square, plus the company name. --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <span class="relative grid size-9 place-items-center overflow-hidden rounded-xl bg-gradient-to-br from-brand-500 to-brand-800 shadow-sm shadow-brand-900/30">
        <span class="absolute -right-2 -top-2 size-5 rounded-full bg-accent-400/90"></span>
        <span class="relative text-base font-bold text-white">{{ mb_substr(config('sitedesk.company'), 0, 1) }}</span>
    </span>
    <span class="text-[15px] font-semibold tracking-tight {{ $dark ? 'text-white' : 'text-slate-900' }}">{{ config('sitedesk.company') }}</span>
</span>
