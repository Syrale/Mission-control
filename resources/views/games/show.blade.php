<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- HEADER: Title & Status (Timezone/Reset) -->
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
                            <!-- STATUS BADGES -->
                            <div class="flex gap-2">
                                <span class="px-2 py-1 text-xs font-bold text-indigo-400 bg-indigo-900/20 rounded border border-indigo-800">
                                    {{ $game->timezone }}
                                </span>
                                <span class="px-2 py-1 text-xs font-bold text-green-400 bg-green-900/20 rounded border border-green-800">
                                    Reset: {{ $game->reset_hour }}:00
                                </span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ $game->developer }}
                        </p>
                    </div>
                </div>

                <!-- Right: Settings Button -->
                <a href="{{ route('games.edit', $game) }}" class="flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Settings
                </a>
            </div>
            
            <!-- ERROR DISPLAY -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Whoops!</strong>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- MAIN GRID LAYOUT -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                <!-- LEFT COLUMN: TOOLS & WIDGETS -->
                <div class="space-y-6">
                    
                    <!-- 1. ADD TASK (Collapsed by Default) -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <span class="font-bold text-indigo-400">+ Add New Task</span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" class="p-6 border-t border-gray-700" x-transition x-cloak>
                            <div x-data="{ taskType: 'daily' }">
                                <form action="{{ route('games.tasks.store', $game) }}" method="POST">
                                    @csrf
                                    <div class="space-y-4">
                                        <!-- Name -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-400 mb-1">Task Name</label>
                                            <input type="text" name="name" required placeholder="e.g. Monthly Shop" class="w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                        <!-- Type -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-400 mb-1">Type</label>
                                            <select name="type" x-model="taskType" class="w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="daily">Daily</option>
                                                <option value="weekly">Weekly (7 Days)</option>
                                                <option value="monthly">Monthly (Shop Reset)</option>
                                                <option value="loop">Custom Cycle</option>
                                                <option value="custom">One-Time Goal</option>
                                            </select>
                                        </div>
                                        <!-- Options -->
                                        <div x-show="taskType === 'loop' || taskType === 'monthly'" class="p-3 bg-indigo-900/30 border border-indigo-500/50 rounded-md space-y-3">
                                            <div x-show="taskType === 'loop'">
                                                <label class="block text-sm font-medium text-indigo-300 mb-1">Days</label>
                                                <input type="number" name="repeat_days" min="1" placeholder="14" class="w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-indigo-300 mb-1">Last Reset Date</label>
                                                <input type="datetime-local" name="start_date" class="w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm text-sm">
                                            </div>
                                        </div>
                                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">Save Task</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 2. TIMERS & BANNERS (Open by Default) -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <span class="font-bold text-gray-200">Timers & Banners</span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="open" class="px-4 pb-4 border-t border-gray-700 pt-4" x-transition>
                            <!-- Active List -->
                            <div class="space-y-2 mb-4">
                                @foreach($game->events as $event)
                                    @if($event->end_time->isFuture())
                                        @php
                                            $colors = match($event->type) {
                                                'banner' => 'bg-yellow-900/20 border-yellow-600/50 text-yellow-400',
                                                'patch'  => 'bg-blue-900/20 border-blue-600/50 text-blue-400',
                                                default  => 'bg-green-900/20 border-green-600/50 text-green-400',
                                            };
                                        @endphp
                                        <div class="{{ $colors }} border p-2 rounded text-xs relative group">
                                            <div class="flex justify-between items-start">
                                                <span class="font-bold">{{ $event->name }}</span>
                                                <form action="{{ route('events.destroy', $event) }}" method="POST" class="opacity-0 group-hover:opacity-100">
                                                    @csrf @method('DELETE')
                                                    <button class="text-red-400 hover:text-red-600 font-bold">&times;</button>
                                                </form>
                                            </div>
                                            <div class="opacity-80 mt-1">{{ $event->end_time->diffForHumans() }}</div>
                                        </div>
                                    @endif
                                @endforeach
                                @if($game->events->where('end_time', '>', now())->isEmpty())
                                    <p class="text-gray-500 text-xs italic">No active timers.</p>
                                @endif
                            </div>

                            <!-- Add Timer Form (Within Toggle) -->
                            <div x-data="{ showAdd: false, mode: 'duration' }"> <!-- Added mode: 'duration' -->
                                
                                <button @click="showAdd = !showAdd" class="text-xs text-indigo-400 hover:text-indigo-300 font-bold flex items-center gap-1 mb-2">
                                    <span x-text="showAdd ? '- Cancel' : '+ Add Timer'"></span>
                                </button>

                                <form x-show="showAdd" action="{{ route('games.events.store', $game) }}" method="POST" class="space-y-3" x-transition>
                                    @csrf
                                    
                                    <!-- Name & Type -->
                                    <input type="text" name="name" placeholder="Name (e.g. Raiden Banner)" required class="w-full text-xs rounded bg-gray-900 border-gray-700 text-white">
                                    <select name="type" class="w-full text-xs rounded bg-gray-900 border-gray-700 text-white">
                                        <option value="event">Event (Green)</option>
                                        <option value="banner">Banner (Gold)</option>
                                        <option value="patch">Patch (Blue)</option>
                                    </select>

                                    <!-- Input Mode Switcher -->
                                    <div class="flex gap-3 text-[10px] uppercase font-bold tracking-wide pt-1">
                                        <button type="button" @click="mode = 'duration'" :class="mode === 'duration' ? 'text-white border-b-2 border-indigo-500' : 'text-gray-500 hover:text-gray-300'">Time Remaining</button>
                                        <button type="button" @click="mode = 'date'" :class="mode === 'date' ? 'text-white border-b-2 border-indigo-500' : 'text-gray-500 hover:text-gray-300'">Exact Date</button>
                                    </div>

                                    <!-- MODE A: Duration (Days/Hours Remaining) -->
                                    <div x-show="mode === 'duration'" class="flex gap-2">
                                        <!-- Hidden input to tell Controller we are using duration mode -->
                                        <input type="hidden" name="duration_mode" value="1" :disabled="mode !== 'duration'">
                                        
                                        <div class="w-1/2">
                                            <label class="text-[10px] text-gray-400 uppercase">Days Left</label>
                                            <input type="number" name="duration_days" placeholder="20" class="w-full text-xs rounded bg-gray-900 border-gray-700 text-white focus:border-indigo-500">
                                        </div>
                                        <div class="w-1/2">
                                            <label class="text-[10px] text-gray-400 uppercase">Hours Left</label>
                                            <input type="number" name="duration_hours" placeholder="0" class="w-full text-xs rounded bg-gray-900 border-gray-700 text-white focus:border-indigo-500">
                                        </div>
                                    </div>

                                    <!-- MODE B: Exact Dates (Original) -->
                                    <div x-show="mode === 'date'" class="flex gap-2">
                                        <div class="w-1/2">
                                            <label class="text-[10px] text-gray-400 uppercase">Start</label>
                                            <input type="datetime-local" name="start_time" class="w-full text-[10px] rounded bg-gray-900 border-gray-700 text-white">
                                        </div>
                                        <div class="w-1/2">
                                            <label class="text-[10px] text-gray-400 uppercase">End</label>
                                            <input type="datetime-local" name="end_time" class="w-full text-[10px] rounded bg-gray-900 border-gray-700 text-white">
                                        </div>
                                    </div>

                                    <button class="w-full bg-indigo-700 hover:bg-indigo-600 text-white text-xs font-bold py-2 rounded shadow-md transition">Add Timer</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 3. MAINTENANCE (Open by Default) -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <span class="font-bold text-red-400">Maintenance</span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="open" class="px-4 pb-4 border-t border-gray-700 pt-4" x-transition>
                            <div class="space-y-2 mb-4">
                            
                                @foreach($game->maintenances as $maintenance)
                                    @if($maintenance->end_at->isFuture())
                                        <div class="bg-red-900/20 border border-red-800 p-2 rounded text-xs relative group">
                                            <div class="flex justify-between">
                                                <span class="font-bold text-red-400">{{ $maintenance->title }}</span>
                                                <form action="{{ route('maintenances.destroy', $maintenance) }}" method="POST" class="opacity-0 group-hover:opacity-100">
                                                    @csrf @method('DELETE')
                                                    <button class="text-red-400 hover:text-red-600">&times;</button>
                                                </form>
                                            </div>
                                            @if(now()->between($maintenance->start_at, $maintenance->end_at))
                                                <div class="text-[10px] font-bold text-red-500 animate-pulse">OFFLINE NOW</div>
                                            @else
                                                <div class="text-[10px] text-gray-400">Starts {{ $maintenance->start_at->diffForHumans() }}</div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                                @if($game->maintenances->where('end_at', '>', now())->isEmpty())
                                    <p class="text-gray-500 text-xs italic">No maintenance scheduled.</p>
                                @endif
                            </div>

                            <!-- Add Maintenance Form (Within Toggle) -->
                            <div x-data="{ showAdd: false }">
                                <button @click="showAdd = !showAdd" class="text-xs text-red-400 hover:text-red-300 font-bold flex items-center gap-1 mb-2">
                                    <span x-text="showAdd ? '- Cancel' : '+ Schedule'"></span>
                                </button>
                                <form x-show="showAdd" action="{{ route('games.maintenances.store', $game) }}" method="POST" class="space-y-2" x-transition>
                                    @csrf
                                    <input type="text" name="title" placeholder="Patch v1.0" required class="w-full text-xs rounded bg-gray-900 border-gray-700 text-white">
                                    <div class="flex gap-1">
                                        <input type="datetime-local" name="start_at" required class="w-1/2 text-[10px] rounded bg-gray-900 border-gray-700 text-white">
                                        <input type="datetime-local" name="end_at" required class="w-1/2 text-[10px] rounded bg-gray-900 border-gray-700 text-white">
                                    </div>
                                    <button class="w-full bg-red-700 hover:bg-red-600 text-white text-xs font-bold py-1 rounded">Schedule</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 4. NOTES / SCRATCHPAD -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <span class="font-bold text-yellow-500">📝 Notes</span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="open" class="p-4 border-t border-gray-700" x-transition>
                            <form action="{{ route('games.update', $game) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                
                                {{-- Hidden Inputs to bypass validation if your Controller requires name/timezone --}}
                                <input type="hidden" name="name" value="{{ $game->name }}">
                                <input type="hidden" name="timezone" value="{{ $game->timezone }}">
                                <input type="hidden" name="reset_hour" value="{{ $game->reset_hour }}">

                                <label class="sr-only">Game Notes</label>
                                <textarea 
                                    name="notes" 
                                    rows="6" 
                                    class="w-full rounded-md border-gray-700 bg-gray-900 text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono"
                                    placeholder="Write farm routes, resource locations, or to-do lists here..."
                                >{{ $game->notes }}</textarea>

                                <div class="flex justify-end mt-2">
                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Save Notes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                
                
                <!-- RIGHT COLUMN: TASK LISTS (Main Content) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- SECTION: TASKS -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        
                        <!-- Daily -->
                        <h3 class="font-bold text-lg text-indigo-400 mb-3">Daily Routines</h3>
                        <div class="space-y-2 mb-6">
                            @foreach($game->tasks->where('type', 'daily') as $task)
                                @include('games.partials.task-row', ['task' => $task])
                            @endforeach
                            @if($game->tasks->where('type', 'daily')->isEmpty())
                                <p class="text-gray-500 text-sm italic">No daily tasks set.</p>
                            @endif
                        </div>

                        <!-- Weekly -->
                        <h3 class="font-bold text-lg text-purple-400 mb-3 pt-4 border-t border-gray-700">Weekly & Endgame</h3>
                        <div class="space-y-2 mb-6">
                            @foreach($game->tasks->whereIn('type', ['weekly', 'biweekly']) as $task)
                                @include('games.partials.task-row', ['task' => $task])
                            @endforeach
                            @if($game->tasks->whereIn('type', ['weekly', 'biweekly'])->isEmpty())
                                <p class="text-gray-500 text-sm italic">No weekly/endgame content.</p>
                            @endif
                        </div>

                        <!-- Custom/One-Time -->
                        <h3 class="font-bold text-lg text-gray-400 mb-3 pt-4 border-t border-gray-700">One-Time / Goals</h3>
                        <div class="space-y-2 mb-6">
                            @foreach($game->tasks->where('type', 'custom') as $task)
                                @include('games.partials.task-row', ['task' => $task])
                            @endforeach
                        </div>

                        <!-- Loop/Monthly -->
                        <h3 class="font-bold text-lg text-purple-400 mb-3 pt-4 border-t border-gray-700">Cycle Tasks</h3>
                        <div class="space-y-2">
                            @foreach($game->tasks->whereIn('type', ['loop', 'monthly']) as $task)
                                @include('games.partials.task-row', ['task' => $task])
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>