{{-- Custom pagination: 1,2,3,4,5,...,last --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-end">
        <ul class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md border border-gray-200 overflow-hidden">
            {{-- Previous Page Link --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-white border-r border-gray-200 cursor-not-allowed rounded-l-md" aria-disabled="true" aria-label="Previous">&lt;</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-700 bg-white border-r border-gray-200 rounded-l-md hover:bg-gray-100" aria-label="Previous">&lt;</a>
                @endif
            </li>

            {{-- Page Links --}}
            @php
                $total = $paginator->lastPage();
                $current = $paginator->currentPage();
                $window = 5;
            @endphp
            @for ($i = 1; $i <= $window && $i <= $total; $i++)
                <li>
                    @if ($i == $current)
                        <span aria-current="page" class="inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-emerald-600 border-r border-gray-200">{{ $i }}</span>
                    @else
                        <a href="{{ $paginator->url($i) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border-r border-gray-200 hover:bg-gray-100">{{ $i }}</a>
                    @endif
                </li>
            @endfor
            @if ($total > $window + 1)
                <li><span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border-r border-gray-200">...</span></li>
                <li>
                    @if ($total == $current)
                        <span aria-current="page" class="inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-emerald-600 border-r border-gray-200">{{ $total }}</span>
                    @else
                        <a href="{{ $paginator->url($total) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border-r border-gray-200 hover:bg-gray-100">{{ $total }}</a>
                    @endif
                </li>
            @endif

            {{-- Next Page Link --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-700 bg-white rounded-r-md hover:bg-gray-100" aria-label="Next">&gt;</a>
                @else
                    <span class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-white rounded-r-md cursor-not-allowed" aria-disabled="true" aria-label="Next">&gt;</span>
                @endif
            </li>
        </ul>
    </nav>
@endif
