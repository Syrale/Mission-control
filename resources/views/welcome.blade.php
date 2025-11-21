<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Mission Control') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <!-- Keep the default Tailwind styles you had -->
            <style>
                /* Minimal Reset just in case */
                *,:after,:before{box-sizing:border-box;border:0 solid;margin:0;padding:0}
                body{font-family:'Instrument Sans',sans-serif; antialiased;}
            </style>
            <!-- (I am keeping the styles simpler here, assuming you have Tailwind running via Vite. 
                 If you are using the raw CSS file you pasted, keep that huge <style> block in the head!) -->
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                    darkMode: 'media',
                    theme: {
                        extend: {
                            colors: {
                                gray: { 850: '#1f2937', 950: '#030712' }
                            }
                        }
                    }
                }
            </script>
        @endif
    </head>
    <body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col transition-colors duration-300">
        
        <!-- NAV HEADER -->
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 flex justify-between items-center">
            <div class="font-bold text-xl tracking-tighter flex items-center gap-2">
                <span>🚀</span> Mission Control
            </div>

            @if (Route::has('login'))
                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg transition">
                            Enter Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium transition">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg transition shadow-lg shadow-indigo-500/20">
                                Get Started
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <!-- MAIN CARD -->
        <div class="flex w-full max-w-[335px] lg:max-w-4xl flex-col-reverse lg:flex-row shadow-2xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 dark:ring-white/10">
            
            <!-- LEFT COLUMN: TEXT -->
            <div class="flex-1 p-8 lg:p-12 bg-white dark:bg-gray-900 flex flex-col justify-center">
                <h1 class="text-3xl lg:text-4xl font-bold tracking-tight mb-4 text-gray-900 dark:text-white">
                    Sync your gaming life.
                </h1>
                <p class="text-lg text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                    Stop guessing when the server resets. Track dailies, weeklies, and events for all your gacha and MMO games in <span class="text-indigo-500 font-bold">your local time</span>.
                </p>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            🌍
                        </div>
                        <span>Auto-converts Server Time to Local Time</span>
                    </li>
                    <li class="flex items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            ✅
                        </div>
                        <span>Daily & Weekly Task Checklists</span>
                    </li>
                    <li class="flex items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            🔔
                        </div>
                        <span>Never miss a Weekly Reset again</span>
                    </li>
                </ul>

                <div>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 text-base font-bold text-white bg-gray-900 dark:bg-white dark:text-gray-900 rounded-lg hover:bg-gray-700 dark:hover:bg-gray-200 transition duration-200 w-full sm:w-auto">
                        Create Free Account &rarr;
                    </a>
                </div>
            </div>

            <!-- RIGHT COLUMN: VISUAL -->
            <div class="bg-indigo-600 dark:bg-indigo-900 relative w-full lg:w-[400px] flex items-center justify-center p-10 overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
                
                <!-- Hero SVG: Radar/Clock Concept -->
                <svg class="w-64 h-64 text-white/90 drop-shadow-2xl animate-[pulse_4s_ease-in-out_infinite]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 2V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 20V22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12H4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20 12H22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="3" class="text-indigo-300" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 4" />
                </svg>
                
                <!-- Decorative Circles -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[300px] h-[300px] border border-white/20 rounded-full"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] border border-white/10 rounded-full"></div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="mt-12 text-center text-xs text-gray-400 dark:text-gray-600">
            <p>&copy; {{ date('Y') }} Mission Control. All systems nominal.</p>
            <p class="mt-2">Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</p>
        </div>

    </body>
</html>