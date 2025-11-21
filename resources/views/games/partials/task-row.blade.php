<div class="flex items-center justify-between group p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition">
    <div class="flex items-center">
        <!-- Checkbox Form -->
        <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="flex items-center">
            @csrf 
            @method('PATCH')
            <input type="checkbox" 
                   onchange="this.form.submit()" 
                   {{ $task->is_completed ? 'checked' : '' }}
                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer h-5 w-5">
        </form>
        
        <!-- Task Name -->
        <div class="ml-3 text-sm font-medium {{ $task->is_completed ? 'text-gray-400 line-through' : 'text-gray-700 dark:text-gray-200' }}">
            {{ $task->name }}
            
            <!-- Loop Details (if it's a loop task) -->
            @if($task->type === 'loop')
                <span class="text-[10px] text-indigo-400 bg-indigo-900/20 px-1 rounded ml-2 border border-indigo-500/30">
                    Every {{ $task->repeat_days }} days
                </span>
            @endif
        </div>
    </div>

    <!-- Delete Button (Only shows on hover) -->
    <form action="{{ route('tasks.destroy', $task) }}" method="POST" 
          class="opacity-0 group-hover:opacity-100 transition-opacity"
          onsubmit="return confirm('Delete this task?');">
        @csrf 
        @method('DELETE')
        <button type="submit" class="text-gray-400 hover:text-red-500 p-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        </button>
    </form>
</div>