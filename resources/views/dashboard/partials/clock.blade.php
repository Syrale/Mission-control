<div class="grid grid-cols-1 md:grid-cols-2 gap-4" 
     x-data='{ 
        now: new Date(),
        timezone: localStorage.getItem("dash_timezone") || "UTC",
        timezones: @json(config("timezones.list") ?? ["UTC" => "UTC"]),
        init() { 
            try { new Intl.DateTimeFormat("en-US", { timeZone: this.timezone }); } 
            catch(e) { console.log("Using manual offset"); }
            setInterval(() => { this.now = new Date(); }, 1000); 
        },
        updateTimezone(e) {
            this.timezone = e.target.value;
            localStorage.setItem("dash_timezone", this.timezone);
        },
        getServerTime() {
            if (this.timezone.startsWith("+") || this.timezone.startsWith("-")) {
                const offsetHours = parseInt(this.timezone.split(":")[0]);
                const utc = this.now.getTime() + (this.now.getTimezoneOffset() * 60000);
                return new Date(utc + (3600000 * offsetHours)).toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit", hour12: false });
            }
            try {
                return new Intl.DateTimeFormat("en-US", { timeZone: this.timezone, hour: "2-digit", minute: "2-digit", hour12: false }).format(this.now);
            } catch (e) { return "Invalid TZ"; }
        }
     }'
     x-init="init()">
    {{-- Server Time --}}
    <div class="bg-indigo-900 text-white overflow-hidden shadow-sm sm:rounded-lg border border-indigo-700 p-6 flex flex-col justify-between relative h-40">
        <div class="absolute top-0 right-0 p-2 opacity-10 text-6xl font-black select-none">UTC</div>
        <div class="z-10">
            <div class="flex justify-between items-start">
                <div class="text-xs font-bold text-indigo-300 uppercase tracking-widest mb-1">Server Time</div>
                <select x-model="timezone" @change="updateTimezone" class="bg-indigo-800 border-none text-[10px] uppercase font-bold text-white rounded focus:ring-0 cursor-pointer py-0.5 pl-2 pr-6 h-6">
                    <optgroup label="Saved"><template x-for="(label, key) in timezones"><option :value="isNaN(key) ? key : label" x-text="label" :selected="(isNaN(key) ? key : label) === timezone" class="text-black"></option></template></optgroup>
                    <optgroup label="Offsets"><template x-for="i in 27"><option :value="(i-13 > 0 ? '+' : '') + (i-13) + ':00'" x-text="'UTC ' + (i-13 > 0 ? '+' : '') + (i-13) + ':00'" :selected="((i-13 > 0 ? '+' : '') + (i-13) + ':00') === timezone" class="text-black"></option></template></optgroup>
                </select>
            </div>
            <div class="text-4xl font-mono font-bold tracking-tighter mt-2"><span x-text="getServerTime()"></span></div>
            <div class="text-sm text-indigo-200 mt-1"><span x-text="timezone"></span></div>
        </div>
    </div>
    {{-- Local Time --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col justify-between h-40">
        <div>
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Your Local Time</div>
            <div class="text-4xl font-mono font-bold text-gray-900 dark:text-white tracking-tighter mt-2"><span x-text="now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false})"></span></div>
            <div class="text-sm text-gray-500 mt-1"><span x-text="now.toLocaleDateString([], {weekday: 'long', day: 'numeric'})"></span></div>
        </div>
        <div class="flex justify-end"><svg class="w-6 h-6 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
    </div>
</div>