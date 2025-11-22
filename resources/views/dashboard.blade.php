<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- SECTION 1: HEADER & CLOCK -->
            <div class="space-y-6">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
                            🚀 Mission Control
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manage your daily resets and events</p>
                    </div>
                    <a href="{{ route('games.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Add Game
                    </a>
                </div>

                <!-- DUAL CLOCK -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" 
                     x-data='{ 
                        now: new Date(),
                        timezone: localStorage.getItem("dash_timezone") || "UTC",
                        timezones: @json(config("timezones.list") ?? ["UTC" => "UTC"]),
                        init() { 
                            try { new Intl.DateTimeFormat("en-US", { timeZone: this.timezone }); } 
                            catch(e) { console.log("Using manual offset"); }
                            setInterval(() => { this.now = new Date(); }, 1000); 
                        },
                        updateTimezone(e) {
                            this.timezone = e.target.value;
                            localStorage.setItem("dash_timezone", this.timezone);
                        },
                        getServerTime() {
                            if (this.timezone.startsWith("+") || this.timezone.startsWith("-")) {
                                const offsetHours = parseInt(this.timezone.split(":")[0]);
                                const utc = this.now.getTime() + (this.now.getTimezoneOffset() * 60000);
                                return new Date(utc + (3600000 * offsetHours)).toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit", hour12: false });
                            }
                            try {
                                return new Intl.DateTimeFormat("en-US", { timeZone: this.timezone, hour: "2-digit", minute: "2-digit", hour12: false }).format(this.now);
                            } catch (e) { return "Invalid TZ"; }
                        }
                     }'
                     x-init="init()">
                    <!-- Server Time -->
                    <div class="bg-indigo-900 text-white overflow-hidden shadow-sm sm:rounded-lg border border-indigo-700 p-6 flex flex-col justify-between relative h-40">
                        <div class="absolute top-0 right-0 p-2 opacity-10 text-6xl font-black select-none">UTC</div>
                        <div class="z-10">
                            <div class="flex justify-between items-start">
                                <div class="text-xs font-bold text-indigo-300 uppercase tracking-widest mb-1">Server Time</div>
                                <select x-model="timezone" @change="updateTimezone" class="bg-indigo-800 border-none text-[10px] uppercase font-bold text-white rounded focus:ring-0 cursor-pointer py-0.5 pl-2 pr-6 h-6">
                                    <optgroup label="Saved"><template x-for="(label, key) in timezones"><option :value="isNaN(key) ? key : label" x-text="label" :selected="(isNaN(key) ? key : label) === timezone" class="text-black"></option></template></optgroup>
                                    <optgroup label="Offsets"><template x-for="i in 27"><option :value="(i-13 > 0 ? '+' : '') + (i-13) + ':00'" x-text="'UTC ' + (i-13 > 0 ? '+' : '') + (i-13) + ':00'" :selected="((i-13 > 0 ? '+' : '') + (i-13) + ':00') === timezone" class="text-black"></option></template></optgroup>
                                </select>
                            </div>
                            <div class="text-4xl font-mono font-bold tracking-tighter mt-2"><span x-text="getServerTime()"></span></div>
                            <div class="text-sm text-indigo-200 mt-1"><span x-text="timezone"></span></div>
                        </div>
                    </div>
                    <!-- Local Time -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col justify-between h-40">
                        <div>
                            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Your Local Time</div>
                            <div class="text-4xl font-mono font-bold text-gray-900 dark:text-white tracking-tighter mt-2"><span x-text="now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false})"></span></div>
                            <div class="text-sm text-gray-500 mt-1"><span x-text="now.toLocaleDateString([], {weekday: 'long', day: 'numeric'})"></span></div>
                        </div>
                        <div class="flex justify-end"><svg class="w-6 h-6 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: ALERTS -->
            @if(isset($missedTasks) && $missedTasks->count() > 0)
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md">
                    <p class="font-bold flex items-center"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>Missed Deadlines</p>
                    <ul class="list-disc list-inside text-sm mt-1">
                        @foreach($missedTasks as $task)
                            <li><strong>{{ $task->game->name }}:</strong> {{ $task->name }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- SECTION 3: GAMES (SWITCHABLE VIEW) -->
            <div x-data="{ view: localStorage.getItem('dashboard_view') || 'cards' }" 
                @view-change.window="view = $event.detail">

                @php
                    // Pre-calculate logic for sorting
                    $sortedGames = $games->map(function ($game) use ($todoTasks, $activeMaintenances) {
                        $gameTasks = $todoTasks->where('game_id', $game->id);
                        $earliestTask = $gameTasks->whereNotNull('next_due_at')->sortBy('next_due_at')->first();
                        $minutesUntilDue = $earliestTask ? now()->diffInMinutes($earliestTask->next_due_at, false) : 999999;

                        // Check if this game is in the active maintenance list
                        $isMaintenance = $activeMaintenances->where('game_id', $game->id)->isNotEmpty();

                        if ($isMaintenance) {
                            $score = 0; // Priority 0: Move Maintenance to TOP
                        } elseif ($gameTasks->isEmpty()) {
                            $score = 4; 
                        } elseif ($minutesUntilDue < 180) { 
                            $score = 1; 
                        } elseif ($minutesUntilDue < 1440) { 
                            $score = 2; 
                        } else {
                            $score = 3;
                        }
                        
                        return (object) [
                            'game' => $game,
                            'score' => $score,
                            'minutes' => $minutesUntilDue,
                            'tasks' => $gameTasks,
                            'isMaintenance' => $isMaintenance
                        ];
                    })->sortBy([ ['score', 'asc'], ['minutes', 'asc'] ]);
                @endphp

                <!-- ========================================== -->
                <!-- OPTION A: GRID / CARDS VIEW                -->
                <!-- ========================================== -->
                <div x-show="view === 'cards'" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    @foreach($sortedGames as $item)
                        @php
                            $game = $item->game;
                            $gameTasks = $item->tasks;
                            
                            // --- GRID STYLING LOGIC ---
                            if ($item->isMaintenance) {
                                // Maintenance Style (Blinking Amber)
                                $statusColor = 'border-amber-500 ring-1 ring-amber-400 animate-pulse';
                                $bgHeader = 'bg-amber-100 dark:bg-amber-900/40';
                                $cardBg = 'bg-amber-50 dark:bg-gray-800';
                            } else {
                                // Normal Styles
                                $statusColor = match($item->score) {
                                    1 => 'border-red-500 shadow-red-500/20 ring-1 ring-red-500', 
                                    2 => 'border-amber-400 shadow-amber-500/10',
                                    3 => 'border-gray-200 dark:border-gray-700',
                                    4 => 'border-green-200 dark:border-green-900/30 opacity-75',
                                    default => 'border-gray-200'
                                };
                                $bgHeader = match($item->score) {
                                    1 => 'bg-red-50 dark:bg-red-900/20',
                                    2 => 'bg-amber-50 dark:bg-amber-900/20',
                                    3 => 'bg-gray-50 dark:bg-gray-800',
                                    4 => 'bg-green-50 dark:bg-green-900/20',
                                    default => 'bg-gray-50'
                                };
                                $cardBg = 'bg-white dark:bg-gray-800';
                            }
                        @endphp

                        <div class="{{ $cardBg }} rounded-xl shadow-sm border {{ $statusColor }} flex flex-col h-full transition hover:shadow-md">
                            <!-- Card Header -->
                            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 {{ $bgHeader }} rounded-t-xl flex justify-between items-center">
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-lg truncate flex items-center gap-2">
                                        {{ $game->name }}
                                        @if($item->isMaintenance) <span title="Maintenance Mode">🔧</span> @endif
                                    </h3>
                                    <p class="text-xs text-gray-500">{{ $game->developer ?? 'Game' }}</p>
                                </div>

                                @if($item->isMaintenance)
                                    <span class="bg-amber-500 text-white text-[10px] uppercase font-black px-2 py-1 rounded shadow-sm tracking-widest">Offline</span>
                                @elseif($gameTasks->count() > 0)
                                    <span class="bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 text-xs font-bold px-2 py-1 rounded-full">{{ $gameTasks->count() }}</span>
                                @else
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @endif
                            </div>

                            <!-- Card Body -->
                            <div class="p-4 flex-1 overflow-y-auto max-h-64 scrollbar-thin">
                                @if($item->isMaintenance)
                                    <div class="h-full flex flex-col items-center justify-center text-amber-600 dark:text-amber-500 py-6 space-y-2">
                                        <svg class="w-10 h-10 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span class="font-bold text-sm uppercase">Servers Offline</span>
                                        <p class="text-xs text-center px-4">Tasks are hidden during maintenance.</p>
                                    </div>
                                @elseif($gameTasks->isEmpty())
                                    <div class="h-full flex flex-col items-center justify-center text-gray-400 text-sm py-6">
                                        <span>All caught up!</span>
                                    </div>
                                @else
                                    <ul class="space-y-3">
                                        @foreach($gameTasks as $task)
                                            @php
                                                $isOverdue = $task->next_due_at && $task->next_due_at->isPast();
                                                $isUrgent = $task->next_due_at && $task->next_due_at->diffInHours(now()) < 3;
                                            @endphp
                                            <li class="flex items-start group">
                                                <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="mt-0.5 mr-3">
                                                    @csrf @method('PATCH')
                                                    <input type="checkbox" onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer w-4 h-4">
                                                </form>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex justify-between items-start">
                                                        <p class="text-sm font-medium {{ $isOverdue ? 'text-red-600 line-through' : 'text-gray-700 dark:text-gray-300' }}">{{ $task->name }}</p>
                                                    </div>
                                                    @if($task->next_due_at)
                                                        <div class="flex items-center gap-2 mt-0.5" x-data="{ local: new Date('{{ $task->next_due_at->toIso8601String() }}').toLocaleTimeString([], {hour:'2-digit', minute:'2-digit', hour12:false}) }">
                                                            <span class="text-[10px] font-mono px-1.5 rounded {{ $isOverdue ? 'bg-red-100 text-red-800' : ($isUrgent ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-500') }}">{{ $task->next_due_at->format('H:i') }} UTC</span>
                                                            <span class="text-[10px] text-gray-400" x-text="local"></span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 rounded-b-xl text-right">
                                <a href="{{ route('games.show', $game) }}" class="text-xs font-bold text-indigo-500 hover:text-indigo-700 uppercase tracking-wider">Settings &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- ========================================== -->
                <!-- OPTION B: LIST / ACCORDION VIEW            -->
                <!-- ========================================== -->
                <div x-show="view === 'list'" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="space-y-4" x-data="{ activeGame: null }">
                    
                    @foreach($sortedGames as $item)
                        @php
                            $game = $item->game;
                            $gameTasks = $item->tasks;
                            $hasCritical = $item->score === 1;
                            $hasWarning = $item->score === 2;
                            
                            $borderColor = 'border-gray-200 dark:border-gray-700'; 
                            $bgColor = 'bg-white dark:bg-gray-800';
                            $statusIcon = '';
                            $extraClasses = '';

                            // --- LIST STYLING LOGIC ---
                            if ($item->isMaintenance) {
                                $borderColor = 'border-amber-400 dark:border-amber-500 border-l-8';
                                $bgColor = 'bg-amber-50 dark:bg-gray-800'; 
                                $statusIcon = '🔧';
                                $extraClasses = 'animate-pulse'; // BLINKING EFFECT
                            } elseif ($hasCritical) {
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

                        <div class="rounded-lg border {{ $borderColor }} {{ $bgColor }} {{ $extraClasses }} overflow-hidden transition-all duration-300">
                            <button @click="activeGame === {{ $game->id }} ? activeGame = null : activeGame = {{ $game->id }}" 
                                    class="w-full px-6 py-4 flex items-center justify-between focus:outline-none group">
                                <div class="flex items-center gap-4">
                                    <div class="text-left">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                            {{ $game->name }} <span class="text-xs font-mono">{{ $statusIcon }}</span>
                                        </h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $game->developer }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6">
                                    <div class="flex flex-col items-end">
                                        @if($item->isMaintenance)
                                            <span class="text-amber-600 font-bold text-sm uppercase tracking-widest">Offline</span>
                                        @else
                                            <span class="text-2xl font-bold {{ $gameTasks->count() > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400' }}">
                                                {{ $gameTasks->count() }}
                                            </span>
                                            <span class="text-[10px] uppercase text-gray-400 font-bold tracking-wider">Pending</span>
                                        @endif
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" 
                                            :class="{'rotate-180': activeGame === {{ $game->id }}}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </button>

                            <div x-show="activeGame === {{ $game->id }}" x-collapse class="border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800/50">
                                <div class="p-6">
                                    @if($item->isMaintenance)
                                        <div class="mb-4 text-sm bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-200 p-4 rounded-lg flex items-center gap-3">
                                            <svg class="w-6 h-6 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <div>
                                                <strong>Under Maintenance</strong><br>
                                                <span class="text-xs">Game servers are currently unavailable. Resets will be tracked once servers are online.</span>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Existing Task List Logic (kept same as before) -->
                                    @if($gameTasks->isEmpty() && !$item->isMaintenance)
                                        <div class="text-center py-4 text-green-500 font-medium">All tasks complete.</div>
                                    @elseif($gameTasks->isNotEmpty())
                                        <div class="space-y-3">
                                            @foreach($gameTasks as $task)
                                                <!-- Task Item Logic -->
                                                @php
                                                    $taskColor = 'text-gray-700 dark:text-gray-200';
                                                    $timeColor = 'text-gray-400';
                                                    $isUrgent = false;
                                                    $isOverdue = false;
                                                    if ($task->next_due_at) {
                                                        if ($task->next_due_at->isPast()) {
                                                            $isOverdue = true;
                                                            $taskColor = 'text-red-800 dark:text-red-300 font-bold line-through opacity-70';
                                                            $timeColor = 'text-red-600 font-bold'; 
                                                        } else {
                                                            $hours = $task->next_due_at->diffInHours(now());
                                                            if ($hours < 3) {
                                                                $taskColor = 'text-red-600 dark:text-red-400 font-bold';
                                                                $timeColor = 'text-red-500';
                                                                $isUrgent = true;
                                                            } elseif ($hours < 24) { $timeColor = 'text-amber-500'; }
                                                        }
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
                                                                    {{ $task->title ?? $task->name }} 
                                                                    @if($isUrgent) <span class="ml-2 text-[10px] bg-red-100 text-red-800 px-1.5 rounded border border-red-200 uppercase">Critical</span> @endif
                                                                    @if($isOverdue) <span class="ml-2 text-[10px] bg-gray-200 text-gray-600 px-1.5 rounded border border-gray-300 uppercase">Missed</span> @endif
                                                                </div>
                                                                <span class="text-[10px] uppercase tracking-wide text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 rounded">{{ $task->type }}</span>
                                                            </div>
                                                            @if($task->next_due_at)
                                                                <div class="flex flex-col items-end text-right" 
                                                                        x-data="{ local: new Date('{{ $task->next_due_at->toIso8601String() }}').toLocaleTimeString([], {hour:'2-digit', minute:'2-digit', hour12:false}) }">
                                                                    <span class="text-[10px] font-mono font-bold {{ $timeColor }}">
                                                                        {{ $isOverdue ? 'Missed' : 'Due' }} {{ $task->next_due_at->setTimezone('UTC')->format('H:i') }} UTC
                                                                    </span>
                                                                    <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500">(<span x-text="local"></span> Local)</span>
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
                </div> <!-- End Accordion View -->
            </div> <!-- End X-Data Wrapper -->
        </div>
    </div>
</x-app-layout>