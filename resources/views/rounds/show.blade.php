@extends('layouts.main')

@section('content')
    <section class="bg-slate-50 min-h-screen py-12">
        <div class="container mx-auto max-w-3xl">

            {{-- ส่วนหัว (Header) --}}
            <div class="text-center mb-12">
                <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-widest rounded-full mb-4">
                    ประกาศผลผู้ได้รับรางวัล
                </span>
                <h1 class="text-3xl font-extrabold text-slate-900">
                    ปีการศึกษา {{ $applicationRound->thai_academic_year }}
                </h1>
                <p class="text-slate-500 mt-2 font-medium">ภาคการศึกษา{{ $applicationRound->semester->label() }}</p>
                <div class="mt-4 h-1.5 w-16 bg-indigo-600 mx-auto rounded-full"></div>
            </div>

            {{-- ส่วนแสดงหมวดหมู่รางวัล (Categories) --}}
            <div class="space-y-8">
                @foreach($categories as $category)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                        {{-- หัวข้อหมวดหมู่ (Category Header) --}}
                        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                            <h2 class="text-lg font-bold text-slate-800">
                                {{ $category->name }}
                            </h2>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                ประเภทรางวัล
                            </span>
                        </div>

                        {{-- รายชื่อนิสิต (Students List) --}}
                        <div class="p-6">
                            <ul class="divide-y divide-slate-100">
                                @forelse($category->applications as $index => $app)
                                    <li class="py-4 flex items-center justify-between group">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-400">
                                                {{ $index + 1 }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                                                    {{ $app->user->name }}
                                                </p>
                                                <p class="text-xs text-slate-500 font-medium">
                                                    คณะ{{ $app->user->faculty?->label() }}
                                                </p>
                                            </div>
                                        </div>

                                        {{-- ป้ายสถานะการตรวจสอบ (Verification Badge) --}}
                                        <div class="flex items-center gap-1.5 text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-[10px] font-black uppercase tracking-tighter">ผ่านการตรวจสอบ</span>
                                        </div>
                                    </li>
                                @empty
                                    {{-- กรณีที่ยังไม่มีคนได้รางวัลในหมวดหมู่นี้ --}}
                                    <li class="py-10 text-center">
                                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-50 mb-3">
                                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <p class="text-slate-400 font-medium text-sm">ยังไม่มีรายชื่อผู้ได้รับรางวัลในประเภทนี้</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- System Footer  --}}
            {{--            <div class="mt-16 text-center border-t border-slate-200 pt-8">--}}
            {{--                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">--}}
            {{--                    ระบบจัดการรางวัลนิสิตดีเด่น--}}
            {{--                </p>--}}
            {{--            </div>--}}
        </div>
    </section>
@endsection
