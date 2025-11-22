<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- HEADER -->
            <div class="flex flex-col md:flex-row justify-between items-end gap-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
                        ⚙️ Configuration
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Editing protocols for <span class="font-semibold text-indigo-500">{{ $game->name }}</span>
                    </p>
                </div>
                <a href="{{ route('games.show', $game) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none transition ease-in-out duration-150">
                    &larr; Back to Mission Control
                </a>
            </div>

            <!-- EDIT FORM WITH LIVE PREVIEW LOGIC -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700"
                 x-data="{ 
                    tz: '{{ old('timezone', $game->timezone) }}', 
                    resetHour: '{{ old('reset_hour', $game->reset_hour) }}',
                    serverTimeNow: '--:--',
                    localResetTime: 'Calculating...',
                    
                    updateCalc() {
                        let now = new Date();
                        try {
                            if (this.tz.includes(':')) {
                                this.serverTimeNow = 'UTC ' + this.tz; 
                                this.localResetTime = 'See Dashboard after save';
                            } else {
                                let options = { timeZone: this.tz, hour: 'numeric', minute: '2-digit', hour12: true };
                                this.serverTimeNow = new Intl.DateTimeFormat('en-US', options).format(now);
                                this.localResetTime = 'Reset happens when Server hits ' + this.resetHour + ':00'; 
                            }
                        } catch (e) {
                            this.serverTimeNow = 'Invalid Timezone';
                        }
                    }
                 }"
                 x-init="updateCalc(); setInterval(() => updateCalc(), 1000);">
                 
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form action="{{ route('games.update', $game) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Game Name</label>
                                <input type="text" name="name" value="{{ old('name', $game->name) }}" required 
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Developer</label>
                                <input type="text" name="developer" value="{{ old('developer', $game->developer) }}" 
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <!-- TIMEZONE SECTION -->
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg border border-indigo-100 dark:border-indigo-800">
                            <h3 class="text-indigo-700 dark:text-indigo-300 font-bold text-sm mb-3 uppercase">Time Configuration</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Timezone Selector -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Server Timezone</label>
                                    <select name="timezone" x-model="tz" @change="updateCalc()"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        
                                        <optgroup label="Major Timezones">
                                            @foreach(config('timezones.list') as $tz => $label)
                                                <option value="{{ $tz }}">{{ $label }}</option>
                                            @endforeach
                                        </optgroup>

                                        <optgroup label="Manual Offsets">
                                            @foreach(range(-12, 14) as $offset)
                                                @php 
                                                    $sign = $offset < 0 ? '-' : '+';
                                                    $val = sprintf("%s%02d:00", $sign, abs($offset));
                                                    $label = "UTC $val";
                                                @endphp
                                                <option value="{{ $val }}">{{ $label }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>

                                <!-- Reset Hour -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Daily Reset (Server Time)</label>
                                    <select name="reset_hour" x-model="resetHour" @change="updateCalc()"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">
                                        @foreach(range(0, 23) as $h)
                                            @php $display = Carbon\Carbon::createFromTime($h, 0)->format('H:00 (g:00 A)'); @endphp
                                            <option value="{{ $h }}">{{ $display }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- LIVE PREVIEW BOX -->
                            <div class="mt-4 p-3 bg-white dark:bg-gray-800 rounded border border-indigo-200 dark:border-indigo-700 shadow-sm flex items-center justify-between">
                                
                                <!-- Server Time Check -->
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Current Server Time</p>
                                    <p class="text-xl font-mono font-bold text-indigo-600 dark:text-indigo-400" x-text="serverTimeNow">
                                        --:--
                                    </p>
                                </div>

                                <!-- Arrow -->
                                <div class="text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </div>

                                <!-- Reset Confirmation -->
                                <div class="text-right">
                                    <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Scheduled Reset</p>
                                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                        <span x-text="resetHour"></span>:00 Server Time
                                    </p>
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-2 text-center">
                                * Verify this matches your in-game clock.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea name="notes" rows="3" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $game->notes) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                                Update Game Configuration
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- DANGER ZONE -->
            <div class="bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-lg overflow-hidden">
                <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-red-700 dark:text-red-400">Decommission Game</h3>
                        <p class="text-sm text-red-600/80 dark:text-red-400/70">
                            Permanently delete this game and all associated tasks. This action cannot be undone.
                        </p>
                    </div>
                    
                    <form action="{{ route('games.destroy', $game) }}" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to delete {{ $game->name }}?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="whitespace-nowrap px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase rounded shadow transition focus:outline-none focus:ring-2 focus:ring-red-500">
                            Delete Game
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>