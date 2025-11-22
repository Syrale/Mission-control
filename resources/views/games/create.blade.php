<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- HEADER -->
            <div class="flex flex-col md:flex-row justify-between items-end gap-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
                        🆕 Initialize Protocol
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Add a new game to your tracking database.
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none transition ease-in-out duration-150">
                    &larr; Cancel
                </a>
            </div>

            <!-- FORM WITH ALPINE LOGIC -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700"
                 x-data="{ 
                    tz: '{{ old('timezone', 'UTC') }}', 
                    resetHour: '{{ old('reset_hour', 4) }}',
                    serverTimeNow: '--:--',
                    localResetTime: 'Calculating...',
                    
                    updateCalc() {
                        // 1. Get Current Time
                        let now = new Date();

                        // 2. Calculate 'Server Time' right now
                        // We use native Intl API to convert browser time to selected timezone
                        try {
                            // Handle Manual Offsets (e.g. '+08:00') vs Named Timezones (e.g. 'Asia/Tokyo')
                            // Note: Native JS struggles with manual offsets like '+05:00' in Intl.DateTimeFormat
                            // So we only show accurate preview for Named Timezones.
                            if (this.tz.includes(':')) {
                                this.serverTimeNow = 'UTC ' + this.tz; // Just show label for manual
                                this.localResetTime = 'See Dashboard after save';
                            } else {
                                let options = { timeZone: this.tz, hour: 'numeric', minute: '2-digit', hour12: true };
                                this.serverTimeNow = new Intl.DateTimeFormat('en-US', options).format(now);

                                // 3. Estimate Local Reset Time
                                // This is complex in pure JS, so we simply show the Server Time check
                                // which is usually enough for users to verify they picked the right one.
                                this.localResetTime = 'Reset happens when Server hits ' + this.resetHour + ':00'; 
                            }
                        } catch (e) {
                            this.serverTimeNow = 'Invalid Timezone';
                        }
                    }
                 }"
                 x-init="updateCalc(); setInterval(() => updateCalc(), 1000);"> 
                 <!-- We run updateCalc every second so the clock ticks! -->
                 
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-lg text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('games.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Name & Developer -->
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Game Name</label>
                                <input type="text" name="name" placeholder="e.g. Genshin Impact" required 
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Developer</label>
                                <input type="text" name="developer" placeholder="e.g. Hoyoverse" 
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

                            <!-- HERE IS THE MISSING LIVE PREVIEW BOX -->
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
                                * Check your in-game clock. If "Current Server Time" matches, you are good to go.
                            </p>

                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Notes (Optional)</label>
                            <textarea name="notes" rows="3" placeholder="e.g. Weekly reset is on Monday..." 
                                      class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        <!-- Save Button -->
                        <div class="flex items-center justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                                Save Game to Database
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>