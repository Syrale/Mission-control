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