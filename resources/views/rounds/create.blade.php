@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[60%] py-8 space-y-8">
            {{-- Header --}}
            <div>
                <a href="{{ route('rounds.index') }}" class="text-sm text-slate-500 hover:text-primary">&larr; กลับหน้ารวมรอบรับสมัคร</a>
                <h1 class="text-3xl font-extrabold text-slate-900 mt-2">สร้างรอบการรับสมัครใหม่</h1>
                <p class="text-slate-500">ตั้งค่าช่วงเวลาสำหรับเปิดรับผลงานนิสิตในปีการศึกษาถัดไป</p>
            </div>

            <form action="{{ route('rounds.store') }}" method="POST" class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-6">
                @csrf

                {{-- แถวที่ 1: ปีการศึกษา และ ภาคการศึกษา (อ่านได้อย่างเดียวเพื่อป้องกันการข้ามรอบ) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">ปีการศึกษา</label>
                        {{-- แสดงผลเป็นปี พ.ศ. ให้ยูสเซอร์อ่านง่าย (บวก 543) --}}
                        <input type="text" value="{{ $expectedYear + 543 }}" readonly
                               class="mt-1 block w-full rounded-lg border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed focus:ring-0">
                        {{-- แต่เวลาส่ง Request (Submit) เราซ่อนค่าตัวเลขปี ค.ศ. เอาไว้ให้ Database --}}
                        <input type="hidden" name="academic_year" value="{{ $expectedYear }}">
                        <p class="mt-1 text-xs text-slate-400 font-medium">ระบบตั้งค่าปีการศึกษาให้อัตโนมัติตามลำดับ</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700">ภาคการศึกษา</label>
                        {{-- ส่งค่า Value เข้าหลังบ้าน --}}
                        <input type="hidden" name="semester" value="{{ $expectedSemester->value }}">
                        {{-- แสดง Label ภาษาไทยหน้าบ้าน --}}
                        <input type="text" value="ภาคการศึกษา{{ $expectedSemester->label() }}" readonly
                               class="mt-1 block w-full rounded-lg border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                    </div>
                </div>

                {{-- แถวที่ 2: เลือกสถานะเริ่มต้น --}}
                <div class="w-full">
                    <label for="status" class="block text-sm font-semibold text-slate-700">สถานะการรับสมัคร</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20">
                        <option value="{{ \App\Enums\RoundStatus::DRAFT->value }}">ฉบับร่าง (ยังไม่เปิดให้นิสิตเห็น)</option>
                        <option value="{{ \App\Enums\RoundStatus::OPEN->value }}">เปิดรับสมัคร (เผยแพร่ทันที)</option>
                    </select>
                    @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- แถวที่ 3: ปฏิทินเลือกวันและเวลา --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_time" class="block text-sm font-semibold text-slate-700">วันและเวลาที่เริ่มต้น</label>
                        <input type="datetime-local" name="start_time" id="start_time" required
                               value="{{ old('start_time') }}"
                               class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20">
                        @error('start_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-semibold text-slate-700">วันและเวลาที่สิ้นสุด</label>
                        <input type="datetime-local" name="end_time" id="end_time" required
                               value="{{ old('end_time') }}"
                               class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20">
                        @error('end_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- แจ้งเตือน Error (กรณีที่ยังมีรอบรับสมัครอื่นเปิดอยู่) --}}
                @if($errors->has('academic_year'))
                    <div class="p-4 bg-red-50 border border-red-100 rounded-xl text-red-700 text-sm">
                        {{ $errors->first('academic_year') }}
                    </div>
                @endif

                {{-- ปุ่มยืนยัน --}}
                <div class="pt-4">
                    <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:opacity-90 transition-opacity shadow-sm">
                        บันทึกการสร้างรอบรับสมัคร
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
