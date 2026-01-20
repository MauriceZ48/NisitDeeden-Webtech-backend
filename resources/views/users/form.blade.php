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

        $roles = [
            ['value' => 'USER', 'label' => 'Student', 'desc' => 'Applicant access'],
            ['value' => 'USER',   'label' => 'Staff',   'desc' => 'Reviewer access'],
            ['value' => 'ADMIN',   'label' => 'Admin',   'desc' => 'Full system access'],
        ];

        // show existing picture when edit
        $currentPhoto = $isEdit ? ($user->profile_picture_url ?? null) : null; // adjust accessor name to your model

        $selectedRole = old('role', $user->role ?? 'student');
    @endphp

    <section class="bg-background">
        <div class="container mx-auto w-[80%] md:w-[60%] py-6 space-y-6">

            {{-- Header --}}
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
                <div class="{{ $pad }}">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-5">
                            {{-- avatar box --}}
                            <div class="relative">
                                <div class="h-20 w-20 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                    @if($currentPhoto)
                                        <img src="{{ $currentPhoto }}" class="h-full w-full object-cover" alt="Profile">
                                    @else
                                        <svg class="h-8 w-8 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    @endif
                                </div>

                                {{-- small camera button --}}
                                <label for="profile_picture"
                                       class="absolute -right-2 -bottom-2 w-9 h-9 rounded-xl bg-primary text-white
                                          flex items-center justify-center shadow-sm cursor-pointer hover:opacity-95">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 8a2 2 0 012-2h3l2-2h4l2 2h3a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 17a4 4 0 100-8 4 4 0 000 8z"/>
                                    </svg>
                                </label>

                                <input id="profile_picture" name="profile_picture" type="file" accept="image/*" class="hidden">
                            </div>

                            <div>
                                <div class="text-sm font-semibold text-gray-900">Profile Photo</div>
                                <p class="mt-1 text-xs {{ $muted }} max-w-md">
                                    Upload a high-resolution headshot for official award documentation and faculty display.
                                </p>

                                <div class="mt-3 flex items-center gap-2">
                                    <label for="profile_picture"
                                           class="inline-flex items-center justify-center rounded-lg bg-primary text-white
                                              px-4 py-2 text-xs font-semibold shadow-sm cursor-pointer hover:opacity-95">
                                        Upload New Image
                                    </label>

                                    @if($isEdit && $currentPhoto)
                                        <label class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-200 cursor-pointer">
                                            <input type="checkbox" name="remove_profile_picture" value="1" class="rounded border-gray-300">
                                            Remove
                                        </label>
                                    @endif
                                </div>

                                @error('profile_picture')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>

                <div class="h-px bg-gray-200"></div>

                {{-- Body --}}
                <div class="{{ $pad }} space-y-8">

                    {{-- Basic Information --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">1</div>
                            <h2 class="text-base {{ $title }}">Basic Information</h2>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Full Name</label>
                                <input name="name" type="text" class="{{ $input }} mt-2"
                                       value="{{ old('name', $user->name ?? '') }}"
                                       placeholder="e.g. Alexander Hamilton">
                                @error('name') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">University Email</label>
                                    <input name="email" type="email" class="{{ $input }} mt-2"
                                           value="{{ old('email', $user->email ?? '') }}"
                                           placeholder="name@university.edu">
                                    @error('email') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-gray-600">Student / Staff ID</label>
                                    <input name="student_staff_id" type="text" class="{{ $input }} mt-2"
                                           value="{{ old('student_staff_id', $user->student_staff_id ?? '') }}"
                                           placeholder="ID-882910">
                                    @error('student_staff_id') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Role & Access (FIXED with peer-checked) --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">2</div>
                            <h2 class="text-base {{ $title }}">Role &amp; Access</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($roles as $r)
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="role"
                                        value="{{ $r['value'] }}"
                                        class="sr-only peer"
                                        {{ $selectedRole === $r['value'] ? 'checked' : '' }}
                                    >

                                    <div class="w-full text-left border rounded-2xl p-4 bg-white shadow-sm transition
                                                border-gray-200 hover:border-primary/60
                                                peer-checked:border-primary peer-checked:bg-primary/10">
                                        <div class="flex items-start gap-3">
                                            <div class="mt-1 w-4 h-4 rounded-full border flex items-center justify-center border-gray-300
                                                        peer-checked:border-primary">
                                                <div class="w-2 h-2 rounded-full bg-transparent peer-checked:bg-primary"></div>
                                            </div>

                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $r['label'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $r['desc'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @error('role') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Organization --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">3</div>
                            <h2 class="text-base {{ $title }}">Organization</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Faculty --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Faculty</label>
                                <select id="faculty" name="faculty" class="{{ $input }} mt-2">
                                    <option value="">Select faculty</option>
                                    @foreach($faculties as $f)
                                        <option value="{{ $f->value }}"
                                            {{ old('faculty', $user->faculty ?? '') === $f->value ? 'selected' : '' }}>
                                            {{ $f->value }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('faculty')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Department --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Department</label>
                                <select id="department" name="department" class="{{ $input }} mt-2" disabled>
                                    <option value="">Select faculty first</option>
                                </select>

                                @error('department')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Bottom actions --}}
                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('users.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>

                        <div class="flex items-center gap-2 justify-end">
                            <button type="submit" name="action" value="save"
                                    class="inline-flex items-center justify-center rounded-lg bg-primary text-white
                                       px-5 py-2 text-sm font-semibold shadow-sm hover:opacity-95">
                                Save User Profile
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </section>

    {{-- Faculty -> Department --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const facultySelect = document.getElementById('faculty');
            const deptSelect = document.getElementById('department');

            async function loadDepartments(selectedFaculty, selectedDept = '') {
                deptSelect.innerHTML = '<option value="">Loading...</option>';
                deptSelect.disabled = true;

                if (!selectedFaculty) {
                    deptSelect.innerHTML = '<option value="">Select faculty first</option>';
                    return;
                }

                const res = await fetch(`/api/departments?faculty=${encodeURIComponent(selectedFaculty)}`);
                const data = await res.json();

                deptSelect.innerHTML = '<option value="">Select department</option>';

                data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.value;
                    opt.textContent = d.label;
                    if (selectedDept && selectedDept === d.value) opt.selected = true;
                    deptSelect.appendChild(opt);
                });

                deptSelect.disabled = false;
            }

            facultySelect.addEventListener('change', async () => {
                await loadDepartments(facultySelect.value);
            });

            // Auto-load on edit / validation error (old())
            const selectedFaculty = facultySelect.value;
            const selectedDept = @json(old('department', $user->department ?? ''));
            if (selectedFaculty) loadDepartments(selectedFaculty, selectedDept);
        });
    </script>
@endsection
