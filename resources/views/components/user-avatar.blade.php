@props(['user', 'size' => 'md', 'class' => '', 'isGroupAdmin' => false])

@php
    use Illuminate\Support\Facades\Storage;
    
    $sizes = [
        'sm' => 'h-8 w-8 text-sm',
        'md' => 'h-12 w-12 text-lg', 
        'lg' => 'h-20 w-20 text-2xl',
        'xl' => 'h-32 w-32 text-4xl'
    ];
    
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    // Cek apakah user adalah admin website atau admin grup
    $isAdmin = $user->role === 'admin' || $isGroupAdmin;
@endphp

<div class="avatar-container {{ $sizeClass }} {{ $class }}">
    @if($user->photo && Storage::exists($user->photo))
        <img src="{{ Storage::url($user->photo) }}" 
             alt="{{ $user->name }}" 
             class="h-full w-full rounded-full object-cover border-2 {{ $isAdmin ? 'border-yellow-400' : 'border-gray-300' }}">
    @else
        <div class="h-full w-full rounded-full flex items-center justify-center {{ $isAdmin ? 'bg-gradient-to-br from-yellow-400 to-yellow-600' : 'bg-gradient-to-br from-blue-400 to-blue-600' }} text-white font-bold relative overflow-hidden">
            @if($isAdmin)
                <!-- Admin Crown Icon -->
                <svg class="w-3/4 h-3/4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M5 16L3 7l5.5 5L12 4l3.5 8L21 7l-2 9H5zm2.7-2h8.6l.9-4.4-2.1 1.8L12 8.5l-3.1 2.9-2.1-1.8L7.7 14z"/>
                    <circle cx="7" cy="7" r="1"/>
                    <circle cx="12" cy="3" r="1"/>
                    <circle cx="17" cy="7" r="1"/>
                </svg>
            @else
                <!-- User Pawn Icon -->
                <svg class="w-3/4 h-3/4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2M21 9V7L12 9L3 7V9C3 10.1 3.9 11 5 11V12.5L6.5 18H17.5L19 12.5V11C20.1 11 21 10.1 21 9Z"/>
                </svg>
            @endif
            
            <!-- Shine effect for premium look -->
            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white to-transparent opacity-20 transform -skew-x-12"></div>
        </div>
    @endif
    
    @if($isAdmin)
        <!-- Admin badge indicator -->
        <div class="absolute -top-1 -right-1 bg-yellow-500 text-white text-xs rounded-full px-1 py-0.5 font-bold border-2 border-white">
            ★
        </div>
    @endif
</div>

<style>
.avatar-container {
    position: relative;
    flex-shrink: 0;
}

.avatar-container svg {
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.avatar-container:hover .absolute {
    animation: shimmer 1.5s ease-in-out;
}
</style>
