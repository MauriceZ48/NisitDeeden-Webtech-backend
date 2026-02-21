{{-- Top Navbar (no sidebar) --}}
<header class="sticky top-0 z-50 border-b border-slate-200/70 bg-white backdrop-blur shadow-sm">
    <div class="container mx-auto px-4">
        <div class="flex h-20 items-center justify-between">

            {{-- Brand --}}
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-2xl bg-primary/10 flex items-center justify-center border border-primary/15">
                    <span class="text-primary font-extrabold">N</span>
                </div>
                <div class="leading-tight">
                    <div class="text-lg font-extrabold text-slate-900">NisitDeeden</div>
                    <div class="text-xs text-slate-500 -mt-0.5">Student Excellence Portal</div>
                </div>
            </a>

            @php
                use App\Enums\UserRole;

                $role = auth()->check() ? auth()->user()->role : null; // enum or null
                $isAdmin = $role === UserRole::ADMIN;
            @endphp

            {{-- Center links --}}
            <nav class="hidden md:flex items-center gap-1">
                @if($isAdmin)
                    <a href="{{ route('applications.index') }}"
                       class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900">
                        Applications
                    </a>
                    <a href="{{ route('users.index') }}"
                       class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900">
                        Users
                    </a>
                    <a href="{{ route('rounds.index') }}"
                       class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900">
                        Rounds
                    </a>
                @endif
            </nav>


            {{-- Right actions --}}
            <div class="flex items-center gap-2">
                @guest
                    <a href="{{ url('login') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30">
                        Login
                    </a>

                    <a href="{{ url('register') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary/20">
                        Register
                    </a>
                @endguest

                @auth
                    {{-- Mobile quick links --}}
                    <div class="md:hidden flex items-center gap-2">
                        <a href="{{ route('applications.index') }}"
                           class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Applications
                        </a>
                        <a href="{{ route('users.index') }}"
                           class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Users
                        </a>
                        <a href="{{ route('rounds.index') }}"
                           class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Rounds
                        </a>
                        <a href="{{ route('categories.index') }}"
                           class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Category
                        </a>
                    </div>

                    {{-- User dropdown --}}
                    <div class="relative" x-data="{ open:false }">
                        <button type="button"
                                @click="open=!open"
                                @keydown.escape.window="open=false"
                                class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary/20">
                            @php
                                $name = auth()->user()->name ?? 'User';
                                $initials = collect(explode(' ', trim($name)))
                                    ->filter()->take(2)->map(fn($p) => mb_substr($p,0,1))->join('');
                            @endphp

                            <div class="h-9 w-9 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-xs font-extrabold text-slate-700">
                                {{ $initials ?: 'U' }}
                            </div>

                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-extrabold text-slate-900 leading-4">
                                    {{ auth()->user()->name }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ auth()->user()->email }}
                                </div>
                            </div>

                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition
                             @click.outside="open=false"
                             class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
                            <div class="px-4 py-3 border-b border-slate-100">
                                <div class="text-sm font-extrabold text-slate-900">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
                            </div>

{{--                            <a href="{{ route('applications.index') }}"--}}
{{--                               class="block px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">--}}
{{--                                Applications--}}
{{--                            </a>--}}
{{--                            <a href="{{ route('users.index') }}"--}}
{{--                               class="block px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">--}}
{{--                                Users--}}
{{--                            </a>--}}

                            <div class="border-t border-slate-100"></div>

                            <form method="POST" action="{{ route('logout') }}" class="p-2">
                                @csrf
                                <button type="submit"
                                        class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>
