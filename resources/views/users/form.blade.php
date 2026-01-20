@extends('layouts.main')

@section('content')
    @php
        // Set this from controller later (example)
        $isEdit = isset($user) && $user->exists;

        // Small class helpers (same style as your application page)
        $card  = 'bg-white border border-gray-200 rounded-2xl shadow-sm';
        $pad   = 'p-6';
        $title = 'text-gray-900 font-semibold';
        $muted = 'text-gray-500';
        $input = 'w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm
                  focus:ring-2 focus:ring-primary focus:border-primary';

        // UI-only demo data
        $facultyOptions = ['Engineering', 'Science', 'Business'];
    @endphp

    <section class="bg-background" x-data="userProfilePage()" x-init="init()">
        <div class="container mx-auto w-[80%] md:w-[60%] py-6 space-y-6">

            {{-- Header --}}
            <div>
                <h1 class="text-3xl font-bold text-gray-900" x-text="isEdit ? 'Edit User Profile' : 'Create User Profile'"></h1>
                <p class="mt-1 text-sm text-gray-500">
                    Manage student and staff excellence award participants.
                </p>
            </div>

            {{-- Main card --}}
            <div class="{{ $card }} overflow-hidden">

                {{-- Profile Photo --}}
                <div class="{{ $pad }}">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-5">
                            {{-- avatar box --}}
                            <div class="relative">
                                <div class="h-20 w-20 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                    <template x-if="avatarSrc">
                                        <img :src="avatarSrc" class="h-full w-full object-cover" alt="Profile">
                                    </template>

                                    <template x-if="!avatarSrc">
                                        <svg class="h-8 w-8 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </template>
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

                                <input id="profile_picture" type="file" accept="image/*" class="hidden" @change="pickPhoto($event)">
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

                                    <button type="button"
                                            @click="removePhoto()"
                                            class="inline-flex items-center justify-center rounded-lg bg-gray-100
                                               px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-200">
                                        Remove
                                    </button>
                                </div>
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
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">
                                1
                            </div>
                            <h2 class="text-base {{ $title }}">Basic Information</h2>
                        </div>

                        <div class="space-y-4">
                            {{-- Full name --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Full Name</label>
                                <input type="text" x-model="form.name" class="{{ $input }} mt-2"
                                       placeholder="e.g. Alexander Hamilton">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Email --}}
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">University Email</label>
                                    <input type="email" x-model="form.email" class="{{ $input }} mt-2"
                                           placeholder="name@university.edu">
                                </div>

                                {{-- ID --}}
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">Student / Staff ID</label>
                                    <input type="text" x-model="form.student_staff_id" class="{{ $input }} mt-2"
                                           placeholder="ID-882910">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Role & Access --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">
                                2
                            </div>
                            <h2 class="text-base {{ $title }}">Role &amp; Access</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <template x-for="r in roles" :key="r.value">
                                <button type="button"
                                        @click="form.role = r.value"
                                        class="w-full text-left border rounded-2xl p-4 bg-white hover:border-primary/60
                                           shadow-sm transition"
                                        :class="form.role === r.value ? 'border-primary bg-primary/10' : 'border-gray-200'">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-1 w-4 h-4 rounded-full border flex items-center justify-center"
                                             :class="form.role === r.value ? 'border-primary' : 'border-gray-300'">
                                            <div class="w-2 h-2 rounded-full"
                                                 :class="form.role === r.value ? 'bg-primary' : 'bg-transparent'"></div>
                                        </div>

                                        <div>
                                            <div class="text-sm font-semibold text-gray-900" x-text="r.label"></div>
                                            <div class="text-xs text-gray-500" x-text="r.desc"></div>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Organization --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">
                                3
                            </div>
                            <h2 class="text-base {{ $title }}">Organization</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Faculty</label>
                                <select x-model="form.faculty" class="{{ $input }} mt-2">
                                    <option value="">Search for faculty...</option>
                                    @foreach($facultyOptions as $f)
                                        <option value="{{ $f }}">{{ $f }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600">Department</label>
                                <input x-model="form.department" class="{{ $input }} mt-2"
                                       placeholder="e.g. Civil Engineering">
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Bottom actions (like screenshot) --}}
                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <a href="/users" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>

                        <div class="flex items-center gap-2 justify-end">
                            <button type="button"
                                    @click="saveDraft()"
                                    class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white
                                       px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm">
                                Save Draft
                            </button>

                            <button type="button"
                                    @click="submit()"
                                    class="inline-flex items-center justify-center rounded-lg bg-primary text-white
                                       px-5 py-2 text-sm font-semibold shadow-sm hover:opacity-95">
                                Save User Profile
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <p class="text-center text-xs text-gray-500">
                Ensuring data accuracy is vital for the integrity of the Excellence Awards.
                <a href="#" class="text-primary font-semibold hover:underline">Read the data policy.</a>
            </p>

            {{-- Alpine (UI only) --}}
            <script>
                window.__EDIT_USER__ = window.__EDIT_USER__ || null;

                function userProfilePage() {
                    return {
                        isEdit: false,

                        roles: [
                            { value: 'student', label: 'Student', desc: 'Applicant access' },
                            { value: 'staff',   label: 'Staff',   desc: 'Reviewer access' },
                            { value: 'admin',   label: 'Admin',   desc: 'Full system access' },
                        ],

                        form: {
                            name: '',
                            email: '',
                            student_staff_id: '',
                            role: 'student',
                            faculty: '',
                            department: '',
                            profile_picture: null, // backend url later
                        },

                        localAvatarUrl: null,

                        init() {
                            this.isEdit = window.location.pathname.includes('/edit') || !!window.__EDIT_USER__;
                            if (this.isEdit && window.__EDIT_USER__) {
                                this.form = { ...this.form, ...window.__EDIT_USER__ };
                            }
                        },

                        get avatarSrc() {
                            return this.localAvatarUrl || this.form.profile_picture || null;
                        },

                        pickPhoto(e) {
                            const file = e?.target?.files?.[0];
                            if (!file) return;
                            if (this.localAvatarUrl) URL.revokeObjectURL(this.localAvatarUrl);
                            this.localAvatarUrl = URL.createObjectURL(file);
                        },

                        removePhoto() {
                            if (this.localAvatarUrl) URL.revokeObjectURL(this.localAvatarUrl);
                            this.localAvatarUrl = null;
                            this.form.profile_picture = null;
                            const input = document.getElementById('profile_picture');
                            if (input) input.value = '';
                        },

                        saveDraft() {
                            console.log('SAVE DRAFT (UI only)', { ...this.form });
                            alert('Saved draft (UI only)');
                        },

                        submit() {
                            console.log(this.isEdit ? 'UPDATE USER' : 'CREATE USER', { ...this.form });
                            alert('Saved user profile (UI only)');
                            window.location.href = '/users';
                        },
                    }
                }
            </script>

        </div>
    </section>
@endsection
