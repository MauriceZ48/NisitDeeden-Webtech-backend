@extends('layouts.main')

@section('content')
    <section class="bg-background min-h-screen">
        <div class="container mx-auto w-[80%] md:w-[60%] py-8 space-y-8">

            {{-- ส่วนหัว (Header) --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900">จัดการประเภทรางวัล</h1>
                    <p class="text-slate-500 mt-1">เพิ่ม ลบ หรือแก้ไขประเภทรางวัล</p>
                </div>
                <a href="{{ route('categories.create') }}" class="bg-primary text-white px-5 py-2.5 rounded-xl font-bold shadow-sm hover:opacity-90 transition">
                    + สร้างประเภทรางวัล
                </a>
            </div>

            {{-- ตารางการ์ดประเภทรางวัล (Categories Grid) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($categories as $category)
                    <a href="{{ route('categories.show', $category) }}" class="block group">
                        <div class="relative flex flex-col items-center text-center p-6 rounded-2xl border transition-all duration-300
                            {{ $category->is_active
                                ? 'bg-white border-slate-200 shadow-sm group-hover:shadow-md group-hover:border-primary/50'
                                : 'bg-slate-50 border-slate-200 opacity-60 grayscale group-hover:grayscale-0 group-hover:opacity-100' }}">


                            {{-- ไอคอน (Icon) --}}
                            <div class="text-primary mb-3 {{ $category->is_active ? '' : 'text-slate-400' }}">
                                <i data-lucide="{{ str_replace('lucide:', '', $category->icon) }}" class="w-12 h-12"></i>
                            </div>

                            {{-- เนื้อหา (Content) --}}
                            <h2 class="text-lg font-bold text-slate-900 mb-2">{{ $category->name }}</h2>
                            <p class="text-sm text-slate-500 line-clamp-3">{{ $category->description }}</p>

                        </div>
                    </a>
                @empty
                    {{-- กรณีที่ยังไม่มีข้อมูล (Empty State) --}}
                    <div class="col-span-full p-12 text-center bg-white border-2 border-dashed border-slate-300 rounded-2xl">
                        <p class="text-slate-500 font-medium">ยังไม่มีข้อมูลประเภทรางวัล คลิกปุ่ม "+ สร้างประเภทรางวัล" เพื่อเริ่มต้นใช้งาน</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
@endsection
