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
                    resetHour: {{ old('reset_hour', 4) }},
                    serverTimeDisplay: '--:--',
                    localResetDisplay: '--:--',
                    
                    init() {
                        this.calculate();
                        setInterval(() => this.calculate(), 1000);
                    },

                    calculate() {
                        const now = new Date();

                        // 1. GET SERVER TIME
                        let serverTimeStr;
                        try {
                            serverTimeStr = now.toLocaleTimeString('en-US', { timeZone: this.tz, hour12: false, hour: '2-digit', minute: '2-digit' });
                        } catch (e) {
                            this.serverTimeDisplay = 'Invalid Timezone';
                            this.localResetDisplay = '---';
                            return;
                        }
                        this.serverTimeDisplay = serverTimeStr;

                        // 2. CALCULATE LOCAL RESET TIME
                        const serverHour = parseInt(serverTimeStr.split(':')[0]);
                        const localHour = now.getHours();
                        
                        let diff = localHour - serverHour;
                        let calculatedLocalHour = parseInt(this.resetHour) + diff;
                        
                        // Normalize 24h format
                        if (calculatedLocalHour >= 24) calculatedLocalHour -= 24;
                        if (calculatedLocalHour < 0) calculatedLocalHour += 24;

                        // Format nicely
                        const ampm = calculatedLocalHour >= 12 ? 'PM' : 'AM';
                        const displayHour = calculatedLocalHour % 12 || 12;
                        
                        this.localResetDisplay = displayHour + ':00 ' + ampm;
                    }
                 }"
                 x-init="init()">
                 
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
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Genshin Impact" required 
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Developer</label>
                                <input type="text" name="developer" value="{{ old('developer') }}" placeholder="e.g. Hoyoverse" 
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
                                    <select name="timezone" x-model="tz" @change="calculate()"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        
                                        <optgroup label="Major Timezones">
                                            @foreach(config('timezones.list') as $tzKey => $label)
                                                <option value="{{ $tzKey }}">{{ $label }}</option>
                                            @endforeach
                                        </optgroup>

                                        <optgroup label="Manual Offsets">
                                            @foreach(range(-12, 14) as $offset)
                                                @php 
                                                    $sign = $offset < 0 ? '' : '+';
                                                    $val = sprintf("%s%02d:00", $sign, $offset);
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
                                    <select name="reset_hour" x-model="resetHour" @change="calculate()"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">
                                        @foreach(range(0, 23) as $h)
                                            @php $display = Carbon\Carbon::createFromTime($h, 0)->format('H:00 (g:00 A)'); @endphp
                                            <option value="{{ $h }}">{{ $display }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- LIVE PREVIEW BOX -->
                            <div class="mt-4 flex flex-col md:flex-row gap-4">
                                
                                <!-- Server Status -->
                                <div class="flex-1 bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Game Server Time</p>
                                        <p class="text-xl font-mono font-bold text-indigo-600 dark:text-indigo-400" x-text="serverTimeDisplay">--:--</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Reset Set To</p>
                                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                            <span x-text="resetHour + ':00'"></span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Conversion Arrow -->
                                <div class="flex items-center justify-center text-gray-400">
                                    <svg class="w-6 h-6 md:rotate-0 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </div>

                                <!-- Local Status -->
                                <div class="flex-1 bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-700 flex flex-col justify-center">
                                    <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Your Local Time</p>
                                    <p class="text-xl font-mono font-bold text-green-600 dark:text-green-400" x-text="localResetDisplay">--:--</p>
                                    <p class="text-[10px] text-gray-400 leading-tight mt-1">When server hits reset, it will be this time for you.</p>
                                </div>

                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Notes (Optional)</label>
                            <textarea name="notes" rows="3" placeholder="e.g. Weekly reset is on Monday..." 
                                      class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
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