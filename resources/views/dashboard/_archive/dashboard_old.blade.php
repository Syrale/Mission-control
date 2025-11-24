{{-- 
    DEPRECATED: This file is replaced by dashboard/index.blade.php as of v0.5.0
    Scheduled for removal in v0.6.0 (Est. Dec 2025)
    DO NOT EDIT. For reference only.
--}}

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

        <!-- SECTION 2: TIERED ALERTS SYSTEM -->
        <div class="space-y-4">

            <!-- 1. MISSED DEADLINES (Past Due) - COLLAPSIBLE & DISMISSIBLE -->
            @if(isset($missedTasks) && $missedTasks->count() > 0)
                <div x-data="{ open: false, visible: true }" 
                    x-show="visible" 
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded shadow-sm mb-6">
                    
                    <!-- Header (Click to Toggle) -->
                    <div class="flex justify-between items-center p-4 cursor-pointer hover:bg-red-200/50 dark:hover:bg-red-900/50 transition rounded-t" @click="open = !open">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-bold uppercase tracking-wide text-xs">
                                Missed Deadlines ({{ $missedTasks->count() }})
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Toggle Icon -->
                            <span class="text-xs font-semibold opacity-75" x-text="open ? 'Hide Details' : 'Show Details'"></span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            
                            <!-- Vertical Divider -->
                            <div class="h-4 w-px bg-red-300 dark:bg-red-700 mx-1"></div>

                            <!-- Close Button -->
                            <button @click.stop="visible = false" title="Dismiss Alert" class="text-red-500 hover:text-red-800 dark:text-red-400 dark:hover:text-white p-1 rounded hover:bg-red-200 dark:hover:bg-red-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- List (Hidden by default) -->
                    <div x-show="open" x-collapse class="border-t border-red-200 dark:border-red-800/50">
                        <div class="p-4 pt-2">
                            <p class="text-xs mb-3 opacity-75 italic">
                                These tasks were not completed before the server reset. Checking them off will remove them from this list.
                            </p>
                            <ul class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($missedTasks as $task)
                                    <li class="text-sm flex items-center gap-2 bg-white/50 dark:bg-black/20 p-2 rounded border border-red-200 dark:border-red-800/30">
                                        <!-- Checkbox to complete directly from here -->
                                        <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="flex items-center">
                                            @csrf @method('PATCH')
                                            <input type="checkbox" onchange="this.form.submit()" class="rounded border-red-300 text-red-600 focus:ring-red-500 cursor-pointer w-4 h-4 mr-2">
                                        </form>
                                        <span class="font-bold text-xs uppercase text-red-500 tracking-wider border border-red-200 px-1 rounded bg-white dark:bg-transparent">{{ $task->game->name }}</span>
                                        <span class="truncate">{{ $task->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- CALCULATE TIERS -->
            @php
                $alerts = [
                    'critical' => collect(),
                    'urgent'   => collect(),
                    'upcoming' => collect(),
                ];

                $now = now();

                foreach($games as $g) {
                    foreach($g->events as $e) {
                        if ($e->end_time->isPast()) continue;
                        $e->game_name = $g->name;
                        
                        if ($e->end_time->lte($now->copy()->addHours(6))) {
                            $alerts['critical']->push($e);
                        }
                        elseif ($e->end_time->lte($now->copy()->addHours(24))) {
                            $alerts['urgent']->push($e);
                        }
                        elseif ($e->end_time->lte($now->copy()->addDays(3))) {
                            $alerts['upcoming']->push($e);
                        }
                    }
                }
            @endphp

            <!-- TIER 1: CRITICAL (< 6 HOURS) -->
            @if($alerts['critical']->isNotEmpty())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 shadow-lg shadow-red-500/10 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-red-500 animate-pulse"></div>
                    <p class="font-black text-red-600 dark:text-red-400 flex items-center uppercase tracking-widest text-xs mb-2">
                        <svg class="w-4 h-4 mr-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.929-7.071s-1.929 2.5-1.929 7.071c1.814 1.273 3-1 3-9 4.299-4.299 4.001 4.001 5.002 6.002z"></path></svg>
                        Last Day
                    </p>
                    <div class="space-y-2">
                        @foreach($alerts['critical'] as $event)
                            <div class="flex justify-between items-center bg-white dark:bg-red-900/40 p-2 rounded border border-red-100 dark:border-red-800/50">
                                <div>
                                    <span class="text-xs font-bold text-red-500 uppercase mr-1">{{ $event->game_name }}</span>
                                    <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $event->name }}</span>
                                </div>
                                <span class="text-xs font-mono font-bold text-red-600">{{ $event->end_time->diffForHumans(null, true, true) }} left</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- TIER 2: URGENT (< 24 HOURS) -->
            @if($alerts['urgent']->isNotEmpty())
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                    <p class="font-bold text-amber-600 dark:text-amber-400 flex items-center uppercase tracking-wide text-xs mb-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Ending Today
                    </p>
                    <div class="space-y-2">
                        @foreach($alerts['urgent'] as $event)
                            <div class="flex justify-between items-center bg-white/50 dark:bg-amber-900/30 p-2 rounded border border-amber-100 dark:border-amber-800/50">
                                <div>
                                    <span class="text-xs font-bold text-gray-500 uppercase mr-1">{{ $event->game_name }}</span>
                                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $event->name }}</span>
                                </div>
                                <span class="text-xs font-mono text-amber-600 dark:text-amber-400">{{ $event->end_time->diffForHumans(null, true, true) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- TIER 3: HEADS UP (< 3 DAYS) -->
            @if($alerts['upcoming']->isNotEmpty())
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <p class="font-bold text-blue-600 dark:text-blue-400 flex items-center uppercase tracking-wide text-xs mb-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Ending Soon (3 Days)
                    </p>
                    <div class="space-y-1">
                        @foreach($alerts['upcoming'] as $event)
                            <div class="flex justify-between items-center px-2 py-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong class="text-gray-800 dark:text-gray-300">{{ $event->game_name }}:</strong> {{ $event->name }}
                                </span>
                                <span class="text-xs text-blue-500">{{ $event->end_time->format('D, M j') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- SECTION 3: GAMES (SWITCHABLE VIEW) -->
        <div x-data="{ view: localStorage.getItem('dashboard_view') || 'cards' }" 
            @view-change.window="view = $event.detail">

            @php
                $sortedGames = $games->map(function ($game) use ($todoTasks, $activeMaintenances) {
                    $gameTasks = $todoTasks->where('game_id', $game->id);
                    $earliestTask = $gameTasks->whereNotNull('next_due_at')->sortBy('next_due_at')->first();
                    $minutesUntilDue = $earliestTask ? now()->diffInMinutes($earliestTask->next_due_at, false) : 999999;
                    $isMaintenance = $activeMaintenances->where('game_id', $game->id)->isNotEmpty();
                    
                    // Get active maintenance for countdown
                    $activeMaint = $activeMaintenances->where('game_id', $game->id)->first();

                    if ($isMaintenance) {
                        $score = 0;
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
                        'isMaintenance' => $isMaintenance,
                        'maintenance' => $activeMaint
                    ];
                })->sortBy([ ['score', 'asc'], ['minutes', 'asc'] ]);
            @endphp

            <!-- View Toggles -->
            <div class="flex justify-end mb-4">
                <div class="bg-gray-100 dark:bg-gray-700 p-1 rounded-lg flex gap-1">
                    <button @click="view = 'cards'; localStorage.setItem('dashboard_view', 'cards')" :class="view === 'cards' ? 'bg-white dark:bg-gray-600 shadow text-indigo-600' : 'text-gray-500'" class="px-3 py-1 rounded text-xs font-bold transition">Grid</button>
                    <button @click="view = 'list'; localStorage.setItem('dashboard_view', 'list')" :class="view === 'list' ? 'bg-white dark:bg-gray-600 shadow text-indigo-600' : 'text-gray-500'" class="px-3 py-1 rounded text-xs font-bold transition">List</button>
                    <button @click="view = 'schedule'; localStorage.setItem('dashboard_view', 'schedule')" :class="view === 'schedule' ? 'bg-white dark:bg-gray-600 shadow text-indigo-600' : 'text-gray-500'" class="px-3 py-1 rounded text-xs font-bold transition">Calendar</button>
                </div>
            </div>

            <!-- GRID / CARDS VIEW -->
            <div x-show="view === 'cards'" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                @foreach($sortedGames as $item)
                    @php
                        $game = $item->game;
                        $gameTasks = $item->tasks;
                        
                        if ($item->isMaintenance) {
                            $statusColor = 'border-amber-500 ring-1 ring-amber-400 animate-pulse';
                            $bgHeader = 'bg-amber-100 dark:bg-amber-900/40';
                            $cardBg = 'bg-amber-50 dark:bg-gray-800';
                        } else {
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
                                <div class="h-full flex flex-col items-center justify-center text-amber-600 dark:text-amber-500 py-6 space-y-3"
                                     x-data="{ 
                                        endTime: new Date('{{ $item->maintenance->end_at->toIso8601String() }}'),
                                        timeLeft: '',
                                        init() {
                                            this.updateCountdown();
                                            setInterval(() => this.updateCountdown(), 1000);
                                        },
                                        updateCountdown() {
                                            const now = new Date();
                                            const diff = this.endTime - now;
                                            if (diff <= 0) {
                                                this.timeLeft = 'Ending soon...';
                                                return;
                                            }
                                            const hours = Math.floor(diff / 3600000);
                                            const minutes = Math.floor((diff % 3600000) / 60000);
                                            const seconds = Math.floor((diff % 60000) / 1000);
                                            this.timeLeft = hours + 'h ' + minutes + 'm ' + seconds + 's';
                                        }
                                     }"
                                     x-init="init()">
                                    <svg class="w-10 h-10 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="font-bold text-sm uppercase">Servers Offline</span>
                                    <div class="text-center">
                                        <p class="text-2xl font-mono font-bold" x-text="timeLeft"></p>
                                        <p class="text-xs mt-1">until servers return</p>
                                    </div>
                                    @if($gameTasks->count() > 0)
                                        <div class="mt-2 bg-amber-100 dark:bg-amber-900/30 border border-amber-300 dark:border-amber-700 rounded-lg p-3 w-full">
                                            <p class="text-xs font-bold uppercase tracking-wide mb-1">⚠️ Pending Tasks</p>
                                            <p class="text-xs">You have <strong>{{ $gameTasks->count() }} incomplete task{{ $gameTasks->count() > 1 ? 's' : '' }}</strong> waiting for you when servers return online.</p>
                                        </div>
                                    @endif
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
                                                    <div class="flex flex-col gap-0.5 mt-1" 
                                                         x-data="{ 
                                                            serverTime: '{{ $task->next_due_at->toIso8601String() }}',
                                                            type: '{{ $task->type }}',
                                                            formatTime(dateStr, zone) {
                                                                let date = new Date(dateStr);
                                                                let options = { hour: '2-digit', minute: '2-digit', hour12: false };
                                                                if (this.type === 'weekly') options.weekday = 'short';
                                                                if (this.type === 'loop' || this.type === 'monthly') {
                                                                    options.month = 'short';
                                                                    options.day = 'numeric';
                                                                }
                                                                if (zone === 'UTC') options.timeZone = 'UTC';
                                                                return new Intl.DateTimeFormat('en-US', options).format(date);
                                                            }
                                                         }">
                                                        <span class="text-[10px] font-mono px-1.5 rounded {{ $isOverdue ? 'bg-red-100 text-red-800' : ($isUrgent ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-500') }}" 
                                                              x-text="formatTime(serverTime, 'UTC') + ' UTC'"></span>
                                                        <span class="text-[10px] text-gray-400 font-mono" 
                                                              x-text="formatTime(serverTime, 'local')"></span>
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

            <!-- LIST / ACCORDION VIEW -->
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

                        if ($item->isMaintenance) {
                            $borderColor = 'border-amber-400 dark:border-amber-500 border-l-8';
                            $bgColor = 'bg-amber-50 dark:bg-gray-800'; 
                            $statusIcon = '🔧';
                            $extraClasses = 'animate-pulse'; 
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
                                    <div class="mb-4 bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-200 p-4 rounded-lg"
                                         x-data="{ 
                                            endTime: new Date('{{ $item->maintenance->end_at->toIso8601String() }}'),
                                            timeLeft: '',
                                            init() {
                                                this.updateCountdown();
                                                setInterval(() => this.updateCountdown(), 1000);
                                            },
                                            updateCountdown() {
                                                const now = new Date();
                                                const diff = this.endTime - now;
                                                if (diff <= 0) {
                                                    this.timeLeft = 'Ending soon...';
                                                    return;
                                                }
                                                const hours = Math.floor(diff / 3600000);
                                                const minutes = Math.floor((diff % 3600000) / 60000);
                                                const seconds = Math.floor((diff % 60000) / 1000);
                                                this.timeLeft = hours + 'h ' + minutes + 'm ' + seconds + 's';
                                            }
                                         }"
                                         x-init="init()">
                                        <div class="flex items-start gap-3">
                                            <svg class="w-6 h-6 animate-spin-slow flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <div class="flex-1">
                                                <strong class="text-sm uppercase tracking-wide">Under Maintenance</strong>
                                                <p class="text-xs mt-1">Game servers are currently unavailable.</p>
                                                <p class="text-lg font-mono font-bold mt-2" x-text="timeLeft"></p>
                                                <p class="text-xs opacity-75">remaining until servers return online</p>
                                                @if($gameTasks->count() > 0)
                                                    <div class="mt-3 pt-3 border-t border-amber-200 dark:border-amber-800">
                                                        <p class="text-xs"><strong>⚠️ {{ $gameTasks->count() }} pending task{{ $gameTasks->count() > 1 ? 's' : '' }}</strong> waiting when servers come back online.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($gameTasks->isEmpty() && !$item->isMaintenance)
                                    <div class="text-center py-4 text-green-500 font-medium">All tasks complete.</div>
                                @elseif($gameTasks->isNotEmpty() && !$item->isMaintenance)
                                    <div class="space-y-3">
                                        @foreach($gameTasks as $task)
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
                                                                 x-data="{ 
                                                                    serverTime: '{{ $task->next_due_at->toIso8601String() }}',
                                                                    type: '{{ $task->type }}',
                                                                    formatTime(dateStr, zone) {
                                                                        let date = new Date(dateStr);
                                                                        let options = { hour: '2-digit', minute: '2-digit', hour12: false };
                                                                        if (this.type === 'weekly') options.weekday = 'short';
                                                                        if (this.type === 'loop' || this.type === 'monthly') {
                                                                            options.month = 'short';
                                                                            options.day = 'numeric';
                                                                        }
                                                                        if (zone === 'UTC') options.timeZone = 'UTC';
                                                                        return new Intl.DateTimeFormat('en-US', options).format(date);
                                                                    }
                                                                 }">
                                                                <span class="text-[10px] font-mono font-bold {{ $timeColor }}">
                                                                    {{ $isOverdue ? 'Missed' : 'Due' }} <span x-text="formatTime(serverTime, 'UTC')"></span> UTC
                                                                </span>
                                                                <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500">
                                                                    (<span x-text="formatTime(serverTime, 'local')"></span> Local)
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
            </div>

            <!-- SCHEDULE / CALENDAR VIEW -->
            <div x-show="view === 'schedule'" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                class="space-y-6">

                @for($i = 0; $i < 7; $i++)
                    @php
                        $currentDay = now()->addDays($i);
                        $isToday = $i === 0;
                        
                        $daysTasks = $todoTasks->filter(function($t) use ($currentDay) {
                            return $t->next_due_at && $t->next_due_at->isSameDay($currentDay);
                        });

                        $daysEvents = collect();
                        foreach($games as $g) {
                            foreach($g->events as $e) {
                                if ($e->end_time->isSameDay($currentDay)) {
                                    $e->game_name = $g->name;
                                    $daysEvents->push($e);
                                }
                            }
                        }
                        
                        $daysMaint = collect();
                        foreach($games as $g) {
                            foreach($g->maintenances as $m) {
                                if ($m->start_at->isSameDay($currentDay)) {
                                    $m->game_name = $g->name;
                                    $daysMaint->push($m);
                                }
                            }
                        }

                        $hasContent = $daysTasks->isNotEmpty() || $daysEvents->isNotEmpty() || $daysMaint->isNotEmpty();
                    @endphp

                    @if($hasContent || $isToday)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center min-w-[80px]">
                                <span class="text-xs font-bold uppercase tracking-wider {{ $isToday ? 'text-indigo-500' : 'text-gray-400' }}">
                                    {{ $isToday ? 'Today' : $currentDay->format('D') }}
                                </span>
                                <span class="text-2xl font-black {{ $isToday ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $currentDay->format('d') }}
                                </span>
                            </div>

                            <div class="relative flex flex-col items-center">
                                <div class="w-0.5 h-full bg-gray-200 dark:bg-gray-700"></div>
                                <div class="absolute top-2 w-3 h-3 rounded-full border-2 border-white dark:border-gray-800 {{ $isToday ? 'bg-indigo-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                            </div>

                            <div class="flex-1 pb-8">
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm hover:shadow-md transition">
                                    
                                    @if(!$hasContent)
                                        <p class="text-gray-400 italic text-sm">Nothing scheduled for today.</p>
                                    @else
                                        <div class="space-y-3">
                                            @foreach($daysMaint as $m)
                                                <div class="flex items-center gap-3 text-amber-600 bg-amber-50 dark:bg-amber-900/20 p-2 rounded">
                                                    <span class="text-xs font-bold uppercase px-2 border border-amber-200 rounded bg-white dark:bg-black/20">{{ $m->game_name }}</span>
                                                    <span class="text-sm font-bold">Maintenance Starts</span>
                                                    <span class="text-xs ml-auto font-mono">{{ $m->start_at->format('H:i') }}</span>
                                                </div>
                                            @endforeach

                                            @foreach($daysEvents as $e)
                                                <div class="flex items-center gap-3 text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 p-2 rounded border border-indigo-100 dark:border-indigo-800">
                                                    <span class="text-xs font-bold uppercase px-2 border border-indigo-200 rounded bg-white dark:bg-black/20">{{ $e->game_name }}</span>
                                                    <span class="text-sm font-bold">{{ $e->name }} Ends</span>
                                                    <span class="text-xs ml-auto font-mono">{{ $e->end_time->format('H:i') }}</span>
                                                </div>
                                            @endforeach

                                            @if($daysTasks->isNotEmpty())
                                                <ul class="space-y-2">
                                                    @foreach($daysTasks as $task)
                                                        <li class="flex items-center justify-between group">
                                                            <div class="flex items-center gap-2">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                                <span class="text-xs font-bold text-gray-500 uppercase w-20 truncate">{{ $task->game->name }}</span>
                                                                <span class="text-sm text-gray-700 dark:text-gray-300 {{ $task->isCompleted ? 'line-through opacity-50' : '' }}">{{ $task->name }}</span>
                                                            </div>
                                                            <span class="text-xs text-gray-400 font-mono">{{ $task->next_due_at->format('H:i') }} UTC</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endfor
            </div>

        </div>
    </div>
</div>   
</x-app-layout>