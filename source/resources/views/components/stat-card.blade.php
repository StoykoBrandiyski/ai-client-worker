@props(['title', 'value', 'color' => 'indigo'])

@php
    $colors = [
        'indigo' => 'text-indigo-600 bg-indigo-100',
        'orange' => 'text-orange-600 bg-orange-100',
        'green'  => 'text-green-600 bg-green-100',
        'blue'   => 'text-blue-600 bg-blue-100',
        'red'    => 'text-red-600 bg-red-100',
    ];
    $iconColor = $colors[$color] ?? $colors['indigo'];
@endphp

<div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-xl p-6">
    <div class="flex items-center">
        <div class="p-3 rounded-lg {{ $iconColor }}">
            @if($color == 'indigo')
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            @elseif($color == 'orange')
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            @elseif($color == 'green')
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            @else
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            @endif
        </div>
        <div class="ml-5">
            <p class="text-sm font-medium text-gray-500 truncate">{{ $title }}</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $value }}</p>
        </div>
    </div>
</div>
