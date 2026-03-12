@extends('layouts.main')

@section('content')
    <section class="bg-background min-h-screen">
        <div class="container mx-auto w-[80%] md:w-[60%] py-8 space-y-8">

            {{-- ส่วนหัว (Header) เพิ่มเข้ามาเพื่อให้ UI เป็นมาตรฐานเดียวกัน --}}
            <div>
                <a href="{{ route('categories.index') }}" class="text-sm text-slate-500 hover:text-primary transition">&larr; กลับหน้ารวมประเภทรางวัล</a>
                <h1 class="text-3xl font-extrabold text-slate-900 mt-2">สร้างประเภทรางวัลใหม่</h1>
                <p class="text-slate-500 mt-1">กำหนดชื่อและสร้างฟอร์มรับสมัครแบบไดนามิกสำหรับประเภทรางวัลนี้</p>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-6">
                @csrf

                {{-- ส่วนที่ 1: ข้อมูลพื้นฐาน --}}
                <div class="space-y-6 bg-white p-6 rounded-xl shadow-sm border border-slate-100">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">ชื่อประเภทรางวัล <span class="text-red-500">*</span></label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name') }}"
                               placeholder="เช่น ด้านวิชาการดีเด่น"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all
               @error('name') border-red-500 @else border-slate-300 @enderror">
                        @error('name')
                        <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="icon" class="block text-sm font-medium text-slate-700 mb-1">ไอคอน (Lucide Icon)</label>
                        <div class="relative">
                            <input type="text"
                                   name="icon"
                                   id="icon"
                                   value="{{ old('icon') }}"
                                   placeholder="เช่น lucide:award"
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all
                   @error('icon') border-red-500 @enderror">
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="info" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <p class="text-slate-400 text-[10px] mt-1">ใช้รูปแบบ 'lucide:ชื่อไอคอน' (เช่น lucide:star)</p>
                        @error('icon')
                        <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1">คำอธิบาย</label>
                        <textarea name="description"
                                  id="description"
                                  rows="3"
                                  placeholder="อธิบายเกณฑ์หรือคุณสมบัติคร่าวๆ สำหรับประเภทรางวัลนี้..."
                                  class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all
                  @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ส่วนที่ 2: สร้างฟอร์มแบบไดนามิก (Alpine.js) --}}
                <div x-data="{
                    attributes: {{ Js::from(old('attributes', [])) }},
                    errors: {{ Js::from($errors->toArray()) }}, // 🌟 1. Added errors object
                    init() {
                        this.$watch('attributes', (value) => {
                            value.forEach(attr => {
                                if (attr.type === 'file') {
                                    attr.is_required = true;
                                }
                            });
                        }, { deep: true });
                    },
                    // 🌟 2. Added clearError function
                    clearError(field, index) {
                        let key = `attributes.${index}.${field}`;
                        if (this.errors[key]) {
                            delete this.errors[key];
                        }
                    }
                }">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-slate-800">ฟิลด์ข้อมูลที่ต้องการให้ผู้สมัครกรอก</h3>
                    </div>

                    <template x-for="(attr, index) in attributes" :key="index">
                        <div class="grid grid-cols-12 gap-4 items-start bg-slate-50 p-4 rounded-xl border border-slate-200 mb-4 w-full">

                            {{-- 1. ชื่อฟิลด์ --}}
                            <div class="col-span-12 md:col-span-6">
                                <label class="flex items-center gap-1 text-sm font-medium text-slate-700 mb-1">
                                    <span class="whitespace-nowrap">ชื่อฟิลด์ (Label)</span>
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       :name="`attributes[${index}][label]`"
                                       x-model="attr.label"
                                       @input="clearError('label', index)"
                                       class="w-full px-4 h-11 rounded-lg shadow-sm focus:border-primary focus:ring-primary/20 outline-none transition-all"
                                       :class="errors[`attributes.${index}.label`] ? 'border-red-500' : 'border-slate-300'"
                                       placeholder="เช่น ทรานสคริปต์, เกรดเฉลี่ย">
                            </div>

                            {{-- 2. ประเภทข้อมูล --}}
                            <div class="col-span-12 md:col-span-4">
                                <label class="block text-sm font-medium text-slate-700 mb-1 whitespace-nowrap">ประเภทข้อมูล</label>
                                <select :name="`attributes[${index}][type]`"
                                        x-model="attr.type"
                                        @change="clearError('type', index)"
                                        class="w-full px-3 h-11 rounded-lg shadow-sm focus:border-primary focus:ring-primary/20 outline-none transition-all"
                                        :class="errors[`attributes.${index}.type`] ? 'border-red-500' : 'border-slate-300'">
                                    <option value="text">ข้อความสั้น</option>
                                    <option value="textarea">ข้อความยาว</option>
                                    <option value="file">อัปโหลดไฟล์</option>
                                </select>
                            </div>

                            {{-- 3. บังคับกรอก --}}
                            <div class="col-span-6 md:col-span-1 flex flex-col items-center">
                                <label class="block text-[11px] font-bold text-slate-500 mb-1 whitespace-nowrap">จำเป็น?</label>
                                <div class="h-11 flex items-center justify-center">
                                    <input type="checkbox"
                                           :name="`attributes[${index}][is_required]`"
                                           x-model="attr.is_required"
                                           :disabled="attr.type === 'file'"
                                           class="w-5 h-5 text-primary border-slate-300 rounded focus:ring-primary/50 disabled:opacity-50"
                                           value="1">
                                </div>
                            </div>

                            {{-- 4. ปุ่มลบฟิลด์ --}}
                            <div class="col-span-6 md:col-span-1 mt-5">
                                <button type="button" @click="attributes.splice(index, 1)"
                                        class="w-full h-11 flex items-center justify-center text-red-400 hover:text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- ปุ่มเพิ่มฟิลด์ --}}
                    <button type="button" @click="attributes.push({label: '', type: 'text', is_required: false})"
                            class="mt-2 inline-flex items-center bg-slate-800 text-white px-5 py-2.5 rounded-xl hover:bg-slate-700 transition-colors shadow-sm font-medium">
                        <span class="mr-2 text-xl leading-none">+</span> เพิ่มฟิลด์ข้อมูล
                    </button>
                </div>

                {{-- ปุ่มยืนยันการสร้าง --}}
                <div class="pt-6 border-t border-slate-100">
                    <button type="submit" class="w-full bg-primary text-white font-bold py-3.5 rounded-xl hover:opacity-90 transition-opacity shadow-sm text-lg">
                        บันทึกการสร้างประเภทรางวัล
                    </button>
                </div>

            </form>
        </div>
    </section>
@endsection
