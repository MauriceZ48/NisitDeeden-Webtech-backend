@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[90%] py-8 space-y-6">

            {{-- Header --}}
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900">จัดการผู้ใช้งาน</h1>
                    <p class="mt-1 text-sm text-slate-500">จัดการข้อมูลผู้ใช้งาน</p>
                </div>

                <a href="{{ route('users.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90">
                    <span class="text-lg leading-none">+</span>
                    เพิ่มผู้ใช้งานใหม่
                </a>
            </div>

            {{-- Summary Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">ทั้งหมด</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $count }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{\App\Enums\UserRole::STUDENT->label()}}</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $userCount }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{\App\Enums\UserRole::ADMIN->label()}} (กองพัฒนานิสิต)</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $adminCount }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{\App\Enums\UserRole::COMMITTEE->label()}}</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $committeeCount }}</p>
                </div>
            </div>

            <div id="main-content-area" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- LEFT: List --}}
                <div class="lg:col-span-8" id="user-list-container">
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        {{-- HTMX Search Form --}}
                        <div class="p-4 border-b border-slate-200">
                            <form method="GET"
                                  action="{{ route('users.index') }}"
                                  hx-get="{{ route('users.index') }}"
                                  hx-target="#user-list-container"
                                  hx-select="#user-list-container"
                                  hx-swap="outerHTML"
                                  hx-push-url="true"
                                  class="flex flex-col gap-3 md:flex-row md:items-center">

                                <div class="relative w-full md:w-[360px]">
                                    <input name="q" value="{{ $q ?? '' }}" type="text" placeholder="ค้นหาชื่อ หรือ รหัสนิสิต..." class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"/>
                                </div>
                                <select name="role" onchange="this.form.dispatchEvent(new Event('submit'))" class="w-full md:w-48 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                                    <option value="">ทุกสิทธิ์การใช้งาน</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->value }}" @selected(($role ?? '') === $r->value)>
                                            {{ $r->name === 'ADMIN' ? 'ผู้ดูแลระบบ' : ($r->name === 'STUDENT' ? 'นิสิต' : 'ผู้ประเมิน') }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">ค้นหา</button>
                                <a href="{{ route('users.index') }}"
                                   hx-get="{{ route('users.index') }}"
                                   hx-target="#user-list-container"
                                   hx-select="#user-list-container"
                                   class="text-sm text-slate-500 hover:text-slate-700">ล้างค่า</a>
                            </form>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    <th class="px-4 py-3">ผู้ใช้งาน</th>
                                    <th class="px-4 py-3">รหัสประจำตัว</th>
                                    <th class="px-4 py-3">สิทธิ์</th>
                                    <th class="px-4 py-3">ตำแหน่ง</th>
                                    <th class="px-4 py-3">คณะ/ภาควิชา</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-sm">
                                @foreach($users as $u)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-4">
                                            <a href="{{ route('users.index', array_merge(request()->query(), ['selected' => $u->id])) }}"
                                               hx-get="{{ route('users.index', array_merge(request()->query(), ['selected' => $u->id])) }}"
                                               hx-target="#main-content-area"
                                               hx-select="#main-content-area"
                                               hx-swap="outerHTML"
                                               hx-push-url="true"
                                               class="flex items-center gap-3">
                                                <div class="font-semibold text-slate-900">{{ $u->name }}</div>
                                            </a>
                                        </td>
                                        <td class="px-4 py-4 text-slate-700">{{ $u->university_id }}</td>
                                        <td class="px-4 py-4">
                                            @php
                                                // Get the value from the Enum or string
                                                $roleValue = $u->role instanceof \UnitEnum ? $u->role->value : $u->role;

                                                $roleStyles = match($roleValue) {
                                                    'ADMIN'     => 'bg-red-100 text-red-700 border-red-200',
                                                    'COMMITTEE' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                    'STUDENT'   => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                                    default     => 'bg-slate-100 text-slate-700 border-slate-200',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold border {{ $roleStyles }}">
                                                {{-- Use your Enum label() method if it exists, otherwise fallback to the value --}}
                                                {{ method_exists($u->role, 'label') ? $u->role->label() : $u->role->name }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            @if($u->role == \App\Enums\UserRole::COMMITTEE)
                                                <div>{{ $u->position->label() }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            @if($u->faculty)
                                                <div class="text-slate-900">{{ $u->faculty->label() }}</div>
                                            @endif
                                            @if($u->department)
                                                <div class="text-xs text-slate-500">{{ $u->department->label() }}</div>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- Footer (Pagination) --}}
                        <div class="p-4 border-t border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-slate-500">
                            <span>
                                แสดงรายการที่ {{ $users->firstItem() ?? 0 }} ถึง {{ $users->lastItem() ?? 0 }} จากทั้งหมด {{ $users->total() }} รายการ
                            </span>

                            <div class="laravel-pagination"
                                 hx-boost="true"
                                 hx-target="#user-list-container"
                                 hx-select="#user-list-container">
                                {{ $users->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Details Panel --}}
                <div class="lg:col-span-4">
                    <div class="sticky top-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-500 mb-4">รายละเอียดผู้ใช้งาน</h2>

                        @if(!$selectedUser)
                            <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center">
                                <p class="text-sm font-semibold text-slate-900">ยังไม่ได้เลือกผู้ใช้งาน</p>
                                <p class="mt-1 text-sm text-slate-500">กรุณาเลือกรายชื่อจากตารางเพื่อดูรายละเอียด</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    @php
                                        $initials = collect(explode(' ', trim($selectedUser->name)))
                                            ->filter()->take(2)->map(fn($p) => mb_substr($p, 0, 1))->join('');
                                    @endphp

                                    {{-- กรอบรูปโปรไฟล์ --}}
                                    <div class="h-14 w-14 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @if($selectedUser->profile_url)
                                            {{-- แสดงรูปภาพ ถ้ามี profile_url --}}
                                            <img src="{{ $selectedUser->profile_url }}"
                                                 alt="{{ $selectedUser->name }}"
                                                 class="h-full w-full object-cover">
                                        @else
                                            {{-- แสดงตัวอักษรย่อ ถ้าไม่มีรูปภาพ --}}
                                            <span class="text-sm font-extrabold text-slate-600">{{ $initials ?: 'U' }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-lg font-extrabold text-slate-900">{{ $selectedUser->name }}</div>
                                        <div class="text-sm text-slate-500">รหัส: <span class="font-semibold text-slate-700">{{ $selectedUser->university_id }}</span></div>
                                    </div>
                                </div>

                                <div class="mt-5 space-y-3">
                                    <div class="rounded-xl border border-slate-200 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">อีเมล</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-900 break-all">{{ $selectedUser->email }}</p>
                                    </div>
                                    @if($selectedUser->department)
                                    <div class="rounded-xl border border-slate-200 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">ภาควิชา</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $selectedUser->department->label() }}</p>
                                    </div>
                                    @endif
                                    @if($selectedUser->faculty)
                                    <div class="rounded-xl border border-slate-200 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">คณะ</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $selectedUser->faculty->label() }}</p>
                                    </div>
                                    @endif
                                    @if($selectedUser->position)
                                        <div class="rounded-xl border border-slate-200 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">ตำแหน่งทางการ</p>
                                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $selectedUser->position->label() }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-5 flex flex-col gap-2">
                                    <a href="{{ route('users.edit', $selectedUser) }}"
                                       class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm18-11.5a1 1 0 0 0 0-1.41l-1.34-1.34a1 1 0 0 0-1.41 0l-1.13 1.13 3.75 3.75L21 5.75z"/>
                                        </svg>
                                        แก้ไขข้อมูล
                                    </a>
                                    <form method="POST" action="{{ route('users.destroy', $selectedUser) }}" onsubmit="return confirm('ยืนยันการลบผู้ใช้งานนี้?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-5 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">
                                            ลบผู้ใช้งาน
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                </div>
            </div>
        </div>
    </section>
@endsection
