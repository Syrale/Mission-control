<x-app-layout>
    <!-- ❌ NO HEADER SLOT HERE (Removes the double bar) ❌ -->

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- NEW: INTEGRATED HEADER -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                
                <!-- Left: Back & Title -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="group flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-indigo-500 hover:border-indigo-500 transition shadow-sm">
                        <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>

                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
                                {{ $game->name }}
                            </h1>
                            <span class="hidden sm:inline-block px-2 py-1 text-xs font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 rounded border border-indigo-200 dark:border-indigo-800 uppercase tracking-wide">
                                Mission Control
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Manage your daily tasks, events, and maintenance.
                        </p>
                    </div>
                </div>

                <!-- Right: Settings Button -->
                <a href="{{ route('games.edit', $game) }}" class="flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Settings
                </a>
            </div>

            <!-- GRID CONTENT -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- LEFT COLUMN: Game Info & Add Task -->
                <div class="space-y-6">
                    <!-- GAME INFO -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h4 class="text-gray-400 text-sm uppercase tracking-wide mb-4 border-b border-gray-700 pb-2">Game Info</h4>
                        <div class="space-y-2 mb-4">
                            <p class="text-white font-medium">Developer: <span class="text-indigo-400">{{ $game->developer ?? 'Unknown' }}</span></p>
                            <p class="text-white font-medium">Timezone: <span class="text-indigo-400">{{ $game->timezone }}</span></p>
                            <p class="text-white font-medium">Reset: <span class="text-indigo-400">{{ $game->reset_hour }}:00</span></p>
                        </div>
                        @if($game->notes)
                            <div class="mt-4 pt-4 border-t border-gray-700">
                                <h5 class="text-gray-400 text-xs uppercase tracking-wide mb-2">Notes</h5>
                                <div class="text-gray-300 text-sm whitespace-pre-wrap bg-gray-900/50 p-3 rounded border border-gray-700/50">{{ $game->notes }}</div>
                            </div>
                        @endif
                    </div>

                    <!-- ADD NEW TASK FORM -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6" x-data="{ taskType: 'daily' }">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Add New Task</h3>
                        <form action="{{ route('games.tasks.store', $game) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Task Name</label>
                                    <input type="text" name="name" required placeholder="e.g. Spiral Abyss, Monthly Shop" class="w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Type</label>
                                    <select name="type" x-model="taskType" class="w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly (Mondays)</option>
                                        <option value="loop">Endgame Loop (Custom Days)</option>
                                        <option value="custom">One-Time Goal</option>
                                    </select>
                                </div>
                                <div x-show="taskType === 'loop'" class="p-3 bg-indigo-900/30 border border-indigo-500/50 rounded-md">
                                    <label class="block text-sm font-medium text-indigo-300 mb-1">Resets Every (Days)</label>
                                    <div class="flex gap-2 items-center">
                                        <input type="number" name="repeat_days" min="1" placeholder="e.g. 14" class="w-24 rounded-md border-gray-600 bg-gray-700 text-white shadow-sm">
                                        <span class="text-xs text-gray-400">(e.g. Enter <strong>14</strong> for Abyss)</span>
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">+ Add Task</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- CENTER COLUMN: TASK LISTS -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- SECTION: TASKS -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="font-bold text-lg text-indigo-400 mb-3">Daily Routines</h3>
                        <div class="space-y-2 mb-6">
                            @foreach($game->tasks->where('type', 'daily') as $task)
                                @include('games.partials.task-row', ['task' => $task])
                            @endforeach
                            @if($game->tasks->where('type', 'daily')->isEmpty())
                                <p class="text-gray-500 text-sm italic">No daily tasks set.</p>
                            @endif
                        </div>
                        <h3 class="font-bold text-lg text-purple-400 mb-3 pt-4 border-t border-gray-700">Weekly & Endgame</h3>
                        <div class="space-y-2 mb-6">
                            @foreach($game->tasks->whereIn('type', ['weekly', 'biweekly']) as $task)
                                @include('games.partials.task-row', ['task' => $task])
                            @endforeach
                            @if($game->tasks->whereIn('type', ['weekly', 'biweekly'])->isEmpty())
                                <p class="text-gray-500 text-sm italic">No weekly/endgame content.</p>
                            @endif
                        </div>
                        <h3 class="font-bold text-lg text-gray-400 mb-3 pt-4 border-t border-gray-700">One-Time / Goals</h3>
                        <div class="space-y-2">
                            @foreach($game->tasks->where('type', 'custom') as $task)
                                @include('games.partials.task-row', ['task' => $task])
                            @endforeach
                        </div>
                        <h3 class="font-bold text-lg text-purple-400 mb-3 pt-4 border-t border-gray-700">Endgame Cycle Tasks</h3>
                        <div class="space-y-2">
                            @foreach($game->tasks->where('type', 'loop') as $task)
                                @include('games.partials.task-row', ['task' => $task])
                            @endforeach
                        </div>
                    </div>

                    <!-- SECTION: EVENTS & MAINTENANCE GRID -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- EVENTS -->
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h3 class="font-bold text-lg mb-4 text-green-400">Active Events</h3>
                            <div class="space-y-3 mb-4">
                                @foreach($game->events as $event)
                                    <!-- FILTER: Hide events that ended -->
                                    @if($event->end_time->isFuture())
                                        <div class="bg-green-900/20 border border-green-800 p-3 rounded text-sm relative group">
                                            <div class="flex justify-between">
                                                <span class="font-bold text-green-400">{{ $event->name }}</span>
                                                <form action="{{ route('events.destroy', $event) }}" method="POST" class="opacity-0 group-hover:opacity-100">
                                                    @csrf @method('DELETE')
                                                    <button class="text-red-400 hover:text-red-600">&times;</button>
                                                </form>
                                            </div>
                                            <div class="text-xs text-gray-400">Ends {{ $event->end_time->diffForHumans() }}</div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <form action="{{ route('games.events.store', $game) }}" method="POST" class="space-y-2">
                                @csrf
                                <input type="text" name="name" placeholder="New Event Name" required class="w-full text-sm rounded bg-gray-900 border-gray-700 text-white">
                                <div class="flex gap-2">
                                    <input type="datetime-local" name="start_time" required class="w-1/2 text-xs rounded bg-gray-900 border-gray-700 text-white">
                                    <input type="datetime-local" name="end_time" required class="w-1/2 text-xs rounded bg-gray-900 border-gray-700 text-white">
                                </div>
                                <button class="w-full bg-green-700 hover:bg-green-600 text-white text-xs font-bold py-2 rounded">Add Event</button>
                            </form>
                        </div>

                        <!-- MAINTENANCE -->
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h3 class="font-bold text-lg mb-4 text-red-400">Maintenance</h3>
                            <div class="space-y-3 mb-4">
                                @foreach($game->maintenances as $maintenance)
                                    <!-- FILTER: Only show if Active OR Future -->
                                    @if($maintenance->end_at->isFuture())
                                        <div class="bg-red-900/20 border border-red-800 p-3 rounded text-sm relative group">
                                            <div class="flex justify-between">
                                                <span class="font-bold text-red-400">{{ $maintenance->title }}</span>
                                                <form action="{{ route('maintenances.destroy', $maintenance) }}" method="POST" class="opacity-0 group-hover:opacity-100">
                                                    @csrf @method('DELETE')
                                                    <button class="text-red-400 hover:text-red-600">&times;</button>
                                                </form>
                                            </div>
                                            @if(now()->between($maintenance->start_at, $maintenance->end_at))
                                                <div class="text-xs font-bold text-red-500 animate-pulse">OFFLINE NOW</div>
                                            @else
                                                <div class="text-xs text-gray-400">Starts {{ $maintenance->start_at->diffForHumans() }}</div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <form action="{{ route('games.maintenances.store', $game) }}" method="POST" class="space-y-2">
                                @csrf
                                <input type="text" name="title" placeholder="Patch v1.0" required class="w-full text-sm rounded bg-gray-900 border-gray-700 text-white placeholder-gray-500">
                                <p class="text-[10px] text-indigo-400 font-bold">Enter times in {{ config('timezones.list')[$game->timezone] ?? $game->timezone }}</p>
                                <div class="flex gap-2">
                                    <input type="datetime-local" name="start_at" required class="w-1/2 text-xs rounded bg-gray-900 border-gray-700 text-white">
                                    <input type="datetime-local" name="end_at" required class="w-1/2 text-xs rounded bg-gray-900 border-gray-700 text-white">
                                </div>
                                <button class="w-full bg-red-700 hover:bg-red-600 text-white text-xs font-bold py-2 rounded transition">Schedule Maintenance</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>