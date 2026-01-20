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

        // FIXED: Roles now use the Enum cases for values
        $roles = [
            ['value' => \App\Enums\UserRole::USER->value, 'label' => 'Student', 'desc' => 'Applicant access'],
            ['value' => \App\Enums\UserRole::USER->value, 'label' => 'Staff',   'desc' => 'Reviewer access'],
            ['value' => \App\Enums\UserRole::ADMIN->value, 'label' => 'Admin',   'desc' => 'Full system access'],
        ];

        // FIXED: Using the standardized accessor from your Model
        $currentPhoto = $isEdit ? ($user->profile_url ?? null) : null;

        // FIXED: Ensures we handle the Enum or old value correctly
        $selectedRole = old('role', $isEdit ? $user->role->value : 'user');
    @endphp

    <section class="bg-background">
        <div class="container mx-auto w-[80%] md:w-[60%] py-6 space-y-6">

            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $isEdit ? 'Edit User Profile' : 'Create User Profile' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Manage student and staff excellence award participants.
                </p>
            </div>

            <form action="{{ $route }}" method="POST" enctype="multipart/form-data" class="{{ $card }} overflow-hidden">
                @csrf
                @method($method)

                {{-- Profile Photo --}}
                {{-- Profile Photo Section --}}
                <div class="{{ $pad }}">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-5">
                            <div class="relative">
                                <div class="h-20 w-20 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                    {{-- FIXED: Logic to show current photo or placeholder --}}
                                    @if($isEdit && $user->profile_path)
                                        <img src="{{ $user->profile_url }}" class="h-full w-full object-cover" alt="Profile">
                                    @else
                                        <svg class="h-8 w-8 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    @endif
                                </div>

                                {{-- FIXED: name="photo" and id="photo" --}}
                                <label for="photo" class="absolute -right-2 -bottom-2 w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center shadow-sm cursor-pointer hover:opacity-95">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8a2 2 0 012-2h3l2-2h4l2 2h3a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17a4 4 0 100-8 4 4 0 000 8z"/>
                                    </svg>
                                </label>
                                <input id="photo" name="photo" type="file" accept="image/*" class="hidden">
                            </div>

                            <div>
                                <div class="text-sm font-semibold text-gray-900">Profile Photo</div>
                                <p class="mt-1 text-xs {{ $muted }} max-w-md">
                                    Upload a high-resolution headshot. Supports JPG, PNG. Max 2MB.
                                </p>

                                <div class="mt-3 flex items-center gap-2">
                                    <label for="photo" class="inline-flex items-center justify-center rounded-lg bg-primary text-white px-4 py-2 text-xs font-semibold shadow-sm cursor-pointer hover:opacity-95">
                                        Upload New Image
                                    </label>

                                    @if($isEdit && $user->profile_path)
                                        <label class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-200 cursor-pointer">
                                            <input type="checkbox" name="remove_photo" value="1" class="rounded border-gray-300">
                                            Remove
                                        </label>
                                    @endif
                                </div>

                                {{-- FIXED: Error matches name="photo" --}}
                                @error('photo')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-px bg-gray-200"></div>

                {{-- Body --}}
                <div class="{{ $pad }} space-y-8">
                    {{-- 1. Basic Info --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">1</div>
                            <h2 class="text-base {{ $title }}">Basic Information</h2>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Full Name</label>
                                <input name="name" type="text" class="{{ $input }} mt-2"
                                       value="{{ old('name', $user->name ?? '') }}">
                                @error('name') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">University Email</label>
                                    <input name="email" type="email" class="{{ $input }} mt-2"
                                           value="{{ old('email', $user->email ?? '') }}">
                                    @error('email') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">Student / Staff ID</label>
                                    <input name="university_id" type="text" class="{{ $input }} mt-2"
                                           value="{{ old('university_id', $user->university_id ?? '') }}">
                                    @error('university_id') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Role & Access --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">2</div>
                            <h2 class="text-base {{ $title }}">Role &amp; Access</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($roles as $r)
                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="{{ $r['value'] }}" class="sr-only peer"
                                        {{ $selectedRole === $r['value'] ? 'checked' : '' }}>
                                    <div class="w-full text-left border rounded-2xl p-4 bg-white shadow-sm transition
                                                border-gray-200 hover:border-primary/60
                                                peer-checked:border-primary peer-checked:bg-primary/10">
                                        <div class="flex items-start gap-3">
                                            <div class="text-sm font-semibold text-gray-900">{{ $r['label'] }}</div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- 3. Organization --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">3</div>
                            <h2 class="text-base {{ $title }}">Organization</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Faculty</label>
                                <select id="faculty" name="faculty" class="{{ $input }} mt-2">
                                    <option value="">Select faculty</option>
                                    @foreach($faculties as $f)
                                        <option value="{{ $f->value }}"
                                            {{ old('faculty', $isEdit ? $user->faculty?->value : '') === $f->value ? 'selected' : '' }}>
                                            {{ $f->value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('faculty') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Department</label>
                                <select id="department" name="department" class="{{ $input }} mt-2" disabled>
                                    <option value="">Select faculty first</option>
                                </select>
                                @error('department') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex justify-end">
                        <button type="submit" class="rounded-lg bg-primary text-white px-5 py-2 text-sm font-semibold shadow-sm">
                            {{ $isEdit ? 'Update User Profile' : 'Save User Profile' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Faculty & Department Logic ---
            const facultySelect = document.getElementById('faculty');
            const deptSelect = document.getElementById('department');

            async function loadDepartments(selectedFaculty, selectedDept = '') {
                if (!selectedFaculty) {
                    deptSelect.innerHTML = '<option value="">Select faculty first</option>';
                    deptSelect.disabled = true;
                    return;
                }

                const res = await fetch(`/api/departments?faculty=${encodeURIComponent(selectedFaculty)}`);
                const data = await res.json();

                deptSelect.innerHTML = '<option value="">Select department</option>';
                data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.value;
                    opt.textContent = d.label;
                    if (selectedDept === d.value) opt.selected = true;
                    deptSelect.appendChild(opt);
                });
                deptSelect.disabled = false;
            }

            facultySelect.addEventListener('change', () => loadDepartments(facultySelect.value));

            const initialFaculty = facultySelect.value;
            const initialDept = @json(old('department', $isEdit ? $user->department?->value : ''));
            if (initialFaculty) loadDepartments(initialFaculty, initialDept);

            // --- Photo Preview Logic ---
            const photoInput = document.getElementById('photo');

            // We only run this if the photo input exists on the page
            if (photoInput) {
                photoInput.addEventListener('change', function(e) {
                    const file = this.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    // Select the image or the svg inside the relative container
                    const container = this.closest('.relative').querySelector('.overflow-hidden');
                    let imgElement = container.querySelector('img') || container.querySelector('svg');

                    reader.onload = function(e) {
                        if (imgElement.tagName.toLowerCase() === 'svg') {
                            const newImg = document.createElement('img');
                            newImg.src = e.target.result;
                            newImg.className = "h-full w-full object-cover";
                            imgElement.parentNode.replaceChild(newImg, imgElement);
                        } else {
                            imgElement.src = e.target.result;
                        }
                    }
                    reader.readAsDataURL(file);
                });
            }
        });
    </script>
@endsection
