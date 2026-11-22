@if ($post->isPublished())
    <span class="badge shrink-0 bg-brand-50 text-brand-700 ring-1 ring-brand-200 ring-inset"><span class="size-1.5 rounded-full bg-brand-500"></span>Published</span>
@elseif ($post->published_at)
    <span class="badge shrink-0 bg-amber-50 text-amber-700 ring-1 ring-amber-200 ring-inset" title="{{ $post->published_at->format('j M Y H:i') }}"><x-icon name="clock" class="size-3.5" />Scheduled</span>
@else
    <span class="badge shrink-0 bg-slate-100 text-slate-600 ring-1 ring-slate-200 ring-inset"><span class="size-1.5 rounded-full bg-slate-400"></span>Draft</span>
@endif
