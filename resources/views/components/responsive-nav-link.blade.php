@props(['active'])

@php $isActive = (bool)($active ?? false); @endphp

<a {{ $attributes->merge([
    'class' => $isActive
        ? 'block w-full px-4 py-2.5 rounded-lg text-start text-base font-semibold transition duration-150 ease-in-out'
        : 'block w-full px-4 py-2.5 rounded-lg text-start text-base font-medium text-emerald-100 hover:text-white hover:bg-white/10 focus:outline-none focus:text-white focus:bg-white/10 transition duration-150 ease-in-out'
    ]) }}
    @if($isActive) style="background-color:#facc15;color:#1f2937;" @endif>
    <span class="flex items-center justify-between w-full">
        <span>{{ $slot }}</span>
        @if($isActive)
            <span class="ml-3 inline-flex items-center justify-center w-5 h-5 rounded-full bg-yellow-500/20 border border-yellow-600/60">
                <svg class="w-3 h-3 text-yellow-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </span>
        @endif
    </span>
</a>
        
