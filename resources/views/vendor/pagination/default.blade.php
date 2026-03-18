@if ($paginator->hasPages())
<nav role="navigation" class="flex items-center justify-between gap-2 flex-wrap">
    <div class="flex items-center gap-2">
        @if ($paginator->onFirstPage())
        <span class="px-3 py-1.5 rounded-lg ultra-input cursor-not-allowed text-[#6b7a9f]">Précédent</span>
        @else
        <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg ultra-btn-primary text-white hover:opacity-90 transition-opacity"><span>Précédent</span></a>
        @endif
        @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg ultra-btn-primary text-white hover:opacity-90 transition-opacity"><span>Suivant</span></a>
        @else
        <span class="px-3 py-1.5 rounded-lg ultra-input cursor-not-allowed text-[#6b7a9f]">Suivant</span>
        @endif
    </div>
    <p class="text-sm text-[#b0bee3]">
        Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }}
    </p>
</nav>
@endif
