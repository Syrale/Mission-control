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
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none transition ease-in-out duration-150">
                    &larr; Back to Mission Control
                </a>
            </div>

            <!-- EDIT FORM -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- TIMEZONE (With Manual Offsets) -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Server Timezone</label>
                                <select name="timezone" 
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    
                                    <optgroup label="Major Timezones">
                                        @foreach(config('timezones.list') as $tz => $label)
                                            <option value="{{ $tz }}" {{ (old('timezone', $game->timezone) == $tz) ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </optgroup>

                                    <optgroup label="Manual Offsets">
                                        @foreach(range(-12, 14) as $offset)
                                            @php 
                                                $sign = $offset < 0 ? '-' : '+';
                                                $val = sprintf("%s%02d:00", $sign, abs($offset));
                                                $label = "UTC $val";
                                            @endphp
                                            <option value="{{ $val }}" {{ (old('timezone', $game->timezone) == $val) ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>

                            <!-- RESET HOUR -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Daily Reset Hour</label>
                                <select name="reset_hour" 
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono">
                                    @foreach(range(0, 23) as $hour)
                                        @php 
                                            $displayTime = Carbon\Carbon::createFromTime($hour, 0)->format('H:00 (g:00 A)');
                                        @endphp
                                        <option value="{{ $hour }}" {{ (old('reset_hour', $game->reset_hour) == $hour) ? 'selected' : '' }}>
                                            {{ $displayTime }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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