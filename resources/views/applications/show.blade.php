@php
    use App\Enums\ApplicationCategory;

    /** @var \App\Models\Application $application */

    $categoryLabel = $application->category?->label()
        ?? ($application->category?->value ?? '—');

    $user = $application->user;

    // ปรับ Format วันที่เป็นภาษาไทยและ พ.ศ.
    $createdAt = $application->created_at ? $application->created_at->toThaiDateTime() : '—';
    $updatedAt = $application->updated_at ? $application->updated_at->toThaiDateTime() : '—';

    $backUrl = request('return_url') ?? url()->previous();

    // Small UI helpers
    $badge = fn ($cls) => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold border {$cls}";
    $card  = "rounded-2xl border border-slate-200 bg-white shadow-sm";
@endphp

@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[90%] lg:w-[80%] py-10 space-y-6">

            {{-- ส่วนหัว (Top bar) --}}
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('applications.index') }}"
                           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15.5 5 8.5 12l7 7 1.5-1.5L11.5 12 17 6.5 15.5 5z"/>
                            </svg>
                            ย้อนกลับ
                        </a>
                    </div>

                    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                        รายละเอียดใบสมัครรางวัลนิสิตดีเด่น
                    </h1>
                    <p class="text-slate-500">
                        ตรวจสอบข้อมูลใบสมัคร ข้อมูลนิสิต และเอกสารแนบประกอบการพิจารณา
                    </p>
                </div>

                {{-- พื้นที่สำหรับปุ่ม Actions --}}
                <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">

                    {{-- ปุ่มแก้ไขข้อมูล (Edit) --}}
                    <a href="{{ route('applications.edit', $application) }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm18-11.5a1 1 0 0 0 0-1.41l-1.34-1.34a1 1 0 0 0-1.41 0l-1.13 1.13 3.75 3.75L21 5.75z"/>
                        </svg>
                        แก้ไขข้อมูล
                    </a>

                    {{-- ปุ่มลบใบสมัคร (Delete) --}}
                    <form onsubmit="return confirm('ยืนยันการลบใบสมัครนี้ใช่หรือไม่?\n(การกระทำนี้ไม่สามารถย้อนกลับได้)')"
                          action="{{ route('applications.destroy', $application) }}"
                          method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-5 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-200 transition">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"/>
                            </svg>
                            ลบใบสมัคร
                        </button>
                    </form>

                </div>

            </div>

            {{-- โครงสร้างหลัก (Main grid) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ฝั่งซ้าย: ข้อมูลใบสมัคร (LEFT: details) --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- การ์ดข้อมูลทั่วไป (Overview card) --}}
                    <div class="{{ $card }}">
                        <div class="p-6 md:p-8 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">ข้อมูลทั่วไป</h2>
                                <p class="text-sm text-slate-500">ข้อมูลพื้นฐานเกี่ยวกับใบสมัครนี้</p>
                            </div>

                            {{-- ป้ายสถานะแบบไดนามิก --}}
                            @if($application->status)
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-bold border {{ $application->status->color() }}">
                                    {{ $application->status->label() }}
                                </span>
                            @endif
                        </div>

                        <div class="p-6 md:p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {{-- สถานะปัจจุบัน --}}
                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">สถานะปัจจุบัน</p>
                                    <p class="mt-1 text-sm font-bold {{ $application->status ? explode(' ', $application->status->color())[1] : 'text-slate-900' }}">
                                        {{ $application->status ? $application->status->label() : '—' }}
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">ประเภทรางวัล</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $application->applicationCategory?->name ?? '—' }}</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">ปีการศึกษา</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">
                                        {{ $application->applicationRound?->thai_academic_year ?? '—' }}
                                        (ภาคการศึกษา{{ $application->applicationRound?->semester?->label() ?? '—' }})
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">วันที่ส่งใบสมัคร</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $createdAt }}</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">อัปเดตล่าสุด</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $updatedAt }}</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">รหัสอ้างอิง</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">#{{ $application->id }}</p>
                                </div>
                            </div>

                            {{-- บันทึกเหตุผลการปฏิเสธ (แสดงเฉพาะเมื่อถูกปฏิเสธ หรือมีการพิมพ์เหตุผลไว้) --}}
                            @if($application->status === \App\Enums\ApplicationStatus::REJECTED || $application->rejection_reason)
                                <div class="mt-6 rounded-2xl border border-red-100 bg-red-50/50 p-5">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <p class="text-xs font-bold text-red-700 uppercase tracking-wider">บันทึกการพิจารณา</p>
                                    </div>
                                    <p class="text-sm text-red-800 leading-relaxed">{{ $application->rejection_reason ?: 'ไม่ได้ระบุเหตุผล' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- คำตอบจากฟอร์มแบบไดนามิก (Dynamic Form Answers) --}}
                    <div class="{{ $card }}">
                        <div class="p-6 md:p-8 border-b border-slate-100">
                            <h2 class="text-lg font-semibold text-slate-900">ข้อมูลคำตอบใบสมัคร</h2>
                            <p class="text-sm text-slate-500">ข้อมูลเฉพาะที่นิสิตระบุสำหรับประเภทรางวัลนี้</p>
                        </div>

                        <div class="p-6 md:p-8 space-y-4">
                            @forelse($application->attributeValues as $answer)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-5">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        {{ $answer->attribute?->label ?? 'ฟิลด์กำหนดเอง' }}
                                    </p>

                                    <div class="mt-2">
                                        @if($answer->attribute?->type === 'file')
                                            @if($answer->value)
                                                <div class="flex items-center justify-between gap-4 rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3">
                                                    <div class="flex items-center gap-3 overflow-hidden">
                                                        <svg class="h-5 w-5 text-blue-600 flex-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                            <polyline points="13 2 13 9 20 9"></polyline>
                                                        </svg>
                                                        <span class="text-sm font-semibold text-blue-900 truncate">
                                                            {{ basename($answer->value) }}
                                                        </span>
                                                    </div>
                                                    <a href="{{ asset('storage/' . $answer->value) }}" target="_blank"
                                                       class="text-xs font-bold text-blue-700 hover:underline flex-none">
                                                        ดูไฟล์
                                                    </a>
                                                </div>
                                            @else
                                                <p class="text-sm italic text-slate-400">ไม่ได้อัปโหลดไฟล์</p>
                                            @endif
                                        @else
                                            <p class="text-sm font-medium text-slate-900 leading-relaxed">
                                                {{ $answer->value ?: '—' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-sm text-slate-500">ไม่มีข้อมูลเพิ่มเติมในฟอร์ม</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- เอกสารแนบ (Attachments) --}}
                    <div class="{{ $card }}">
                        <div class="p-6 md:p-8 border-b border-slate-100 flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">เอกสารแนบ</h2>
                                <p class="text-sm text-slate-500">ไฟล์ที่อัปโหลดเพื่อประกอบการพิจารณาเพิ่มเติม</p>
                            </div>

                            <span class="{{ $badge('bg-slate-50 text-slate-700 border-slate-200') }}">
                                {{ $application->attachments?->count() ?? 0 }} ไฟล์
                            </span>
                        </div>

                        <div class="p-6 md:p-8">
                            @if(($application->attachments?->count() ?? 0) === 0)
                                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/40 p-8 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white border border-slate-200">
                                        <svg class="h-6 w-6 text-slate-600" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 15v4H5v-4H3v6h18v-6h-2zM11 3h2v10h3l-4 4-4-4h3V3z"/>
                                        </svg>
                                    </div>
                                    <p class="mt-3 text-sm font-semibold text-slate-800">ไม่มีเอกสารแนบ</p>
                                    <p class="mt-1 text-xs text-slate-500">คุณสามารถเพิ่มไฟล์ได้โดยการแก้ไขใบสมัครนี้</p>
                                </div>
                            @else
                                <div class="space-y-2">
                                    @foreach($application->attachments as $file)
                                        @php
                                            $name = $file->file_name ?? 'File';
                                            $path = $file->file_path ?? null;
                                            $sizeKb = isset($file->file_size) ? round($file->file_size / 1024, 2) : null;
                                            $ext = strtoupper(pathinfo($name, PATHINFO_EXTENSION));
                                            $ext = $ext !== '' ? $ext : 'FILE';
                                        @endphp

                                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="h-10 w-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center flex-none">
                                                    <span class="text-[11px] font-extrabold text-slate-700">
                                                        {{ strlen($ext) <= 4 ? $ext : 'FILE' }}
                                                    </span>
                                                </div>

                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $name }}</p>
                                                    <p class="text-xs text-slate-500">
                                                        {{ $sizeKb !== null ? $sizeKb.' KB' : '—' }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if($path)
                                                <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex-none">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3z"/>
                                                        <path d="M5 5h6V3H3v8h2V5zm0 14v-6H3v8h8v-2H5zm14 0h-6v2h8v-8h-2v6z"/>
                                                    </svg>
                                                    เปิดดู
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- ฝั่งขวา: ข้อมูลนิสิต (RIGHT: student card) --}}
                <div class="space-y-6">

                    <div class="{{ $card }}">
                        <div class="p-6 md:p-7 border-b border-slate-100">
                            <h2 class="text-lg font-semibold text-slate-900">ประวัตินิสิต</h2>
                            <p class="text-sm text-slate-500">ข้อมูลเจ้าของใบสมัครนี้</p>
                        </div>

                        <div class="p-6 md:p-7 space-y-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-4 min-w-0">
                                    @php
                                        $pic = $user->profile_url ?? null;
                                        $initials = collect(explode(' ', trim($user->name ?? 'User')))
                                            ->filter()
                                            ->take(2)
                                            ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                                            ->implode('');
                                    @endphp

                                    @if($pic)
                                        <img src="{{ $pic }}" alt="Profile"
                                             class="h-14 w-14 rounded-2xl object-cover border border-slate-200 bg-white flex-none">
                                    @else
                                        <div class="h-14 w-14 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-sm font-extrabold text-slate-600 flex-none">
                                            {{ $initials ?: 'U' }}
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <p class="text-base font-extrabold text-slate-900 truncate">{{ $user->name ?? '—' }}</p>
                                        <p class="text-sm text-slate-500 truncate">
                                            รหัส: <span class="font-semibold text-slate-700">{{ $user->university_id ?? '—' }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500">คณะ</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $user->faculty?->label() ?? $user->faculty ?? '—' }}</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500">ภาควิชา</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $user->department?->label() ?? $user->department ?? '—' }}</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500">อีเมล</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900 break-all">{{ $user->email ?? '—' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
