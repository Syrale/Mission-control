<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- SECTION 1: HEADER & CLOCK --}}
            <div class="space-y-6">
                @include('dashboard.partials.header')
                @include('dashboard.partials.clock')
            </div>

            {{-- SECTION 2: ALERTS --}}
            @include('dashboard.partials.alerts')

            {{-- SECTION 3: GAMES CONTAINER --}}
            <div x-data="{ view: localStorage.getItem('dashboard_view') || 'cards' }" 
                 @view-change.window="view = $event.detail">

                {{-- View Toggle Buttons --}}
                <div class="flex justify-end mb-4">
                    <div class="bg-gray-100 dark:bg-gray-700 p-1 rounded-lg flex gap-1">
                        <button @click="view = 'cards'; localStorage.setItem('dashboard_view', 'cards')" :class="view === 'cards' ? 'bg-white dark:bg-gray-600 shadow text-indigo-600' : 'text-gray-500'" class="px-3 py-1 rounded text-xs font-bold transition">Grid</button>
                        <button @click="view = 'list'; localStorage.setItem('dashboard_view', 'list')" :class="view === 'list' ? 'bg-white dark:bg-gray-600 shadow text-indigo-600' : 'text-gray-500'" class="px-3 py-1 rounded text-xs font-bold transition">List</button>
                        <button @click="view = 'schedule'; localStorage.setItem('dashboard_view', 'schedule')" :class="view === 'schedule' ? 'bg-white dark:bg-gray-600 shadow text-indigo-600' : 'text-gray-500'" class="px-3 py-1 rounded text-xs font-bold transition">Calendar</button>
                    </div>
                </div>

                {{-- The Views (Data passed from Controller) --}}
                @include('dashboard.partials.games-grid', ['sortedGames' => $sortedGames])
                @include('dashboard.partials.games-list', ['sortedGames' => $sortedGames])
                @include('dashboard.partials.schedule', ['calendar' => $calendar])
            </div>

        </div>
    </div>
</x-app-layout>