@extends('layouts.main')

@section('content')
    <section class="bg-background min-h-screen">
        <div class="container mx-auto w-[90%] lg:w-[80%] py-10">

            {{-- ส่วนหัว (Header) --}}
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">แก้ไขใบสมัคร #{{ $application->id }}</h1>
                <a href="{{ route('applications.show', $application) }}" class="text-sm font-semibold text-slate-500 hover:text-primary transition">
                    &larr; กลับไปหน้ารายละเอียด
                </a>
            </div>

            {{-- ข้อความแจ้งเตือนสำเร็จ --}}
            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- ข้อความแจ้งเตือนข้อผิดพลาด (Validation Errors) --}}
            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5 shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                        <h3 class="text-sm font-bold text-red-800">กรุณาแก้ไขข้อผิดพลาดต่อไปนี้:</h3>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-700 ml-8">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ข้อมูลภาพรวม (Overview Card) --}}
            <div class="bg-slate-50 p-6 md:p-8 rounded-2xl mb-8 border border-slate-200 shadow-sm">
                <h2 class="text-lg font-bold mb-4 border-b border-slate-200 pb-3 text-slate-800">ข้อมูลภาพรวมของใบสมัคร</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <span class="block text-xs uppercase tracking-wider text-slate-500 font-bold mb-1">ผู้สมัคร</span>
                        <span class="font-semibold text-slate-900">{{ $application->user->name ?? 'ไม่ได้ระบุ' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs uppercase tracking-wider text-slate-500 font-bold mb-1">ประเภทรางวัล</span>
                        <span class="font-semibold text-slate-900">{{ $application->applicationCategory?->name ?? 'ไม่ได้ระบุ' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs uppercase tracking-wider text-slate-500 font-bold mb-1">รอบการรับสมัคร</span>
                        <span class="font-semibold text-slate-900">
                            ปีการศึกษา {{ $application->applicationRound?->thai_academic_year ?? ($application->applicationRound?->academic_year ? $application->applicationRound->academic_year + 543 : '—') }}
                            (ภาคการศึกษา{{ $application->applicationRound?->semester?->label() ?? '—' }})
                        </span>
                    </div>
                </div>
            </div>

            {{-- ฟอร์มแก้ไข (Edit Form) --}}
            <form action="{{ route('applications.update', $application) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200 space-y-8">
                @csrf
                @method('PUT')

                {{-- ส่วนที่ 1: สถานะและเหตุผล --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold text-sm text-slate-700 mb-2">สถานะการพิจารณา <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full rounded-xl border @error('status') border-red-500 @else border-slate-300 @enderror p-3 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition">
                            @foreach(\App\Enums\ApplicationStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ old('status', $application->status->value) === $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-sm text-slate-700 mb-2">บันทึกการพิจารณา / เหตุผล (ถ้ามี)</label>
                        <textarea name="rejection_reason" rows="2" class="w-full rounded-xl border @error('rejection_reason') border-red-500 @else border-slate-300 @enderror p-3 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition" placeholder="อธิบายเหตุผลเพิ่มเติมหากมีการปฏิเสธ หรือแนบบันทึกย่อ...">{{ old('rejection_reason', $application->rejection_reason) }}</textarea>
                        @error('rejection_reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- ส่วนที่ 2: คำตอบจากฟอร์มแบบไดนามิก --}}
                <div>
                    <h3 class="text-lg font-bold mb-4 text-slate-800">ข้อมูลคำตอบจากฟอร์มรับสมัคร</h3>
                    <div class="space-y-4">
                        @forelse($application->attributeValues as $answer)
                            <div class="p-4 bg-slate-50/50 rounded-xl border border-slate-200 transition hover:border-slate-300">
                                <label class="block font-bold text-sm text-slate-700 mb-2">
                                    {{ $answer->attribute?->label ?? 'ฟิลด์กำหนดเอง' }}
                                </label>

                                @if($answer->attribute?->type === 'file')
                                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                                        @if($answer->value)
                                            <a href="{{ asset('storage/' . $answer->value) }}" target="_blank" class="inline-flex items-center gap-2 text-primary text-xs font-bold hover:underline border border-primary/20 px-4 py-2 rounded-lg bg-primary/5">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                ไฟล์เอกสารปัจจุบัน
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400 italic">ยังไม่มีการแนบไฟล์</span>
                                        @endif
                                        <div class="flex-grow">
                                            <input type="file" name="values[{{ $answer->id }}]" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300 transition cursor-pointer">
                                            <p class="text-[10px] text-slate-400 mt-1">อัปโหลดไฟล์ใหม่เพื่อแทนที่ไฟล์เดิม (ถ้ามี)</p>
                                            @error("values.$answer->id") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                @elseif($answer->attribute?->type === 'textarea')
                                    <textarea name="values[{{ $answer->id }}]" rows="3" class="w-full rounded-xl border @error("values.$answer->id") border-red-500 @else border-slate-300 @enderror p-3 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition">{{ old('values.'.$answer->id, $answer->value) }}</textarea>
                                    @error("values.$answer->id") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                @else
                                    <input type="text" name="values[{{ $answer->id }}]" value="{{ old('values.'.$answer->id, $answer->value) }}" class="w-full rounded-xl border @error("values.$answer->id") border-red-500 @else border-slate-300 @enderror p-3 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition">
                                    @error("values.$answer->id") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 italic">ไม่มีข้อมูลแบบฟอร์มเพิ่มเติม</p>
                        @endforelse
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- ส่วนที่ 3: เอกสารแนบทั่วไป (Attachments) --}}
                <div>
                    <h3 class="text-lg font-bold mb-4 text-slate-800">เอกสารแนบประกอบการพิจารณา</h3>

                    {{-- ไฟล์ที่มีอยู่เดิม --}}
                    @if($application->attachments->count() > 0)
                        <div class="mb-6 space-y-3">
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-bold mb-2">ไฟล์ที่มีอยู่ในระบบ (ติ๊กถูกเพื่อลบไฟล์)</p>
                            @foreach($application->attachments as $attachment)
                                <div class="flex items-center p-3 border border-slate-200 rounded-xl hover:bg-red-50 hover:border-red-200 transition-colors group">
                                    <input type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}" id="delete_file_{{ $attachment->id }}" class="w-5 h-5 text-red-600 border-slate-300 rounded focus:ring-red-500 cursor-pointer">

                                    <label for="delete_file_{{ $attachment->id }}" class="ml-3 flex-grow flex items-center justify-between cursor-pointer">
                                        <span class="text-sm font-semibold text-slate-700 group-hover:text-red-700 transition-colors">{{ $attachment->file_name }}</span>
                                    </label>
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="text-xs text-primary font-bold hover:underline ml-4 whitespace-nowrap" onclick="event.stopPropagation();">
                                        เปิดดูไฟล์
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- อัปโหลดไฟล์ใหม่เพิ่มเติม --}}
                    <div class="bg-slate-50/50 p-6 rounded-xl border border-dashed border-slate-300 @error('new_attachments.*') border-red-400 @enderror text-center md:text-left md:flex md:items-center md:justify-between">
                        <div>
                            <label class="block font-bold text-sm text-slate-800 mb-1">อัปโหลดไฟล์แนบเพิ่มเติม</label>
                            <p class="text-xs text-slate-500 mb-3 md:mb-0">รองรับการอัปโหลดหลายไฟล์พร้อมกัน</p>
                        </div>
                        <div class="md:w-1/2">
                            <input type="file" name="new_attachments[]" multiple class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition cursor-pointer">
                            @error('new_attachments.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- ปุ่ม Action ด้านล่างสุด --}}
                <div class="pt-6 mt-6 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-slate-400">ข้อมูลการอัปเดตทั้งหมดจะถูกบันทึกเวลาไว้ในระบบ</p>
                    <button type="submit" class="w-full md:w-auto bg-primary text-white font-bold py-3 px-8 rounded-xl hover:opacity-90 transition shadow-sm">
                        บันทึกการแก้ไข
                    </button>
                </div>
            </form>

        </div>
    </section>
@endsection
