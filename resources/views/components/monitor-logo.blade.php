@props(['size' => 'md'])

@php
    $sizeClasses = [
        'sm' => 'w-20 h-20',
        'md' => 'w-28 h-28',
        'lg' => 'w-40 h-40',
        'xl' => 'w-56 h-56',
    ];

    $textSize = [
        'sm' => 'text-base',
        'md' => 'text-2xl',
        'lg' => 'text-3xl',
        'xl' => 'text-4xl',
    ];

    $subtextSize = [
        'sm' => 'text-xs',
        'md' => 'text-sm',
        'lg' => 'text-base',
        'xl' => 'text-lg',
    ];

    $logoSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $textSizeClass = $textSize[$size] ?? $textSize['md'];
    $subtextSizeClass = $subtextSize[$size] ?? $subtextSize['md'];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center']) }}>
    <!-- Logo Image -->
    <div class="{{ $logoSize }} flex-shrink-0">
        <img src="{{ asset('images/unnamed.png') }}"
             alt="MonitorMesin Logo"
             class="w-full h-full object-contain">
    </div>
</div>
