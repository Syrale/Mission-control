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