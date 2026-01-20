@extends('layouts.main')

@section('content')
    @php
        $users = collect([
        (object)[
            'id' => 1,
            'name' => 'John Doe',
            'student_staff_id' => '6412345678',
            'department' => 'Computer Engineering',
            'faculty' => 'Engineering',
            'profile_picture' => null,
        ],
        (object)[
            'id' => 2,
            'name' => 'Jane Smith',
            'student_staff_id' => '6311122233',
            'department' => 'Information Technology',
            'faculty' => 'Engineering',
            'profile_picture' => null,
        ],
        (object)[
            'id' => 3,
            'name' => 'Anan Chaiyawat',
            'student_staff_id' => '6519988776',
            'department' => 'Computer Science',
            'faculty' => 'Science',
            'profile_picture' => null,
        ],
        (object)[
            'id' => 4,
            'name' => 'Suda Kittisak',
            'student_staff_id' => '6214455667',
            'department' => 'Electrical Engineering',
            'faculty' => 'Engineering',
            'profile_picture' => null,
        ],
        (object)[
            'id' => 5,
            'name' => 'Pimchanok Wong',
            'student_staff_id' => '6612233445',
            'department' => 'Business Administration',
            'faculty' => 'Business',
            'profile_picture' => null,
        ],
    ]);
    @endphp

    <section class="bg-background">
        <div class="container mx-auto w-[90%] py-8 space-y-6">

            {{-- Header --}}
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900">Users</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage users by faculty and department.</p>
                </div>

                <a href=""
                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <span class="text-lg leading-none">+</span>
                    New User
                </a>
            </div>

            {{-- Summary --}}
            @php
                $totalUsers = isset($users) ? $users->count() : 0;
                $facultyCount = isset($users) ? $users->pluck('faculty')->filter()->unique()->count() : 0;
                $deptCount = isset($users) ? $users->pluck('department')->filter()->unique()->count() : 0;
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Users</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalUsers }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Faculties</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $facultyCount }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Departments</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $deptCount }}</p>
                </div>
            </div>

            {{-- Master-Detail Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- LEFT: List --}}
                <div class="lg:col-span-8">
                    <div x-data="usersMasterDetail()"
                         class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                        {{-- Toolbar --}}
                        <div class="p-4 border-b border-slate-200">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div class="flex gap-2 items-center w-full md:w-auto">
                                    <div class="relative w-full md:w-[360px]">
                                        <input
                                            x-model="query"
                                            type="text"
                                            placeholder="Search name or ID..."
                                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary focus:ring-primary/20"
                                        />
                                        <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M8.5 3a5.5 5.5 0 104.27 9.01l2.61 2.61a1 1 0 001.42-1.42l-2.61-2.61A5.5 5.5 0 008.5 3zm-3.5 5.5a3.5 3.5 0 117 0 3.5 3.5 0 01-7 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <button type="button"
                                            @click="openFilter = !openFilter"
                                            class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                        Advanced Filter
                                    </button>

                                    <a href=""
                                       class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                        Export CSV
                                    </a>
                                </div>

                                {{-- Bulk actions (optional) --}}
                                <div class="flex items-center gap-2">
                                    <template x-if="selectedIds.length">
                                        <div class="inline-flex items-center gap-2 rounded-lg bg-slate-50 border border-slate-200 px-3 py-2">
                                        <span class="text-sm text-slate-700">
                                            <span class="font-semibold" x-text="selectedIds.length"></span> selected
                                        </span>

                                            <button type="button"
                                                    class="text-sm font-semibold text-slate-700 hover:text-slate-900"
                                                    @click="bulkClear()">
                                                Clear
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Filter panel (Faculty/Department only) --}}
                            <div x-show="openFilter" x-cloak class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Faculty</label>
                                        <select x-model="faculty"
                                                class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-primary focus:ring-primary/20">
                                            <option value="">All</option>
                                            <template x-for="f in faculties" :key="f">
                                                <option :value="f" x-text="f"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Department</label>
                                        <select x-model="department"
                                                class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-primary focus:ring-primary/20">
                                            <option value="">All</option>
                                            <template x-for="d in departments" :key="d">
                                                <option :value="d" x-text="d"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center gap-2">
                                    <button type="button"
                                            @click="applyFilters()"
                                            class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        Apply
                                    </button>
                                    <button type="button"
                                            @click="resetFilters()"
                                            class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    <th class="px-4 py-3 w-10">
                                        <input type="checkbox"
                                               class="rounded border-slate-300 text-primary focus:ring-primary/20"
                                               @change="toggleAll($event.target.checked)">
                                    </th>
                                    <th class="px-4 py-3">User</th>
                                    <th class="px-4 py-3">Student/Staff ID</th>
                                    <th class="px-4 py-3">Department</th>
                                    <th class="px-4 py-3">Faculty</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-200">
                                @foreach($users as $u)
                                    @php
                                        // Expecting:
                                        // $u->id (internal), $u->name, $u->student_staff_id, $u->department, $u->faculty, $u->profile_picture (url or path)
                                        $pic = $u->profile_picture ?? null;
                                        $initials = collect(explode(' ', trim($u->name ?? 'User')))
                                            ->filter()
                                            ->take(2)
                                            ->map(fn($p) => strtoupper(mb_substr($p, 0, 1)))
                                            ->join('');
                                    @endphp

                                    <tr
                                        class="hover:bg-slate-50 cursor-pointer"
                                        :class="selectedUserId === '{{ $u->id }}' ? 'bg-slate-50' : ''"
                                        data-name="{{ strtolower($u->name ?? '') }}"
                                        data-staffid="{{ strtolower($u->student_staff_id ?? '') }}"
                                        data-faculty="{{ strtolower($u->faculty ?? '') }}"
                                        data-department="{{ strtolower($u->department ?? '') }}"
                                        @click="selectUser({
                                        id: '{{ $u->id }}',
                                        name: @js($u->name),
                                        staff_id: @js($u->student_staff_id),
                                        faculty: @js($u->faculty),
                                        department: @js($u->department),
                                        picture: @js($pic),
                                        initials: @js($initials),
                                    })"
                                        x-show="rowVisible($el)"
                                        x-cloak
                                    >
                                        <td class="px-4 py-4" @click.stop>
                                            <input type="checkbox"
                                                   class="rounded border-slate-300 text-primary focus:ring-primary/20"
                                                   :checked="selectedIds.includes('{{ $u->id }}')"
                                                   @change="toggleOne('{{ $u->id }}', $event.target.checked)">
                                        </td>

                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($pic)
                                                    <img src="{{ $pic }}" alt="Profile" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                                                @else
                                                    <div class="h-9 w-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                                        {{ $initials ?: 'U' }}
                                                    </div>
                                                @endif

                                                <div>
                                                    <div class="font-semibold text-slate-900">{{ $u->name }}</div>
                                                    <div class="text-xs text-slate-500">
                                                        {{ $u->department }} • {{ $u->faculty }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-4 py-4 text-sm text-slate-700">
                                            {{ $u->student_staff_id }}
                                        </td>

                                        <td class="px-4 py-4 text-sm text-slate-700">
                                            {{ $u->department }}
                                        </td>

                                        <td class="px-4 py-4 text-sm text-slate-700">
                                            {{ $u->faculty }}
                                        </td>

                                        <td class="px-4 py-4 text-right" @click.stop>
                                            <div class="inline-flex items-center gap-2">
                                                <a href=""
                                                   class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                                    Edit
                                                </a>

                                                <div class="relative" x-data="{open:false}">
                                                    <button type="button"
                                                            @click="open = !open"
                                                            class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                                        ⋯
                                                    </button>

                                                    <div x-show="open" x-cloak @click.outside="open=false"
                                                         class="absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden z-20">
                                                        <a href=""
                                                           class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                                            Edit user
                                                        </a>
                                                        <button type="button"
                                                                class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50"
                                                                @click="open=false; selectUser({
                                                                id: '{{ $u->id }}',
                                                                name: @js($u->name),
                                                                staff_id: @js($u->student_staff_id),
                                                                faculty: @js($u->faculty),
                                                                department: @js($u->department),
                                                                picture: @js($pic),
                                                                initials: @js($initials),
                                                            })">
                                                            View details
                                                        </button>
                                                        <div class="h-px bg-slate-200"></div>
                                                        <form method="POST" action=""
                                                              onsubmit="return confirm('Delete this user?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                                Delete user
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Footer --}}
                        <div class="p-4 border-t border-slate-200 flex items-center justify-between text-sm text-slate-500">
                        <span>
                            Showing {{ method_exists($users, 'firstItem') ? $users->firstItem() : 1 }}
                            to {{ method_exists($users, 'lastItem') ? $users->lastItem() : ($users->count() ?? 0) }}
                            of {{ method_exists($users, 'total') ? $users->total() : ($users->count() ?? 0) }} users
                        </span>

                            @if(method_exists($users, 'links'))
                                <div class="text-slate-700">
                                    {{ $users->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Details panel --}}
                <div class="lg:col-span-4">
                    <div
                        x-data="usersMasterDetail()"
                        class="hidden lg:block">
                        {{-- This is just a placeholder to avoid double Alpine scopes --}}
                    </div>

                    <div class="sticky top-6">
                        <div x-data="usersMasterDetail()"
                             x-init="syncFromWindow()"
                             class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                            <div class="p-5 border-b border-slate-200">
                                <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-500">User Details</h2>
                            </div>

                            <div class="p-5">
                                {{-- Empty state --}}
                                <template x-if="!selectedUser">
                                    <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center">
                                        <div class="mx-auto h-12 w-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/>
                                            </svg>
                                        </div>
                                        <p class="mt-3 text-sm font-semibold text-slate-900">No user selected</p>
                                        <p class="mt-1 text-sm text-slate-500">Select a user from the list to view details.</p>
                                    </div>
                                </template>

                                {{-- Selected user --}}
                                <template x-if="selectedUser">
                                    <div>
                                        <div class="flex items-center gap-4">
                                            <template x-if="selectedUser.picture">
                                                <img :src="selectedUser.picture" alt="Profile"
                                                     class="h-14 w-14 rounded-2xl object-cover border border-slate-200">
                                            </template>

                                            <template x-if="!selectedUser.picture">
                                                <div class="h-14 w-14 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-sm font-extrabold text-slate-600"
                                                     x-text="selectedUser.initials || 'U'"></div>
                                            </template>

                                            <div>
                                                <div class="text-lg font-extrabold text-slate-900" x-text="selectedUser.name"></div>
                                                <div class="text-sm text-slate-500">
                                                    ID: <span class="font-semibold text-slate-700" x-text="selectedUser.staff_id"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-5 space-y-3">
                                            <div class="rounded-xl border border-slate-200 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Department</p>
                                                <p class="mt-1 text-sm font-semibold text-slate-900" x-text="selectedUser.department"></p>
                                            </div>

                                            <div class="rounded-xl border border-slate-200 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Faculty</p>
                                                <p class="mt-1 text-sm font-semibold text-slate-900" x-text="selectedUser.faculty"></p>
                                            </div>
                                        </div>

                                        <div class="mt-5 flex flex-col gap-2">
                                            <a :href="selectedUser ? `{{ url('/users') }}/${selectedUser.id}/edit` : '#'"
                                               class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                                Edit User
                                            </a>

                                            <form method="POST"
                                                  :action="selectedUser ? `{{ url('/users') }}/${selectedUser.id}` : '#'"
                                                  onsubmit="return confirm('Delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="w-full inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">
                                                    Delete User
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Alpine store script --}}
                    <script>
                        // One shared store (window-level) so left list and right panel stay synced
                        window.__usersMD = window.__usersMD || {
                            selectedUserId: null,
                            selectedUser: null,
                        };

                        function usersMasterDetail() {
                            return {
                                // filters
                                query: '',
                                faculty: '',
                                department: '',
                                openFilter: false,

                                // derived options (read from DOM rows)
                                faculties: [],
                                departments: [],

                                // selection
                                selectedUserId: window.__usersMD.selectedUserId,
                                selectedUser: window.__usersMD.selectedUser,

                                // bulk
                                selectedIds: [],

                                init() {
                                    this.buildOptions();
                                    // if you want: auto-select first visible row
                                },

                                syncFromWindow() {
                                    this.selectedUserId = window.__usersMD.selectedUserId;
                                    this.selectedUser = window.__usersMD.selectedUser;
                                },

                                selectUser(user) {
                                    window.__usersMD.selectedUserId = user.id;
                                    window.__usersMD.selectedUser = user;

                                    this.selectedUserId = user.id;
                                    this.selectedUser = user;
                                },

                                buildOptions() {
                                    const rows = Array.from(this.$root.querySelectorAll('tbody tr'));
                                    const fac = new Set();
                                    const dep = new Set();

                                    rows.forEach(r => {
                                        const f = (r.dataset.faculty || '').trim();
                                        const d = (r.dataset.department || '').trim();
                                        if (f) fac.add(this.titleize(f));
                                        if (d) dep.add(this.titleize(d));
                                    });

                                    this.faculties = Array.from(fac).sort();
                                    this.departments = Array.from(dep).sort();
                                },

                                titleize(v) {
                                    // dataset stored in lowercase; show nicer
                                    return v.split(' ').map(w => w ? w[0].toUpperCase() + w.slice(1) : w).join(' ');
                                },

                                applyFilters() {
                                    // nothing needed, rowVisible uses reactive fields
                                },

                                resetFilters() {
                                    this.query = '';
                                    this.faculty = '';
                                    this.department = '';
                                    this.openFilter = false;
                                },

                                rowVisible(el) {
                                    if (!el) return true;
                                    const name = el.dataset.name || '';
                                    const id = el.dataset.staffid || '';
                                    const f = el.dataset.faculty || '';
                                    const d = el.dataset.department || '';

                                    const q = (this.query || '').toLowerCase().trim();
                                    const faculty = (this.faculty || '').toLowerCase().trim();
                                    const department = (this.department || '').toLowerCase().trim();

                                    const matchQ = !q || name.includes(q) || id.includes(q);
                                    const matchF = !faculty || f === faculty;
                                    const matchD = !department || d === department;

                                    return matchQ && matchF && matchD;
                                },

                                toggleAll(checked) {
                                    const ids = [];
                                    const rows = Array.from(this.$root.querySelectorAll('tbody tr'));
                                    rows.forEach(r => {
                                        if (this.rowVisible(r)) {
                                            const id = r.getAttribute('@click') ? null : null; // ignore
                                        }
                                    });
                                    // simpler: just clear / set based on current
                                    if (!checked) {
                                        this.selectedIds = [];
                                        return;
                                    }
                                    // Collect from Blade by storing id in dataset
                                    rows.forEach(r => {
                                        if (this.rowVisible(r)) {
                                            // we stored selected user id in inline click object; easiest is dataset-id
                                            // If you want bulk, add: data-userid="{{ $u->id }}" on <tr>
                                            if (r.dataset.userid) ids.push(r.dataset.userid);
                                        }
                                    });
                                    this.selectedIds = ids;
                                },

                                toggleOne(id, checked) {
                                    if (checked && !this.selectedIds.includes(id)) this.selectedIds.push(id);
                                    if (!checked) this.selectedIds = this.selectedIds.filter(x => x !== id);
                                },

                                bulkClear() {
                                    this.selectedIds = [];
                                }
                            }
                        }
                    </script>
                </div>
            </div>

        </div>
    </section>
@endsection
