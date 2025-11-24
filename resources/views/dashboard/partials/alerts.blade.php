<div class="space-y-4">
    {{-- 1. MISSED DEADLINES --}}
    @if(isset($missedTasks) && $missedTasks->count() > 0)
        <div x-data="{ open: false, visible: true }" 
            x-show="visible" 
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded shadow-sm mb-6">
            
            <div class="flex justify-between items-center p-4 cursor-pointer hover:bg-red-200/50 dark:hover:bg-red-900/50 transition rounded-t" @click="open = !open">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-bold uppercase tracking-wide text-xs">Missed Deadlines ({{ $missedTasks->count() }})</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold opacity-75" x-text="open ? 'Hide Details' : 'Show Details'"></span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    <div class="h-4 w-px bg-red-300 dark:bg-red-700 mx-1"></div>
                    <button @click.stop="visible = false" title="Dismiss Alert" class="text-red-500 hover:text-red-800 dark:text-red-400 dark:hover:text-white p-1 rounded hover:bg-red-200 dark:hover:bg-red-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
            
            <div x-show="open" x-collapse class="border-t border-red-200 dark:border-red-800/50">
                <div class="p-4 pt-2">
                    <p class="text-xs mb-3 opacity-75 italic">These tasks were not completed before the server reset.</p>
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach($missedTasks as $task)
                            <li class="text-sm flex items-center gap-2 bg-white/50 dark:bg-black/20 p-2 rounded border border-red-200 dark:border-red-800/30">
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

    {{-- 2. EVENT ALERTS --}}
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
                        <div><span class="text-xs font-bold text-red-500 uppercase mr-1">{{ $event->game_name }}</span><span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $event->name }}</span></div>
                        <span class="text-xs font-mono font-bold text-red-600">{{ $event->end_time->diffForHumans(null, true, true) }} left</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($alerts['urgent']->isNotEmpty())
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
            <p class="font-bold text-amber-600 dark:text-amber-400 flex items-center uppercase tracking-wide text-xs mb-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Ending Today
            </p>
            <div class="space-y-2">
                @foreach($alerts['urgent'] as $event)
                    <div class="flex justify-between items-center bg-white/50 dark:bg-amber-900/30 p-2 rounded border border-amber-100 dark:border-amber-800/50">
                        <div><span class="text-xs font-bold text-gray-500 uppercase mr-1">{{ $event->game_name }}</span><span class="text-sm text-gray-800 dark:text-gray-200">{{ $event->name }}</span></div>
                        <span class="text-xs font-mono text-amber-600 dark:text-amber-400">{{ $event->end_time->diffForHumans(null, true, true) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($alerts['upcoming']->isNotEmpty())
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <p class="font-bold text-blue-600 dark:text-blue-400 flex items-center uppercase tracking-wide text-xs mb-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Ending Soon (3 Days)
            </p>
            <div class="space-y-1">
                @foreach($alerts['upcoming'] as $event)
                    <div class="flex justify-between items-center px-2 py-1">
                        <span class="text-sm text-gray-600 dark:text-gray-400"><strong class="text-gray-800 dark:text-gray-300">{{ $event->game_name }}:</strong> {{ $event->name }}</span>
                        <span class="text-xs text-blue-500">{{ $event->end_time->format('D, M j') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>