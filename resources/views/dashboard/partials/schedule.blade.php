<div x-show="view === 'schedule'" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100"
    class="space-y-6">

    @foreach($calendar as $day)
        @if($day->hasContent || $day->isToday)
            <div class="flex gap-4">
                <div class="flex flex-col items-center min-w-[80px]">
                    <span class="text-xs font-bold uppercase tracking-wider {{ $day->isToday ? 'text-indigo-500' : 'text-gray-400' }}">
                        {{ $day->isToday ? 'Today' : $day->date->format('D') }}
                    </span>
                    <span class="text-2xl font-black {{ $day->isToday ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300' }}">
                        {{ $day->date->format('d') }}
                    </span>
                </div>

                <div class="relative flex flex-col items-center">
                    <div class="w-0.5 h-full bg-gray-200 dark:bg-gray-700"></div>
                    <div class="absolute top-2 w-3 h-3 rounded-full border-2 border-white dark:border-gray-800 {{ $day->isToday ? 'bg-indigo-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                </div>

                <div class="flex-1 pb-8">
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm hover:shadow-md transition">
                        
                        @if(!$day->hasContent)
                            <p class="text-gray-400 italic text-sm">Nothing scheduled.</p>
                        @else
                            <div class="space-y-3">
                                @foreach($day->maintenance as $m)
                                    <div class="flex items-center gap-3 text-amber-600 bg-amber-50 dark:bg-amber-900/20 p-2 rounded">
                                        <span class="text-xs font-bold uppercase px-2 border border-amber-200 rounded bg-white dark:bg-black/20">{{ $m->game_name }}</span>
                                        <span class="text-sm font-bold">Maintenance Starts</span>
                                        <span class="text-xs ml-auto font-mono">{{ $m->start_at->format('H:i') }}</span>
                                    </div>
                                @endforeach

                                @foreach($day->events as $e)
                                    <div class="flex items-center gap-3 text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 p-2 rounded border border-indigo-100 dark:border-indigo-800">
                                        <span class="text-xs font-bold uppercase px-2 border border-indigo-200 rounded bg-white dark:bg-black/20">{{ $e->game_name }}</span>
                                        <span class="text-sm font-bold">{{ $e->name }} Ends</span>
                                        <span class="text-xs ml-auto font-mono">{{ $e->end_time->format('H:i') }}</span>
                                    </div>
                                @endforeach

                                @if($day->tasks->isNotEmpty())
                                    <ul class="space-y-2">
                                        @foreach($day->tasks as $task)
                                            <li class="flex items-center justify-between group">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                    <span class="text-xs font-bold text-gray-500 uppercase w-20 truncate">{{ $task->game->name }}</span>
                                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $task->name }}</span>
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
    @endforeach
</div>