@extends('layouts.main')

@section('content')
    <section class="bg-background" x-data="userFormPage()" x-init="init()">
        <div class="container mx-auto w-[90%] py-8 space-y-6">

            {{-- Header --}}
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900" x-text="isEdit ? 'Edit User' : 'Create User'"></h1>
                    <p class="mt-1 text-sm text-slate-500"
                       x-text="isEdit ? 'Update user information and save changes.' : 'Fill in user information to create a new account.'"></p>
                </div>

                <a href="/users"
                   class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Back to Users
                </a>
            </div>

            {{-- Form --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                {{-- Top bar --}}
                <div class="p-5 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        {{-- Avatar (like your picture) --}}
                        <div class="relative">
                            <div class="h-20 w-20 rounded-full bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center">
                                <template x-if="avatarSrc">
                                    <img :src="avatarSrc" alt="Profile"
                                         class="h-full w-full object-cover">
                                </template>

                                <template x-if="!avatarSrc">
                                    <div class="text-base font-extrabold text-slate-600" x-text="avatarInitials"></div>
                                </template>
                            </div>

                            {{-- camera button overlay --}}
                            <label for="profile_picture"
                                   class="absolute -bottom-1 -right-1 h-9 w-9 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center cursor-pointer hover:bg-slate-50">
                                <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8a2 2 0 012-2h3l2-2h4l2 2h3a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 17a4 4 0 100-8 4 4 0 000 8z"/>
                                </svg>
                            </label>

                            <input id="profile_picture" type="file" accept="image/*" class="hidden" @change="onPickFile($event)">
                        </div>

                        <div>
                            <div class="text-sm font-extrabold text-slate-900" x-text="form.name || (isEdit ? 'User' : 'New User')"></div>
                            <div class="text-xs text-slate-500" x-text="form.email || 'email@example.com'"></div>

                            <div class="mt-2 flex items-center gap-2" x-show="avatarSrc" x-cloak>
                                <button type="button"
                                        @click="clearPhoto()"
                                        class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                                    Remove photo
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="resetForm()"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Reset
                        </button>
                        <button type="button"
                                @click="submit()"
                                class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30">
                            <span x-text="isEdit ? 'Save Changes' : 'Create User'"></span>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-5 space-y-6">

                    {{-- Basic Information --}}
                    <div class="space-y-4">
                        <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Basic Information</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Full name</label>
                                <input type="text" x-model="form.name"
                                       class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary focus:ring-primary/20"
                                       placeholder="e.g. John Doe">
                                <p class="mt-1 text-xs text-red-600" x-show="errors.name" x-text="errors.name" x-cloak></p>
                            </div>

                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Email</label>
                                <input type="email" x-model="form.email"
                                       class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary focus:ring-primary/20"
                                       placeholder="e.g. john.doe@ku.th">
                                <p class="mt-1 text-xs text-red-600" x-show="errors.email" x-text="errors.email" x-cloak></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Student/Staff ID</label>
                                <input type="text" x-model="form.student_staff_id"
                                       class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary focus:ring-primary/20"
                                       placeholder="e.g. 6412345678">
                                <p class="mt-1 text-xs text-red-600" x-show="errors.student_staff_id" x-text="errors.student_staff_id" x-cloak></p>
                            </div>

                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Role</label>
                                <select x-model="form.role"
                                        class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-primary focus:ring-primary/20">
                                    <option value="student">Student</option>
                                    <option value="staff">Staff</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Organization --}}
                    <div class="space-y-4">
                        <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Organization</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Faculty</label>
                                <select x-model="form.faculty"
                                        class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-primary focus:ring-primary/20">
                                    <option value="">Select faculty</option>
                                    <template x-for="f in facultyOptions" :key="f">
                                        <option :value="f" x-text="f"></option>
                                    </template>
                                </select>
                                <p class="mt-1 text-xs text-red-600" x-show="errors.faculty" x-text="errors.faculty" x-cloak></p>
                            </div>

                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Department</label>
                                <select x-model="form.department"
                                        class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-primary focus:ring-primary/20">
                                    <option value="">Select department</option>
                                    <template x-for="d in departmentOptions" :key="d">
                                        <option :value="d" x-text="d"></option>
                                    </template>
                                </select>
                                <p class="mt-1 text-xs text-red-600" x-show="errors.department" x-text="errors.department" x-cloak></p>
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="h-px bg-slate-200"></div>

                    {{-- Danger zone (only edit) --}}
                    <template x-if="isEdit">
                        <div class="space-y-4">
                            <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Danger Zone</h2>

                            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                                <p class="text-sm font-semibold text-red-700">Delete this user</p>
                                <p class="mt-1 text-sm text-red-600">This action cannot be undone.</p>

                                <div class="mt-3">
                                    <button type="button"
                                            @click="confirmDelete()"
                                            class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
                                        Delete User
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                </div>
            </div>

            {{-- JS (UI only, no PHP logic) --}}
            <script>
                // Optional: set this from backend later when editing
                window.__EDIT_USER__ = window.__EDIT_USER__ || null;

                function userFormPage() {
                    return {
                        isEdit: false,

                        facultyOptions: ['Engineering', 'Science', 'Business'],
                        departmentOptions: [
                            'Computer Engineering',
                            'Information Technology',
                            'Computer Science',
                            'Electrical Engineering',
                            'Business Administration'
                        ],

                        form: {
                            id: null,
                            name: '',
                            email: '',
                            student_staff_id: '',
                            faculty: '',
                            department: '',
                            role: 'student',
                            profile_picture: null, // URL from backend later
                        },

                        // local UI-only selected file preview
                        localAvatarUrl: null,

                        errors: {},

                        init() {
                            this.isEdit = window.location.pathname.includes('/edit') || !!window.__EDIT_USER__;

                            if (this.isEdit && window.__EDIT_USER__) {
                                this.form = { ...this.form, ...window.__EDIT_USER__ };
                            }
                        },

                        get avatarInitials() {
                            const parts = String(this.form.name || 'User').trim().split(/\s+/).filter(Boolean).slice(0, 2);
                            return parts.map(p => (p[0] || '').toUpperCase()).join('') || 'U';
                        },

                        get avatarSrc() {
                            return this.localAvatarUrl || this.form.profile_picture || null;
                        },

                        onPickFile(e) {
                            const file = e?.target?.files?.[0];
                            if (!file) return;

                            // UI-only preview inside avatar
                            if (this.localAvatarUrl) URL.revokeObjectURL(this.localAvatarUrl);
                            this.localAvatarUrl = URL.createObjectURL(file);
                        },

                        clearPhoto() {
                            if (this.localAvatarUrl) URL.revokeObjectURL(this.localAvatarUrl);
                            this.localAvatarUrl = null;

                            // if you want to also clear backend URL in UI:
                            this.form.profile_picture = null;

                            // reset input value so selecting same file again works
                            const input = document.getElementById('profile_picture');
                            if (input) input.value = '';
                        },

                        resetForm() {
                            this.errors = {};

                            if (this.isEdit && window.__EDIT_USER__) {
                                this.form = { ...this.form, ...window.__EDIT_USER__ };
                                this.clearPhoto(); // clears local preview & also clears profile_picture; remove if you don't want that
                                // If you want to keep backend picture after reset, comment the line above and use:
                                // if (this.localAvatarUrl) URL.revokeObjectURL(this.localAvatarUrl);
                                // this.localAvatarUrl = null;
                                return;
                            }

                            this.form = {
                                id: null,
                                name: '',
                                email: '',
                                student_staff_id: '',
                                faculty: '',
                                department: '',
                                role: 'student',
                                profile_picture: null,
                            };
                            this.clearPhoto();
                        },

                        validate() {
                            const errs = {};

                            if (!this.form.name?.trim()) errs.name = 'Name is required.';
                            if (!this.form.email?.trim()) errs.email = 'Email is required.';
                            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email.trim())) errs.email = 'Email format is invalid.';

                            if (!this.form.student_staff_id?.trim()) errs.student_staff_id = 'Student/Staff ID is required.';
                            if (!this.form.faculty?.trim()) errs.faculty = 'Faculty is required.';
                            if (!this.form.department?.trim()) errs.department = 'Department is required.';

                            this.errors = errs;
                            return Object.keys(errs).length === 0;
                        },

                        submit() {
                            if (!this.validate()) return;

                            const payload = { ...this.form };
                            console.log(this.isEdit ? 'UPDATE USER' : 'CREATE USER', payload);

                            alert(this.isEdit ? 'Saved changes (UI only)' : 'Created user (UI only)');
                            window.location.href = '/users';
                        },

                        confirmDelete() {
                            if (!confirm('Delete this user?')) return;

                            console.log('DELETE USER', this.form.id);
                            alert('Deleted user (UI only)');
                            window.location.href = '/users';
                        },
                    }
                }
            </script>
        </div>
    </section>
@endsection
