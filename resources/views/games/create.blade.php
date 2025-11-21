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

            <!-- FORM -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- IMPROVED TIMEZONE SELECTOR -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Server Timezone</label>
                                <select name="timezone" 
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    
                                    <!-- 1. Standard Named Timezones (from config) -->
                                    <optgroup label="Major Timezones">
                                        @foreach(config('timezones.list') as $tz => $label)
                                            <option value="{{ $tz }}" {{ (old('timezone', 'UTC') == $tz) ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </optgroup>

                                    <!-- 2. Manual Offsets (For lazy people like us!) -->
                                    <optgroup label="Manual Offsets">
                                        @foreach(range(-12, 14) as $offset)
                                            @php 
                                                // Format: "+05:00" or "-07:00"
                                                $sign = $offset < 0 ? '-' : '+';
                                                $abs = abs($offset);
                                                $val = sprintf("%s%02d:00", $sign, $abs); // e.g. -07:00
                                                $label = "UTC $val";
                                            @endphp
                                            <option value="{{ $val }}" {{ (old('timezone') == $val) ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </optgroup>

                                </select>
                                <p class="text-xs text-gray-500 mt-1">If you don't know the city, just pick "UTC -07:00".</p>
                            </div>

                            <!-- Reset Hour -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Daily Reset Hour</label>
                                <select name="reset_hour" 
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono">
                                    @foreach(range(0, 23) as $hour)
                                        @php 
                                            $displayTime = Carbon\Carbon::createFromTime($hour, 0)->format('H:00 (g:00 A)');
                                        @endphp
                                        <option value="{{ $hour }}" {{ (old('reset_hour', 4) == $hour) ? 'selected' : '' }}>
                                            {{ $displayTime }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Server time, not local.</p>
                            </div>
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