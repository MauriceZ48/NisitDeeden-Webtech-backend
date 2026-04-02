@extends('layouts.main')

@section('content')
    @php
        $isEdit = isset($user) && $user?->exists;

        $route  = $isEdit ? route('users.update', $user) : route('users.store');
        $method = $isEdit ? 'PUT' : 'POST';

        $card  = 'bg-white border border-gray-200 rounded-2xl shadow-sm';
        $pad   = 'p-6';
        $title = 'text-gray-900 font-semibold';
        $muted = 'text-gray-500';

        $input = 'w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm
                  focus:ring-2 focus:ring-primary focus:border-primary';

        $currentPhoto = $isEdit ? ($user->profile_url ?? null) : null;
    @endphp

    <section class="bg-background">
        <div class="container mx-auto w-[80%] md:w-[60%] py-6 space-y-6">

            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $isEdit ? 'แก้ไขข้อมูลผู้ใช้งาน' : 'เพิ่มผู้ใช้งานใหม่' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    จัดการข้อมูลส่วนตัว สิทธิ์การเข้าถึง และสังกัดของผู้ใช้งานในระบบ
                </p>
            </div>

            <form action="{{ $route }}" method="POST" enctype="multipart/form-data" class="{{ $card }} overflow-hidden">
                @csrf
                @method($method)

                {{-- ส่วนรูปโปรไฟล์ --}}
                <div class="{{ $pad }}">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-5">
                            <div class="relative">
                                <div id="photoPreviewBox"
                                     class="h-20 w-20 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                    @if($isEdit && $user->profile_path)
                                        <img src="{{ $user->profile_url }}" class="h-full w-full object-cover"
                                             alt="Profile">
                                    @else
                                        <svg class="h-8 w-8 text-gray-400" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    @endif
                                </div>

                                <label for="photo"
                                       class="absolute -right-2 -bottom-2 w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center shadow-sm cursor-pointer hover:opacity-95">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 8a2 2 0 012-2h3l2-2h4l2 2h3a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 17a4 4 0 100-8 4 4 0 000 8z"/>
                                    </svg>
                                </label>
                                <input id="photo" name="photo" type="file" accept="image/*" class="hidden">
                            </div>

                            <div>
                                <div class="text-sm font-semibold text-gray-900">รูปประจำตัว</div>
                                <p class="mt-1 text-xs {{ $muted }} max-w-md">
                                    อัปโหลดรูปภาพที่เห็นใบหน้าชัดเจน รองรับไฟล์ JPG, PNG ขนาดไม่เกิน 2MB
                                </p>

                                <div class="mt-3 flex items-center gap-2">
                                    <label for="photo"
                                           class="inline-flex items-center justify-center rounded-lg bg-primary text-white px-4 py-2 text-xs font-semibold shadow-sm cursor-pointer hover:opacity-95">
                                        อัปโหลดรูปภาพใหม่
                                    </label>

                                    <button type="button"
                                            id="remove-photo-btn"
                                            style="display: {{ ($isEdit && $user->profile_path) ? 'inline-flex' : 'none' }};"
                                            class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-4 py-2 text-xs font-semibold text-red-700 hover:bg-red-100 cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        ลบรูปภาพ
                                    </button>

                                    <input type="hidden" name="delete_photo" id="delete_photo_input" value="0">
                                </div>

                                @error('photo')
                                <div class="mt-2 p-2 bg-red-50 border border-red-100 rounded-lg">
                                    <p class="text-xs text-red-600 font-medium">
                                        ⚠️ ไม่สามารถบันทึกรูปภาพได้ กรุณาเลือกรูปใหม่อีกครั้ง
                                    </p>
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-px bg-gray-200"></div>

                {{-- ส่วนที่ 1: ข้อมูลพื้นฐาน --}}
                <div class="{{ $pad }} space-y-8">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">
                                1
                            </div>
                            <h2 class="text-base {{ $title }}">ข้อมูลพื้นฐาน</h2>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">ชื่อ-นามสกุล</label>
                                <input name="name" type="text" class="{{ $input }} mt-2"
                                       value="{{ old('name', $user->name ?? '') }}" placeholder="เช่น สมชาย ใจดี">
                                @error('name') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">อีเมลมหาวิทยาลัย</label>
                                    <input name="email" type="email" class="{{ $input }} mt-2"
                                           value="{{ old('email', $user->email ?? '') }}" placeholder="example@ku.th">
                                    @error('email') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">รหัสประจำตัวนิสิต / เจ้าหน้าที่</label>
                                    <input name="university_id" type="text" class="{{ $input }} mt-2"
                                           value="{{ old('university_id', $user->university_id ?? '') }}" placeholder="รหัสประจำตัว">
                                    @error('university_id') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ส่วนที่ 2: ตำแหน่งและสิทธิ์ --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">
                                2
                            </div>
                            <h2 class="text-base {{ $title }}">ตำแหน่งและบทบาท</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {{-- วนลูป UserPosition Enum เพื่อแสดงภาษาไทยจาก getRole() --}}
                            @foreach(\App\Enums\UserPosition::cases() as $pos)
                                <label class="cursor-pointer">
                                    <input type="radio" name="position" value="{{ $pos->value }}" class="sr-only peer"
                                        {{ old('position', $isEdit ? $user->position?->value : '') === $pos->value ? 'checked' : '' }}>
                                    <div class="w-full text-left border rounded-2xl p-4 bg-white shadow-sm transition
                            border-gray-200 hover:border-primary/60
                            peer-checked:border-primary peer-checked:bg-primary/10">
                                        <div class="text-sm font-semibold text-gray-900">{{ $pos->label() }}</div>
                                        <div class="text-[10px] text-gray-500 uppercase mt-1">
                                            สิทธิ์การใช้งาน: {{ $pos->getRole()->value === 'ADMIN' ? 'ผู้ดูแลระบบ' : ($pos->getRole()->value === 'STUDENT' ? 'ผู้ใช้งานทั่วไป' : 'คณะกรรมการ') }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('position') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- ส่วนที่ 3: สังกัดหน่วยงาน --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">
                                3
                            </div>
                            <h2 class="text-base {{ $title }}">สังกัดหน่วยงาน</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">คณะ</label>
                                {{-- 🌟 เพิ่ม disabled styling --}}
                                <select id="faculty" name="faculty" class="{{ $input }} mt-2 disabled:bg-gray-100 disabled:text-gray-400 disabled:border-gray-200 disabled:cursor-not-allowed transition-colors">
                                    <option value="">เลือกคณะ</option>
                                    @foreach($faculties as $f)
                                        <option value="{{ $f->value }}"
                                            {{ old('faculty', $isEdit ? $user->faculty?->value : '') === $f->value ? 'selected' : '' }}>
                                            {{ $f->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('faculty') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">ภาควิชา</label>
                                {{-- 🌟 เพิ่ม disabled styling --}}
                                <select id="department" name="department" class="{{ $input }} mt-2 disabled:bg-gray-100 disabled:text-gray-400 disabled:border-gray-200 disabled:cursor-not-allowed transition-colors" disabled>
                                    <option value="">กรุณาเลือกคณะก่อน</option>
                                </select>
                                @error('department') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ส่วนปุ่มยืนยัน --}}
                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex justify-end">
                        <button type="submit"
                                class="rounded-lg bg-primary text-white px-5 py-2 text-sm font-semibold shadow-sm hover:opacity-90 transition">
                            {{ $isEdit ? 'บันทึกการแก้ไข' : 'สร้างผู้ใช้งาน' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- ระบบจัดการ คณะ และ ภาควิชา ตามตำแหน่ง ---
            const facultySelect = document.getElementById('faculty');
            const deptSelect = document.getElementById('department');
            const positionRadios = document.querySelectorAll('input[name="position"]');

            // 🌟 ฟังก์ชันหลัก: อัปเดตสถานะการล็อกของ Dropdown
            function updateDropdownState() {
                const selectedPosition = document.querySelector('input[name="position"]:checked')?.value;

                // 🌟 อัปเดตชื่อให้ตรงกับ Enum ของคุณเป๊ะๆ
                const disableBoth = ['committee_member', 'staff'].includes(selectedPosition); // คณะกรรมการ, กองพัฒนานิสิต
                const disableDeptOnly = ['associate_dean', 'dean'].includes(selectedPosition); // รองคณบดี, คณบดี

                if (disableBoth) {
                    // ล็อกทั้งคู่
                    facultySelect.value = '';
                    facultySelect.disabled = true;

                    deptSelect.innerHTML = '<option value="">ไม่ต้องระบุ</option>';
                    deptSelect.disabled = true;

                } else if (disableDeptOnly) {
                    // ล็อกแค่ภาควิชา แต่เปิดให้เลือกคณะได้
                    facultySelect.disabled = false;

                    deptSelect.innerHTML = '<option value="">ไม่ต้องระบุภาควิชา</option>';
                    deptSelect.disabled = true;

                } else {
                    // ปกติ ('student', 'head_of_department') -> เปิดทั้งคู่
                    facultySelect.disabled = false;

                    if (facultySelect.value) {
                        // ถ้าเลือกคณะไว้แล้ว ให้โหลดภาควิชา
                        loadDepartments(facultySelect.value, deptSelect.value);
                    } else {
                        deptSelect.innerHTML = '<option value="">กรุณาเลือกคณะก่อน</option>';
                        deptSelect.disabled = true;
                    }
                }
            }

            // ฟังก์ชันโหลดข้อมูลภาควิชาจาก API
            async function loadDepartments(selectedFaculty, selectedDept = '') {
                const selectedPosition = document.querySelector('input[name="position"]:checked')?.value;

                // 🌟 อัปเดตดักไว้: ถ้าตำแหน่งถูกล็อกภาควิชาอยู่ ไม่ต้องไปยิง API
                if (['committee_member', 'staff', 'associate_dean', 'dean'].includes(selectedPosition)) {
                    return;
                }

                if (!selectedFaculty) {
                    deptSelect.innerHTML = '<option value="">กรุณาเลือกคณะก่อน</option>';
                    deptSelect.disabled = true;
                    return;
                }

                // ดึงข้อมูลภาควิชา
                const res = await fetch(`/api/departments?faculty=${encodeURIComponent(selectedFaculty)}`);
                const data = await res.json();

                deptSelect.innerHTML = '<option value="">เลือกภาควิชา</option>';
                data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.value;
                    opt.textContent = d.label;
                    if (selectedDept === d.value) opt.selected = true;
                    deptSelect.appendChild(opt);
                });
                deptSelect.disabled = false;
            }

            // จับ Event เมื่อผู้ใช้เปลี่ยนตำแหน่ง หรือเปลี่ยนคณะ
            positionRadios.forEach(radio => radio.addEventListener('change', updateDropdownState));
            facultySelect.addEventListener('change', () => loadDepartments(facultySelect.value));

            // ตั้งค่าเริ่มต้นตอนโหลดหน้าเว็บ
            const initialFaculty = facultySelect.value;
            const initialDept = @json(old('department', $isEdit ? $user->department?->value : ''));

            if (initialFaculty) {
                // รอโหลดข้อมูลภาควิชาเสร็จ แล้วค่อยอัปเดตสถานะล็อก เผื่อข้อมูลโดนทับ
                loadDepartments(initialFaculty, initialDept).then(() => {
                    updateDropdownState();
                });
            } else {
                updateDropdownState();
            }

            // --- ระบบแสดงตัวอย่างรูปภาพและลบรูป ---
            const photoInput = document.getElementById('photo');
            const removeBtn = document.getElementById('remove-photo-btn');
            const deleteInput = document.getElementById('delete_photo_input');
            const container = document.getElementById('photoPreviewBox');

            const placeholderSvg = `
            <svg class="h-8 w-8 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>`;

            photoInput?.addEventListener('change', function () {
                const file = this.files?.[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function (ev) {
                    container.innerHTML = `<img src="${ev.target.result}" class="h-full w-full object-cover" alt="Preview">`;
                    if (removeBtn) removeBtn.style.display = 'inline-flex';
                    if (deleteInput) deleteInput.value = "0";
                };
                reader.readAsDataURL(file);
            });

            removeBtn?.addEventListener('click', function () {
                photoInput.value = "";
                container.innerHTML = placeholderSvg;
                removeBtn.style.display = 'none';
                if (deleteInput) deleteInput.value = "1";
            });
        });
    </script>
@endsection
