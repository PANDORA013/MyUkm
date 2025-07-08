@props(['type' => 'info'])

@php
$colors = [
    'success' => 'bg-green-100 border-green-200 text-green-700',
    'error' => 'bg-red-100 border-red-200 text-red-700',
    'info' => 'bg-blue-100 border-blue-200 text-blue-700',
    'warning' => 'bg-yellow-100 border-yellow-200 text-yellow-700'
];

$icon = [
    'success' => 'check-circle',
    'error' => 'exclamation-circle',
    'info' => 'information-circle',
    'warning' => 'exclamation'
];

$colorClasses = $colors[$type] ?? $colors['info'];
$iconName = $icon[$type] ?? 'information-circle';
@endphp

<div {{ $attributes->merge(['class' => "px-4 py-3 rounded-lg border $colorClasses flex items-start"]) }}>
    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <div>
        {{ $slot }}
    </div>
</div>
