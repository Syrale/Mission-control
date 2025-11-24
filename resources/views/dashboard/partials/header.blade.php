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