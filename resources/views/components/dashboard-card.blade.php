@props(['title', 'icon' => 'circle', 'color' => 'blue'])

<div class="p-6 bg-white border-l-4 border-{{ $color }}-500 rounded-xl shadow-md flex items-center space-x-4 hover:shadow-lg transition">
    <div class="text-{{ $color }}-500">
        <x-dynamic-component :component="'lucide-' . $icon" class="w-8 h-8" />
    </div>
    <div>
        <h3 class="text-lg font-semibold">{{ $title }}</h3>
        <p class="text-sm text-gray-500">Klik untuk melihat detail</p>
    </div>
</div>
