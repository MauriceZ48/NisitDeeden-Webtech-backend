@extends('layouts.main')

@section('content')
    <section class="bg-background min-h-screen">
        <div class="container mx-auto w-[80%] md:w-[60%] py-8 space-y-8">

            {{-- ส่วนหัว (Header) --}}
            <div>
                <a href="{{ route('categories.show', $category) }}" class="text-sm text-slate-500 hover:text-primary transition">&larr; กลับหน้ารายละเอียด</a>
                <h1 class="text-3xl font-extrabold text-slate-900 mt-2">แก้ไขประเภทรางวัล</h1>
                <p class="text-slate-500 mt-1">อัปเดตชื่อ ไอคอน และคำอธิบายสำหรับประเภทรางวัลนี้</p>
            </div>

            {{-- ฟอร์มแก้ไขข้อมูล --}}
            <form action="{{ route('categories.update', $category) }}" method="POST" class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-6">
                @csrf
                @method('PUT')

                {{-- ชื่อประเภทรางวัล --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700">
                        ชื่อประเภทรางวัล <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                           value="{{ old('name', $category->name) }}"
                           placeholder="เช่น ด้านกิจกรรมนิสิต"
                           class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20 transition">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- ไอคอน --}}
                <div>
                    <label for="icon" class="block text-sm font-semibold text-slate-700">ไอคอน (Icon)</label>
                    <input type="text" name="icon" id="icon"
                           value="{{ old('icon', $category->icon) }}"
                           placeholder="เช่น lucide:award"
                           class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20 transition">
                    <p class="mt-1 text-xs text-slate-400 font-medium">ระบุชื่อคลาสหรือรหัสไอคอน (เช่น lucide:lightbulb, lucide:shield-check)</p>
                    @error('icon') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- คำอธิบาย --}}
                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-700">คำอธิบาย</label>
                    <textarea name="description" id="description" rows="4"
                              placeholder="อธิบายเกณฑ์หรือคุณสมบัติคร่าวๆ สำหรับประเภทรางวัลนี้..."
                              class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20 transition">{{ old('description', $category->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- ปุ่มดำเนินการ --}}
                <div class="pt-6 mt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                    <button type="submit" class="flex-1 bg-primary text-white font-bold py-3 rounded-xl hover:opacity-90 transition-opacity shadow-sm">
                        บันทึกการแก้ไข
                    </button>
                    <a href="{{ route('categories.show', $category) }}" class="px-6 py-3 text-sm font-semibold text-slate-600 hover:text-slate-900 transition text-center">
                        ยกเลิก
                    </a>
                </div>
            </form>

        </div>
    </section>
@endsection
