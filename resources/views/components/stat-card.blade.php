@props([
    'title',
    'value',
    'icon',
    'description' => null,
    'color' => 'blue',
    'action' => null
])

@php
$colors = [
    'blue' => [
        'bg' => 'bg-blue-50',
        'text' => 'text-blue-500',
        'button' => 'bg-blue-600 hover:bg-blue-700'
    ],
    'green' => [
        'bg' => 'bg-green-50',
        'text' => 'text-green-500',
        'button' => 'bg-green-600 hover:bg-green-700'
    ],
    'yellow' => [
        'bg' => 'bg-yellow-50',
        'text' => 'text-yellow-500',
        'button' => 'bg-yellow-600 hover:bg-yellow-700'
    ],
    'red' => [
        'bg' => 'bg-red-50',
        'text' => 'text-red-500',
        'button' => 'bg-red-600 hover:bg-red-700'
    ],
];

$colorClasses = $colors[$color] ?? $colors['blue'];
@endphp

<div class="bg-white border border-gray-200 shadow rounded-lg p-4 hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $value }}</p>
            @if($description)
                <p class="text-xs text-gray-500 mt-1">{{ $description }}</p>
            @endif
        </div>
        <div class="p-3 {{ $colorClasses['bg'] }} rounded-lg">
            @if(str_starts_with($icon, 'fa-'))
                <i class="fas {{ $icon }} {{ $colorClasses['text'] }} text-2xl"></i>
            @else
                {!! $icon !!}
            @endif
        </div>
    </div>
    
    @if($action)
        <div class="mt-auto">
            <a href="{{ $action['url'] }}" 
               class="w-full text-center px-4 py-2 {{ $colorClasses['button'] }} text-white rounded-lg transition-colors duration-200 flex items-center justify-center">
                @if(isset($action['icon']))
                    <i class="fas {{ $action['icon'] }} mr-2"></i>
                @endif
                {{ $action['label'] }}
            </a>
        </div>
    @endif
</div>
