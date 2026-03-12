{{-- Top Navbar (no sidebar) --}}
<header class="sticky top-0 z-50 border-b border-slate-200/70 bg-white backdrop-blur shadow-sm">
    <div class="container mx-auto px-4">
        <div class="flex h-16 md:h-20 items-center justify-between gap-4">

            <div  class="flex items-center gap-2 md:gap-3 flex-shrink-0">
                <div class="h-9 w-9 rounded-2xl bg-primary/10 flex items-center justify-center border border-primary/15 flex-shrink-0">
                    <span class="text-primary font-extrabold">N</span>
                </div>
                <div class="leading-tight flex-shrink-0">
                    <div class="text-lg font-extrabold text-slate-900 whitespace-nowrap">NisitDeeden</div>
                    <div class="text-[10px] md:text-xs text-slate-500 -mt-0.5 hidden sm:block whitespace-nowrap">ระบบพิจารณารางวัลนิสิตดีเด่น</div>
                </div>
            </div>

            @php
                use App\Enums\UserRole;
                use App\Enums\Domain;

                $user = auth()->check() ? auth()->user() : null;
                $isAdmin = $user && $user->role === UserRole::ADMIN;

                // 🌟 เช็คว่าเป็น "แอดมินวิทยาเขต" (ไม่ใช่ส่วนกลาง) ใช่หรือไม่
                $isCampusAdmin = $isAdmin && $user->domain !== Domain::ALL;
            @endphp

            {{-- Center links (แสดงเฉพาะบนจอคอมพิวเตอร์ และแสดงเฉพาะแอดมินวิทยาเขต) --}}
            <nav class="hidden md:flex items-center gap-1">
                {{-- 🌟 เปลี่ยนจาก $isAdmin เป็น $isCampusAdmin --}}
                @if($isCampusAdmin)
                    <a href="{{ route('applications.index') }}"
                       class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition whitespace-nowrap">
                        ใบสมัคร
                    </a>
                    <a href="{{ route('users.index') }}"
                       class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition whitespace-nowrap">
                        ผู้ใช้งาน
                    </a>
                    <a href="{{ route('rounds.index') }}"
                       class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition whitespace-nowrap">
                        รอบการรับสมัคร
                    </a>
                    <a href="{{ route('categories.index') }}"
                       class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition whitespace-nowrap">
                        ประเภทรางวัล
                    </a>
                @endif
            </nav>

            {{-- Right actions --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                @guest
                    <a href="{{ url('login') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30 whitespace-nowrap">
                        เข้าสู่ระบบ
                    </a>
                    <a href="{{ url('register') }}"
                       class="hidden sm:inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary/20 whitespace-nowrap">
                        ลงทะเบียน
                    </a>
                @endguest

                @auth
                    {{-- User dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button type="button"
                                @click="open = !open"
                                @keydown.escape.window="open = false"
                                class="inline-flex items-center gap-2 md:gap-3 rounded-2xl border border-slate-200 bg-white px-2 py-1.5 md:px-3 md:py-2 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                            @php
                                $name = auth()->user()->name ?? 'User';
                                $initials = collect(explode(' ', trim($name)))
                                    ->filter()->take(2)->map(fn($p) => mb_substr($p,0,1))->join('');
                            @endphp

                            <div class="h-8 w-8 md:h-9 md:w-9 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-xs font-extrabold text-slate-700 flex-shrink-0">
                                {{ $initials ?: 'U' }}
                            </div>

                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-extrabold text-slate-900 leading-4 truncate max-w-[120px]">
                                    {{ auth()->user()->name }}
                                </div>
                                <div class="text-[10px] text-slate-500 truncate max-w-[120px]">
                                    {{ auth()->user()->email }}
                                </div>
                            </div>

                            <svg class="h-4 w-4 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-cloak
                             x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             @click.outside="open = false"
                             class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg z-50">

                            {{-- ข้อมูลโปรไฟล์ส่วนหัวใน Dropdown --}}
                            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                                <div class="text-sm font-extrabold text-slate-900 truncate">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</div>
                            </div>

                            @if($isCampusAdmin)
                                <div class="block md:hidden border-b border-slate-100 py-1">
                                    <a href="{{ route('applications.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition whitespace-nowrap">ใบสมัคร</a>
                                    <a href="{{ route('users.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition whitespace-nowrap">ผู้ใช้งาน</a>
                                    <a href="{{ route('rounds.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition whitespace-nowrap">รอบการรับสมัคร</a>
                                    <a href="{{ route('categories.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition whitespace-nowrap">ประเภทรางวัล</a>
                                </div>
                            @endif

                            {{-- ปุ่มออกจากระบบ --}}
                            <form method="POST" action="{{ route('logout') }}" class="p-2 bg-white">
                                @csrf
                                <button type="submit"
                                        class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20 transition-colors whitespace-nowrap">
                                    ออกจากระบบ
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>

        </div>
    </div>
</header>
