@extends('layouts.main')

@section('content')
    <section class="bg-background min-h-screen">
        <div class="container mx-auto w-[80%] md:w-[60%] py-8 space-y-8">

            {{-- ส่วนหัว (Header) พร้อมปุ่มแก้ไข --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <a href="{{ route('categories.index') }}" class="text-sm text-slate-500 hover:text-primary transition">&larr; กลับหน้ารวมประเภทรางวัล</a>
                    <h1 class="text-3xl font-extrabold text-slate-900 mt-2">รายละเอียด: {{ $category->name }}</h1>
                    <p class="text-slate-500 mt-1">ตรวจสอบข้อมูลและฟอร์มรับสมัครของประเภทรางวัลนี้</p>
                </div>

                {{-- ปุ่มแก้ไขข้อมูลพื้นฐาน (Edit Button) --}}
                <a href="{{ route('categories.edit', $category) }}"
                   class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-700 hover:text-primary hover:border-primary px-4 py-2 rounded-xl font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    แก้ไขข้อมูลพื้นฐาน
                </a>
            </div>

            {{-- รายการฟิลด์ข้อมูลรับสมัคร (Attributes) --}}
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-4">ฟิลด์ข้อมูลที่ต้องการเก็บ (ฟอร์มรับสมัคร)</h2>
                <ul class="space-y-3">
                    @forelse($category->attributes as $attribute)
                        <li class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm flex items-center justify-between hover:border-primary/30 transition">
                            <div>
                                <span class="font-bold text-slate-800">{{ $attribute->label }}</span>
                                @if($attribute->is_required)
                                    <span class="ml-2 text-[10px] bg-red-100 text-red-600 px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wide">
                                        จำเป็น
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs font-semibold px-3 py-1 bg-slate-100 text-slate-600 rounded-lg">
                                {{ match($attribute->type) {
                                    'text' => 'ข้อความสั้น',
                                    'textarea' => 'ข้อความยาว',
                                    'file' => 'ไฟล์อัปโหลด',
                                    default => $attribute->type
                                } }}
                            </span>
                        </li>
                    @empty
                        <li class="p-8 text-center bg-white border border-dashed border-slate-300 rounded-xl text-slate-500 font-medium">
                            ยังไม่มีการกำหนดฟิลด์ข้อมูลสำหรับประเภทรางวัลนี้
                        </li>
                    @endforelse
                </ul>
            </div>

            {{-- ปุ่มจัดการ (ลบ / เปลี่ยนสถานะ) --}}
            <div class="flex flex-wrap gap-4 pt-6 border-t border-slate-200">
                <form action="{{ route('categories.toggleStatus', $category) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="{{ $category->is_active ? 0 : 1 }}">

                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold shadow-sm transition hover:-translate-y-0.5 {{ $category->is_active ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-emerald-500 hover:bg-emerald-600 text-white' }}">
                        {{ $category->is_active ? 'ปิดการใช้งานประเภทนี้' : 'เปิดใช้งานประเภทนี้' }}
                    </button>
                </form>

                @if(!$category->hasApplications())
                    <form action="{{ route('categories.destroy', $category) }}" method="POST"
                          onsubmit="return confirm('ยืนยันการลบประเภทรางวัลนี้อย่างถาวรใช่หรือไม่?\n(การกระทำนี้ไม่สามารถย้อนกลับได้)');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-2.5 bg-white border-2 border-red-500 text-red-600 font-bold rounded-xl hover:bg-red-50 hover:-translate-y-0.5 transition shadow-sm">
                            ลบประเภทรางวัล
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </section>
@endsection
