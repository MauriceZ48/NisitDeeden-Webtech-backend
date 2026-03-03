@extends('layouts.main')

@section('content')

    <section class="bg-background">
        <div class="container mx-auto w-[90%] py-8 space-y-6">

            {{-- Header --}}
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900">Users</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage users by faculty and department.</p>
                </div>

                <a href="{{ route('users.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <span class="text-lg leading-none">+</span>
                    New User
                </a>
            </div>

{{--            --}}{{-- Summary --}}
{{--            @php--}}
{{--                $usersCollection = $users instanceof \Illuminate\Pagination\AbstractPaginator--}}
{{--                    ? $users->getCollection()--}}
{{--                    : collect($users);--}}

{{--                $facultyCount = $usersCollection->pluck('faculty')->filter()->unique()->count();--}}
{{--                $deptCount    = $usersCollection->pluck('department')->filter()->unique()->count();--}}
{{--            @endphp--}}

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4"> {{-- Changed to 4 cols --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $count }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Students</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $userCount }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Admins (Student Development Division)</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $adminCount }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Committee</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $committeeCount }}</p>
                </div>
            </div>

            {{-- Master-Detail Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- LEFT: List --}}
                <div class="lg:col-span-8">
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                        {{-- Toolbar (Laravel GET) --}}
                        <div class="p-4 border-b border-slate-200">
                            <form method="GET" action="{{ route('users.index') }}"
                                  class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

                                <div class="flex w-full flex-col gap-3 md:flex-row md:items-center md:justify-between">

                                    {{-- LEFT: Filters --}}
                                    <div class="flex flex-col md:flex-row gap-2 items-stretch w-full md:w-auto">

                                        <div class="relative w-full md:w-[360px]">
                                            <input
                                                name="q"
                                                value="{{ $q ?? '' }}"
                                                type="text"
                                                placeholder="Search name or ID..."
                                                onkeydown="if(event.key === 'Enter'){ this.form.submit(); }"
                                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary focus:ring-primary/20"
                                            />
                                        </div>

                                        <select name="role"
                                                onchange="this.form.submit()"
                                                class="w-full md:w-56 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-primary focus:ring-primary/20">
                                            <option value="">All Roles</option>
                                            @foreach($roles as $r)
                                                <option value="{{ $r->value }}" @selected(($role ?? '') === $r->value)>
                                                    {{ ucfirst(strtolower($r->name)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- RIGHT: Actions --}}
                                    <div class="flex gap-2 justify-end">
                                        <button type="submit"
                                                class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                            Apply
                                        </button>

                                        <a href="{{ route('users.index') }}"
                                           class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                            Reset
                                        </a>
                                    </div>

                                </div>


                            </form>
                        </div>

                        {{-- Table --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    <th class="px-4 py-3">User</th>
                                    <th class="px-4 py-3">University ID</th>
                                    <th class="px-4 py-3">Department</th>
                                    <th class="px-4 py-3">Faculty</th>
                                    <th class="px-4 py-3">Role</th>
{{--                                    <th class="px-4 py-3 text-right">Actions</th>--}}
                                </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-200">
                                @foreach($users as $u)
                                    @php
                                        $pic = $u->profile_url ?? null;
                                        $initials = collect(explode(' ', trim($u->name ?? 'User')))
                                            ->filter()
                                            ->take(2)
                                            ->map(fn($p) => strtoupper(mb_substr($p, 0, 1)))
                                            ->join('');

                                        $isSelected = ($selectedUser?->id === $u->id);
                                    @endphp

                                    <tr class="hover:bg-slate-50 {{ $isSelected ? 'bg-slate-50' : '' }}">
                                        <td class="px-4 py-4">
                                            <a class="block"
                                               href="{{ route('users.index', array_merge(request()->query(), ['selected' => $u->id])) }}">
                                                <div class="flex items-center gap-3">
                                                    @if($pic)
                                                        <img src="{{ $pic }}" alt="Profile"
                                                             class="h-9 w-9 rounded-full object-cover border border-slate-200">
                                                    @else
                                                        <div
                                                            class="h-9 w-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                                            {{ $initials ?: 'U' }}
                                                        </div>
                                                    @endif

                                                    <div>
                                                        <div class="font-semibold text-slate-900">{{ $u->name }}</div>
                                                        <div class="text-xs text-slate-500">{{ $u->email }}</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </td>

                                        <td class="px-4 py-4 text-sm text-slate-700">
                                            {{ $u->university_id }}
                                        </td>

                                        <td class="px-4 py-4 text-sm text-slate-700">{{ $u->department }}</td>
                                        <td class="px-4 py-4 text-sm text-slate-700">{{ $u->faculty }}</td>
                                        <td class="px-4 py-4">
                                            @php
                                                $roleValue = $u->role?->value ?? $u->role;

                                                $roleStyles = match($roleValue) {
                                                    'ADMIN'     => 'bg-red-100 text-red-700 border-red-200',
                                                    'COMMITTEE' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                    'STUDENT'      => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                                    default     => 'bg-slate-100 text-slate-700 border-slate-200',
                                                };
                                            @endphp

                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold border {{ $roleStyles }}">
                                                {{ ucfirst(strtolower($roleValue)) }}
                                            </span>

                                            {{-- Show the specific position (e.g., Dean) below the role badge --}}
                                            @if($u->position)
                                                <div class="mt-1 text-[10px] font-medium text-slate-400 uppercase tracking-tight">
                                                    {{ $u->position }}
                                                </div>
                                            @endif
                                        </td>


{{--                                        <td class="px-4 py-4 text-right">--}}
{{--                                            <div class="inline-flex items-center gap-2">--}}
{{--                                                <a href="{{ route('users.edit', $u) }}"--}}
{{--                                                   class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">--}}
{{--                                                    Edit--}}
{{--                                                </a>--}}

{{--                                                <form method="POST" action="{{ route('users.destroy', $u) }}"--}}
{{--                                                      onsubmit="return confirm('Delete this user?');">--}}
{{--                                                    @csrf--}}
{{--                                                    @method('DELETE')--}}
{{--                                                    <button type="submit"--}}
{{--                                                            class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-semibold text-red-600 hover:bg-red-50">--}}
{{--                                                        Delete--}}
{{--                                                    </button>--}}
{{--                                                </form>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Footer --}}
                        <div
                            class="p-4 border-t border-slate-200 flex items-center justify-between text-sm text-slate-500">
                        <span>
                            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                        </span>

                            <div class="text-slate-700">
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Details panel --}}
                <div class="lg:col-span-4">
                    <div class="sticky top-6">
                        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                            <div class="p-5 border-b border-slate-200">
                                <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-500">User
                                    Details</h2>
                            </div>

                            <div class="p-5">
                                @if(!$selectedUser)
                                    <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center">
                                        <p class="text-sm font-semibold text-slate-900">No user selected</p>
                                        <p class="mt-1 text-sm text-slate-500">Select a user from the list to view
                                            details.</p>
                                    </div>
                                @else
                                    @php
                                        $pic = $selectedUser->profile_url ?? null;
                                        $initials = collect(explode(' ', trim($selectedUser->name ?? 'User')))
                                            ->filter()
                                            ->take(2)
                                            ->map(fn($p) => strtoupper(mb_substr($p, 0, 1)))
                                            ->join('');
                                    @endphp

                                    <div class="flex items-center justify-between gap-4">
                                        {{-- LEFT: avatar + name --}}
                                        <div class="flex items-center gap-4">
                                            @if($pic)
                                                <img src="{{ $pic }}" alt="Profile"
                                                     class="h-14 w-14 rounded-2xl object-cover border border-slate-200">
                                            @else
                                                <div
                                                    class="h-14 w-14 rounded-2xl bg-slate-100 border border-slate-200
                       flex items-center justify-center text-sm font-extrabold text-slate-600">
                                                    {{ $initials ?: 'U' }}
                                                </div>
                                            @endif

                                            <div>
                                                <div class="text-lg font-extrabold text-slate-900">
                                                    {{ $selectedUser->name }}
                                                </div>

                                                <div class="text-sm text-slate-500">
                                                    ID:
                                                    <span class="font-semibold text-slate-700">
                                                        {{ $selectedUser->university_id }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- RIGHT: role badge --}}
                                        @php
                                            $roleValue = $selectedUser->role?->value ?? $selectedUser->role;

                                            $roleStyles = match($roleValue) {
                                                'ADMIN' => 'bg-red-100 text-red-700 border-red-200',
                                                'STUDENT'  => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                                'COMMITTEE' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                default => 'bg-slate-100 text-slate-700 border border-slate-200',
                                            };

                                            $roleLabel = match($roleValue) {
                                                'ADMIN' => 'Admin',
                                                'STUDENT'  => 'Student',
                                                'COMMITTEE' => 'Committee',
                                                default => 'Unknown',
                                            };
                                        @endphp

                                        <span
                                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $roleStyles }}">
                                            {{ $roleLabel }}
                                        </span>
                                    </div>



                                    <div class="mt-5 space-y-3">
                                        <div class="rounded-xl border border-slate-200 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                                Email</p>
                                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $selectedUser->email }}</p>
                                        </div>
                                        <div class="rounded-xl border border-slate-200 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                                Department</p>
                                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $selectedUser->department }}</p>
                                        </div>

                                        <div class="rounded-xl border border-slate-200 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                                Faculty</p>
                                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $selectedUser->faculty }}</p>
                                        </div>
                                        @if($selectedUser->position)
                                            <div class="rounded-xl border border-slate-200 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Official Position</p>
                                                <p class="mt-1 text-sm font-semibold text-slate-900">{{ $selectedUser->position }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-5 flex flex-col gap-2">
{{--                                        <a href="{{ route('users.edit', $selectedUser) }}"--}}
{{--                                           class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30">--}}
{{--                                            Edit User--}}
{{--                                        </a>--}}

                                        <form method="POST" action="{{ route('users.destroy', $selectedUser) }}"
                                              onsubmit="return confirm('Delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-5 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-200">
                                                Delete User
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
