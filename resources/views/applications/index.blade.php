@extends('layouts.main')

@section('content')
    <section class="bg-background min-h-screen">
        <div class="container mx-auto w-[90%] py-8 space-y-6">

            {{-- Page header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900">รายการใบสมัคร</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        จัดการและตรวจสอบใบสมัครผลงานที่ถูกส่งเข้ามาในระบบ
                    </p>
                </div>

                <a href="{{ route('applications.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition">
                    <span class="text-lg leading-none">+</span>
                    สร้างใบสมัครใหม่
                </a>
            </div>

            {{-- Summary cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                {{-- Total --}}
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">ใบสมัครทั้งหมด</p>
                            <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $totalCount }}</p>
                            <p class="mt-1 text-xs text-slate-400">รายการที่ส่งมาทั้งหมด</p>
                        </div>
                    </div>
                </div>

                {{-- Pending --}}
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">รอตรวจสอบ</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $pendingCount }}</p>
                    <p class="mt-1 text-xs text-slate-400">รอการประเมินจากกรรมการ</p>
                    <div class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full"
                         style="background: color-mix(in oklab, theme(colors.pending) 20%, transparent);"></div>
                </div>

                {{-- Approved --}}
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">ผ่านการพิจารณา</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $approvedCount }}</p>
                    <p class="mt-1 text-xs text-slate-400">ใบสมัครที่ได้รับการอนุมัติ</p>
                    <div class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full"
                         style="background: color-mix(in oklab, theme(colors.approved) 18%, transparent);"></div>
                </div>

                {{-- Rejected --}}
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">ไม่ผ่านเงื่อนไข</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $rejectedCount }}</p>
                    <p class="mt-1 text-xs text-slate-400">ใบสมัครที่ถูกปฏิเสธ</p>
                </div>
            </div>

            {{-- 🌟 Table card: ใส่ ID เป้าหมายสำหรับ HTMX --}}
            <div id="applications-table-container" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                {{-- Toolbar --}}
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-200 bg-slate-50/60 p-4">

                    {{-- 🌟 HTMX Form --}}
                    <form method="GET"
                          action="{{ route('applications.index') }}"
                          hx-get="{{ route('applications.index') }}"
                          hx-target="#applications-table-container"
                          hx-select="#applications-table-container"
                          hx-swap="outerHTML"
                          hx-push-url="true"
                          class="w-full flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                        {{-- Search Input --}}
                        <div class="w-full lg:w-[400px]">
                            <label class="relative block">
                                <span class="sr-only">ค้นหา</span>
                                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M9 3a6 6 0 104.472 10.03l2.249 2.25a1 1 0 001.414-1.415l-2.25-2.249A6 6 0 009 3zm-4 6a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                <input
                                    name="q"
                                    value="{{ request('q') }}"
                                    class="block w-full rounded-lg border-slate-200 bg-white pl-10 pr-3 py-2 text-sm placeholder:text-slate-400 focus:border-primary focus:ring-primary/20 transition"
                                    placeholder="ค้นหาด้วยชื่อ, อีเมล, ประเภท..."
                                    type="text"
                                />
                            </label>
                        </div>

                        {{-- Action Area (Dropdown + Buttons) --}}
                        <div class="flex flex-wrap lg:flex-nowrap gap-2 justify-end w-full lg:w-auto">

                            {{-- Dropdown Filter ตามสถานะ --}}
                            <select name="status"
                                    onchange="this.form.dispatchEvent(new Event('submit'))"
                                    class="rounded-lg border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:ring-primary/20 transition w-full lg:w-48">
                                <option value="">ทุกสถานะ</option>
                                @foreach(\App\Enums\ApplicationStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit"
                                    class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition">
                                ค้นหา
                            </button>

                            {{-- ปุ่มรีเซ็ต (ใช้ HTMX เพื่อโหลดกลับแบบไม่กระพริบ) --}}
                            <a href="{{ route('applications.index') }}"
                               hx-get="{{ route('applications.index') }}"
                               hx-target="#applications-table-container"
                               hx-select="#applications-table-container"
                               hx-swap="outerHTML"
                               hx-push-url="true"
                               class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition whitespace-nowrap">
                                รีเซ็ต
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            {{-- เอาคอลัมน์ รหัส ออกแล้ว --}}
                            <th class="px-6 py-3 whitespace-nowrap">ผู้สมัคร</th>
                            <th class="px-6 py-3 whitespace-nowrap">รอบการรับสมัคร</th>
                            <th class="px-6 py-3 whitespace-nowrap">ประเภทรางวัล</th>
                            <th class="px-6 py-3 whitespace-nowrap text-center">สถานะ</th>
                            <th class="px-6 py-3 whitespace-nowrap">วันที่อัปเดต</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($applications as $application)
                            <tr class="hover:bg-slate-50/70 transition-colors">

                                <td class="px-6 py-4 min-w-[250px]">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-slate-200 overflow-hidden border border-slate-100 flex-shrink-0">
                                            <img src="{{ $application->user->profile_url }}"
                                                 alt="{{ $application->user->name }}"
                                                 class="h-full w-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('applications.show', ['application' => $application]) }}"
                                               class="text-sm font-semibold text-primary hover:underline truncate block">
                                                {{ $application->user->name }}
                                            </a>
                                            @if(!empty($application->user->email))
                                                <p class="text-xs text-slate-500 truncate">{{ $application->user->email }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($application->applicationRound)
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-slate-900">
                                                ปีการศึกษา {{ $application->applicationRound->thai_academic_year ?? ($application->applicationRound->academic_year + 543) }}
                                            </span>
                                            <span class="text-xs text-slate-500">
                                                ภาคการศึกษา{{ $application->applicationRound->semester->label() }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">ไม่ได้ระบุ</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full border border-primary/20 bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">
                                        {{ $application->applicationCategory->name }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $application->status->color() }}">
                                        {{ $application->status->label() }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-500 whitespace-nowrap">
                                    {{ $application->updated_at->locale('th')->translatedFormat('j M') }} {{ $application->updated_at->year + 543 }}
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $application->updated_at->format('H:i') }} น.</div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                {{-- ปรับ colspan เป็น 5 ให้ตรงกับจำนวนคอลัมน์ที่เหลือ --}}
                                <td colspan="5" class="px-6 py-14 text-center">
                                    <div class="mx-auto max-w-sm">
                                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-base font-semibold text-slate-900">ไม่พบข้อมูลใบสมัคร</p>
                                        <p class="mt-1 text-sm text-slate-500">ยังไม่มีการส่งใบสมัครเข้ามาในระบบ หรือลองปรับเงื่อนไขการค้นหาใหม่</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer (มี hx-boost เพื่อให้การกดเปลี่ยนหน้าใช้งาน HTMX) --}}
                <div class="flex flex-col md:flex-row items-center justify-between border-t border-slate-200 px-6 py-4 text-sm text-slate-500 gap-4">
                    <span>แสดงรายการที่ {{ $applications->firstItem() ?? 0 }} ถึง {{ $applications->lastItem() ?? 0 }} จากทั้งหมด <span class="font-semibold text-slate-900">{{ $applications->total() }}</span> รายการค้นหา</span>

                    <div class="laravel-pagination"
                         hx-boost="true"
                         hx-target="#applications-table-container"
                         hx-select="#applications-table-container"
                         hx-swap="outerHTML">
                        {{ $applications->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
