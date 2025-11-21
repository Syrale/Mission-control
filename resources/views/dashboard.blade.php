<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- SECTION 1: HEADER (With Add Button) & DUAL CLOCK -->
            <div class="space-y-6">
                
                <!-- HEADER ROW -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
                            🚀 Mission Control
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manage your daily resets and events</p>
                    </div>
                    
                    <!-- ADD GAME BUTTON -->
                    <a href="{{ route('games.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Add Game
                    </a>
                </div>

                <!-- DUAL CLOCK DASHBOARD -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" 
                     x-data='{ 
                        now: new Date(),
                        timezone: localStorage.getItem("dash_timezone") || "UTC",
                        timezones: @json(config("timezones.list") ?? ["UTC" => "UTC"]),
                        init() { 
                            // Check if timezone is valid, otherwise default to UTC
                            try { new Intl.DateTimeFormat("en-US", { timeZone: this.timezone }); } 
                            catch(e) { 
                                // If it fails (e.g. manual offset "-07:00"), we handle it manually in display
                                console.log("Using manual offset mode for", this.timezone);
                            }
                            setInterval(() => { this.now = new Date(); }, 1000); 
                        },
                        updateTimezone(e) {
                            this.timezone = e.target.value;
                            localStorage.setItem("dash_timezone", this.timezone);
                        },
                        getServerTime() {
                            // Handle Manual Offsets (starts with + or -)
                            if (this.timezone.startsWith("+") || this.timezone.startsWith("-")) {
                                const offsetHours = parseInt(this.timezone.split(":")[0]);
                                // Calculate target time by adjusting UTC
                                const utc = this.now.getTime() + (this.now.getTimezoneOffset() * 60000);
                                const targetTime = new Date(utc + (3600000 * offsetHours));
                                return targetTime.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit", hour12: false });
                            }
                            // Handle Standard Timezones
                            try {
                                return new Intl.DateTimeFormat("en-US", { timeZone: this.timezone, hour: "2-digit", minute: "2-digit", hour12: false }).format(this.now);
                            } catch (e) {
                                return "Invalid TZ";
                            }
                        }
                     }'
                     x-init="init()">
                    
                    <!-- CLOCK 1: SERVER TIME (Editable) -->
                    <div class="bg-indigo-900 text-white overflow-hidden shadow-sm sm:rounded-lg border border-indigo-700 p-6 flex flex-col justify-between relative h-40">
                        <div class="absolute top-0 right-0 p-2 opacity-10 text-6xl font-black select-none">UTC</div>
                        <div class="z-10">
                            <div class="flex justify-between items-start">
                                <div class="text-xs font-bold text-indigo-300 uppercase tracking-widest mb-1">Server Time</div>
                                <select x-model="timezone" @change="updateTimezone" class="bg-indigo-800 border-none text-[10px] uppercase font-bold text-white rounded focus:ring-0 cursor-pointer py-0.5 pl-2 pr-6 h-6">
                                    <optgroup label="Saved">
                                        <template x-for="(label, key) in timezones">
                                            <option :value="isNaN(key) ? key : label" x-text="label" :selected="(isNaN(key) ? key : label) === timezone" class="text-black"></option>
                                        </template>
                                    </optgroup>
                                    <optgroup label="Offsets">
                                         <!-- Manual Offsets for Display -->
                                        <template x-for="i in 27">
                                            <option :value="(i-13 > 0 ? '+' : '') + (i-13) + ':00'" x-text="'UTC ' + (i-13 > 0 ? '+' : '') + (i-13) + ':00'" :selected="((i-13 > 0 ? '+' : '') + (i-13) + ':00') === timezone" class="text-black"></option>
                                        </template>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="text-4xl font-mono font-bold tracking-tighter mt-2">
                                <span x-text="getServerTime()"></span>
                            </div>
                            <div class="text-sm text-indigo-200 mt-1">
                                <span x-text="timezone"></span>
                            </div>
                        </div>
                    </div>

                    <!-- CLOCK 2: LOCAL TIME (Fixed) -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col justify-between h-40">
                        <div>
                            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Your Local Time</div>
                            <div class="text-4xl font-mono font-bold text-gray-900 dark:text-white tracking-tighter mt-2">
                                <span x-text="now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false})"></span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                <span x-text="now.toLocaleDateString([], {weekday: 'long', day: 'numeric'})"></span>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <svg class="w-6 h-6 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: SERVER ALERTS -->
            @if($activeMaintenances->isNotEmpty())
                <div class="bg-red-500 text-white p-4 rounded-lg shadow-lg animate-pulse flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <h3 class="font-bold text-lg">SERVER OFFLINE</h3>
                        @foreach($activeMaintenances as $maintenance)
                            <p class="text-sm">{{ $maintenance->game->name }}: {{ $maintenance->title }} (Ends {{ $maintenance->end_at->format('H:i') }} UTC)</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- SECTION 3: MAIN ACCORDION STACK (SMART SORTED) -->
            <div class="space-y-4" x-data="{ activeGame: null }">
                
                <!-- PHP SORTING LOGIC -->
                @php
                    $sortedGames = $games->map(function ($game) use ($todoTasks) {
                        $gameTasks = $todoTasks->where('game_id', $game->id);
                        $earliestTask = $gameTasks->whereNotNull('next_due_at')->sortBy('next_due_at')->first();
                        $minutesUntilDue = $earliestTask ? now()->diffInMinutes($earliestTask->next_due_at, false) : 999999;

                        if ($gameTasks->isEmpty()) {
                            $score = 4;
                        } elseif ($minutesUntilDue < 180) { // 3 Hours
                            $score = 1;
                        } elseif ($minutesUntilDue < 1440) { // 24 Hours
                            $score = 2;
                        } else {
                            $score = 3;
                        }

                        return (object) [
                            'game' => $game,
                            'score' => $score,
                            'minutes' => $minutesUntilDue,
                            'tasks' => $gameTasks
                        ];
                    })->sortBy([ ['score', 'asc'], ['minutes', 'asc'] ]);
                @endphp

                @foreach($sortedGames as $item)
                    @php
                        $game = $item->game;
                        $gameTasks = $item->tasks;
                        $hasCritical = $item->score === 1;
                        $hasWarning = $item->score === 2;
                        
                        $borderColor = 'border-gray-200 dark:border-gray-700'; 
                        $bgColor = 'bg-white dark:bg-gray-800';
                        $statusIcon = '';

                        if ($hasCritical) {
                            $borderColor = 'border-red-500 dark:border-red-600 shadow-red-500/20 shadow-lg animate-pulse'; 
                            $bgColor = 'bg-red-50 dark:bg-red-900/10';
                            $statusIcon = '🔴'; 
                        } elseif ($hasWarning) {
                            $borderColor = 'border-amber-400 dark:border-amber-500';
                            $bgColor = 'bg-amber-50 dark:bg-amber-900/10';
                            $statusIcon = '🟠'; 
                        } elseif ($gameTasks->isEmpty()) {
                            $bgColor = 'bg-gray-50 dark:bg-gray-900/50 opacity-75';
                        }
                    @endphp

                    <!-- GAME CARD -->
                    <div class="rounded-lg border {{ $borderColor }} {{ $bgColor }} overflow-hidden transition-all duration-300">
                        <button @click="activeGame === {{ $game->id }} ? activeGame = null : activeGame = {{ $game->id }}" 
                                class="w-full px-6 py-4 flex items-center justify-between focus:outline-none group">
                            <div class="flex items-center gap-4">
                                <div class="text-left">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                        {{ $game->name }} <span class="text-xs">{{ $statusIcon }}</span>
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $game->developer }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="flex flex-col items-end">
                                    <span class="text-2xl font-bold {{ $gameTasks->count() > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400' }}">
                                        {{ $gameTasks->count() }}
                                    </span>
                                    <span class="text-[10px] uppercase text-gray-400 font-bold tracking-wider">Pending</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" 
                                     :class="{'rotate-180': activeGame === {{ $game->id }}}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </button>

                        <div x-show="activeGame === {{ $game->id }}" x-collapse class="border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800/50">
                            <div class="p-6">
                                @if($gameTasks->isEmpty())
                                    <div class="text-center py-4 text-green-500 font-medium">All tasks complete. Good job, Commander.</div>
                                @else
                                    <div class="space-y-3">
                                        @foreach($gameTasks as $task)
                                            @php
                                                $taskColor = 'text-gray-700 dark:text-gray-200';
                                                $timeColor = 'text-gray-400';
                                                $isUrgent = false;
                                                if ($task->next_due_at) {
                                                    $hours = $task->next_due_at->diffInHours(now());
                                                    if ($hours < 3) {
                                                        $taskColor = 'text-red-600 dark:text-red-400 font-bold';
                                                        $timeColor = 'text-red-500';
                                                        $isUrgent = true;
                                                    } elseif ($hours < 24) { $timeColor = 'text-amber-500'; }
                                                }
                                            @endphp
                                            <div class="flex items-center justify-between group hover:bg-gray-50 dark:hover:bg-gray-700/30 p-2 rounded transition-colors">
                                                <div class="flex items-center gap-3 w-full">
                                                    <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <input type="checkbox" onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer w-5 h-5">
                                                    </form>
                                                    <div class="flex-1 flex justify-between items-center gap-4">
                                                        <div>
                                                            <div class="{{ $taskColor }} text-sm">
                                                                {{ $task->name }} @if($isUrgent) <span class="ml-2 text-[10px] bg-red-100 text-red-800 px-1.5 rounded border border-red-200 uppercase">Critical</span> @endif
                                                            </div>
                                                            <span class="text-[10px] uppercase tracking-wide text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 rounded">{{ $task->type }}</span>
                                                        </div>
                                                        @if($task->next_due_at)
                                                            <div class="flex flex-col items-end text-right" x-data>
                                                                <span class="text-[10px] font-mono font-bold {{ $timeColor }}">
                                                                    Due {{ $task->next_due_at->setTimezone('UTC')->format('H:i') }} UTC
                                                                </span>
                                                                <span class="text-[9px] text-gray-400" 
                                                                      x-text="'(' + new Date('{{ $task->next_due_at->toISOString() }}').toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false}) + ' Local)'">
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                                    <a href="{{ route('games.show', $game) }}" class="text-xs font-bold text-indigo-500 hover:text-indigo-600 uppercase tracking-wider">Manage Game Settings &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                @if($games->isEmpty())
                    <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300">
                        <h3 class="text-gray-500 font-medium">No games detected.</h3>
                        <p class="text-sm text-gray-400 mb-4">Add your first game to start tracking.</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>