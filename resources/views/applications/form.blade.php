@php use App\Enums\ApplicationCategory; @endphp
@extends('layouts.main')

@section('content')
    @php
        // Expect: $users (collection of users)
        // Optional: $application (edit mode)



        $categoryValue = [
            'co'   => ApplicationCategory::ACTIVITY->value,
            'cre'  => ApplicationCategory::CREATIVITY->value,
            'good' => ApplicationCategory::BEHAVIOR->value,
        ];

        $isEdit = isset($application) && $application->exists;

        $selectedUserId = old('user_id', $application->user_id ?? null);
       $selectedCategory = old(
            'category',
            ($application->category?->value ?? ApplicationCategory::ACTIVITY->value)
        );


        $route = $isEdit ? route('applications.update', $application) : route('applications.store');
    @endphp

    <section class="bg-background">
        <div class="container mx-auto w-[80%] py-10">

            {{-- Page title --}}
            <div class="mb-8">
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
                    {{ $isEdit ? 'Edit Excellence Award Application' : 'Apply for Excellence Award' }}
                </h1>
                <p class="mt-2 text-slate-500">
                    {{ $isEdit ? 'Update your application details and supporting documents.' : 'Self-nomination form for the upcoming academic year awards.' }}
                </p>
            </div>

            {{-- Main card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <form method="POST" action="{{ $route }}" enctype="multipart/form-data"
                      x-data="applyPage({{ Js::from($users) }}, {{ Js::from($selectedUserId) }}, '{{ $selectedCategory }}', {{ Js::from($isEdit) }})">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif
                    <input type="hidden" name="return_url" value="{{ url()->previous() }}">


                    {{-- ===================== 1) GENERAL INFORMATION (USER TABLE) ===================== --}}
                    <div class="p-6 md:p-8 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-sm font-bold">
                                1
                            </span>
                            <div class="flex items-center gap-2">
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900">General Information</h2>
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <p class="text-sm text-slate-500">Select the student profile to apply with.</p>
                                </div>

                                {{-- Locked badge in edit mode --}}
                                <span x-show="isEdit"
                                      class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                    Locked (cannot change student)
                                </span>
                            </div>
                        </div>

                        {{-- Search + Table (Create mode only) --}}
                        <div class="mt-6" x-show="!isEdit">

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                <div class="text-sm text-slate-500">
                                    Find and select a student from the list.
                                </div>

                                <div class="w-full md:w-[380px]">
                                    <div class="relative">
                                        <input type="text"
                                               x-model="q"
                                               :disabled="isEdit"
                                               :class="isEdit ? 'bg-slate-100 cursor-not-allowed' : ''"
                                               placeholder="Search name / university id / email..."
                                               class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"
                                             viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M10 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8zm11 3-6-6 1.5-1.5 6 6z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200">
                                <div class="max-h-[360px] overflow-auto">
                                    <table class="w-full text-left text-sm">
                                        <thead class="sticky top-0 bg-slate-50 text-slate-600">
                                        <tr class="[&>th]:px-4 [&>th]:py-3 [&>th]:font-semibold">
                                            <th class="w-[80px]">Pick</th>
                                            <th>Student</th>
                                            <th class="w-[170px]">University ID</th>
                                            <th class="w-[180px]">Faculty</th>
                                            <th class="w-[220px]">Department</th>
                                            <th class="w-[260px]">Email</th>
                                        </tr>
                                        </thead>

                                        <tbody class="divide-y divide-slate-100">
                                        <template x-for="u in filteredUsers()" :key="u.id">
                                            <tr
                                                class="transition"
                                                :class="[
                                                    selectedUserId === u.id ? 'bg-primary/5' : 'hover:bg-slate-50/70',
                                                    isEdit ? 'cursor-not-allowed opacity-80' : 'cursor-pointer'
                                                ].join(' ')"
                                                @click="selectUser(u.id)"
                                            >
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center">
                                                        <div
                                                            class="h-6 w-6 rounded-full border flex items-center justify-center"
                                                            :class="selectedUserId === u.id ? 'border-primary bg-primary' : 'border-slate-300 bg-white'">
                                                            <svg x-show="selectedUserId === u.id"
                                                                 class="h-4 w-4 text-white" viewBox="0 0 24 24"
                                                                 fill="currentColor">
                                                                <path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-3">
                                                        <img
                                                            class="h-9 w-9 rounded-xl object-cover border border-slate-200 bg-white"
                                                            :src="u.avatar" alt="">
                                                        <div>
                                                            <div class="font-semibold text-slate-900"
                                                                 x-text="u.name"></div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="px-4 py-3 text-slate-700"
                                                    x-text="u.university_id ?? '-'"></td>
                                                <td class="px-4 py-3 text-slate-700" x-text="u.faculty"></td>
                                                <td class="px-4 py-3 text-slate-700" x-text="u.department"></td>
                                                <td class="px-4 py-3 text-slate-700" x-text="u.email"></td>
                                            </tr>
                                        </template>

                                        <tr x-show="filteredUsers().length === 0">
                                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                                No results found.
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @error('user_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- hidden input to submit --}}
                        <input type="hidden" name="user_id" :value="selectedUserId">
                        @error('user_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror


                        {{-- Selected user preview card --}}
                        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50/40 p-5">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <template x-if="selectedUser">
                                        <img
                                            class="h-12 w-12 rounded-2xl object-cover border border-slate-200 bg-white"
                                            :src="selectedUser.avatar"
                                            alt="User avatar"
                                        >
                                    </template>

                                    <template x-if="!selectedUser">
                                        <div
                                            class="h-12 w-12 rounded-2xl bg-slate-200 border border-slate-300
                   flex items-center justify-center text-slate-400 text-xs font-semibold">
                                            N/A
                                        </div>
                                    </template>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm text-slate-500">Selected Student</p>
                                            <span x-show="selectedUserId"
                                                  class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-xs font-semibold text-primary">
                                                Selected
                                            </span>
                                        </div>
                                        <p class="mt-1 text-lg font-semibold text-slate-900"
                                           x-text="selectedUser ? selectedUser.name : 'No student selected'"></p>
                                        <p class="text-sm text-slate-500"
                                           x-text="selectedUser ? (selectedUser.university_id ?? '-') : ''"></p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full md:w-auto">
                                    <div class="rounded-xl bg-white border border-slate-200 px-4 py-3">
                                        <p class="text-xs font-semibold text-slate-500">Faculty</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-900"
                                           x-text="selectedUser ? selectedUser.faculty : '-'"></p>
                                    </div>
                                    <div class="rounded-xl bg-white border border-slate-200 px-4 py-3">
                                        <p class="text-xs font-semibold text-slate-500">Department</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-900"
                                           x-text="selectedUser ? selectedUser.department : '-'"></p>
                                    </div>
                                    <div class="rounded-xl bg-white border border-slate-200 px-4 py-3">
                                        <p class="text-xs font-semibold text-slate-500">Email</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-900 truncate"
                                           x-text="selectedUser ? selectedUser.email : '-'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== 2) NOMINATION CATEGORY (CARDS ONLY) ===================== --}}
                    <div class="p-6 md:p-8 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-sm font-bold">
                                2
                            </span>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Nomination Category</h2>
                                <p class="text-sm text-slate-500">Choose one category.</p>
                            </div>
                        </div>

                        <input type="hidden" name="category" :value="category">

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Co-curricular --}}
                            <button type="button"
                                    @click="category = @js($categoryValue['co'])"

                                    class="relative rounded-2xl border p-5 text-left transition"
                                    :class="category === @js($categoryValue['co']) ? 'border-primary ring-2 ring-primary/15 bg-primary/5' : 'border-slate-200 hover:border-slate-300'">
                                <div class="flex items-center justify-between">
                                    <div
                                        class="h-11 w-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M16 11c1.66 0 3-1.34 3-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zM8 11c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.95 1.97 3.45V19h6v-2.5C23 14.17 18.33 13 16 13z"/>
                                        </svg>
                                    </div>

                                    <div class="h-5 w-5 rounded-full border flex items-center justify-center"
                                         :class="category === @js($categoryValue['co']) ? 'border-primary bg-primary' : 'border-slate-300 bg-white'">
                                        <svg x-show="category === @js($categoryValue['co'])" class="h-3 w-3 text-white"
                                             viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="font-semibold text-slate-900">Activity</div>
                                    <div class="text-sm text-slate-500">Leadership &amp; Community</div>
                                </div>
                            </button>

                            {{-- Creativity --}}
                            <button type="button"
                                    @click="category = @js($categoryValue['cre'])"
                                    class="relative rounded-2xl border p-5 text-left transition"
                                    :class="category === @js($categoryValue['cre']) ? 'border-primary ring-2 ring-primary/15 bg-primary/5' : 'border-slate-200 hover:border-slate-300'">
                                <div class="flex items-center justify-between">
                                    <div
                                        class="h-11 w-11 rounded-2xl bg-purple-100 flex items-center justify-center text-purple-600">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M9 21h6v-1H9v1zm3-20C7.93 1 5 3.93 5 7c0 2.38 1.19 4.47 3 5.74V16c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-3.26c1.81-1.27 3-3.36 3-5.74 0-3.07-2.93-6-7-6z"/>
                                        </svg>
                                    </div>

                                    <div class="h-5 w-5 rounded-full border flex items-center justify-center"
                                         :class="category === @js($categoryValue['cre']) ? 'border-primary bg-primary' : 'border-slate-300 bg-white'">
                                        <svg x-show="category ===@js($categoryValue['cre'])" class="h-3 w-3 text-white"
                                             viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="font-semibold text-slate-900">Creativity</div>
                                    <div class="text-sm text-slate-500">Arts &amp; Innovation</div>
                                </div>
                            </button>

                            {{-- Good Conduct --}}
                            <button type="button"
                                    @click="category = @js($categoryValue['good'])"
                                    class="relative rounded-2xl border p-5 text-left transition"
                                    :class="category === @js($categoryValue['good']) ? 'border-primary ring-2 ring-primary/15 bg-primary/5' : 'border-slate-200 hover:border-slate-300'">
                                <div class="flex items-center justify-between">
                                    <div
                                        class="h-11 w-11 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 2 4 5v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V5l-8-3zm-1.1 14.6-3.5-3.5 1.4-1.4 2.1 2.1 4.6-4.6 1.4 1.4-6 6z"/>
                                        </svg>
                                    </div>

                                    <div class="h-5 w-5 rounded-full border flex items-center justify-center"
                                         :class="category === @js($categoryValue['good']) ? 'border-primary bg-primary' : 'border-slate-300 bg-white'">
                                        <svg x-show="category === @js($categoryValue['good'])" class="h-3 w-3 text-white"
                                             viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="font-semibold text-slate-900">Behavior</div>
                                    <div class="text-sm text-slate-500">Ethics &amp; Discipline</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    {{-- ===================== 3) SUPPORTING DOCUMENTS ===================== --}}
                    <div class="p-6 md:p-8">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-sm font-bold">3</span>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Supporting Documents</h2>
                                <p class="text-sm text-slate-500">Manage existing files or upload new ones to support your nomination.</p>
                            </div>
                        </div>

                        {{-- A. SHOW EXISTING ATTACHMENTS --}}
                        @if($isEdit && $application->attachments->count() > 0)
                            <div class="mt-6 space-y-3">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Current Attachments</h3>

                                {{-- Hidden input to send deleted IDs to Backend --}}
                                <template x-for="id in deletedAttachmentIds" :key="id">
                                    <input type="hidden" name="delete_attachments[]" :value="id">
                                </template>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($application->attachments as $file)
                                        <div x-show="!isDeleted({{ $file->id }})"
                                             class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 transition">

                                            <div class="flex items-center gap-3 truncate">
                                                <div class="h-10 w-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center flex-none">
                                                    <span class="text-[10px] font-black text-slate-500">{{ strtoupper(pathinfo($file->file_name, PATHINFO_EXTENSION)) }}</span>
                                                </div>
                                                <div class="truncate">
                                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $file->file_name }}</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-1">
                                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="text-slate-400 hover:text-primary p-2">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-0L10 14" /></svg>
                                                </a>
                                                {{-- Delete Button --}}
                                                <button type="button" @click="removeExistingFile({{ $file->id }})" class="text-slate-400 hover:text-red-600 p-2">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- B. UPLOAD NEW FILES --}}
                        <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50/40 p-10 text-center">
                            <input type="file" name="attachments[]" multiple accept=".pdf,.png,.jpg,.jpeg" class="hidden" x-ref="fileInput" @change="handleFiles($event)">

                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white border border-slate-200">
                                <svg class="h-6 w-6 text-slate-600" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 15v4H5v-4H3v6h18v-6h-2zM11 3h2v10h3l-4 4-4-4h3V3z"/>
                                </svg>
                            </div>

                            <p class="mt-4 text-sm text-slate-800 font-semibold">Upload New Documents</p>
                            <p class="mt-1 text-xs text-slate-500">PDF, PNG, JPG (Max. 5MB each)</p>

                            <button type="button" @click="$refs.fileInput.click()" class="mt-5 inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90">
                                Choose files
                            </button>
                        </div>

                        {{-- C. NEWLY SELECTED FILES PREVIEW (Client-side) --}}
                        <div class="mt-5 space-y-2" x-show="files.length > 0">
                            <p class="text-xs font-bold uppercase tracking-wider text-primary">New files to be uploaded:</p>
                            <template x-for="(f, idx) in files" :key="idx">
                                <div class="flex items-center justify-between rounded-2xl border border-primary/20 bg-primary/5 px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-white border border-primary/20 flex items-center justify-center">
                                            <span class="text-xs font-bold text-primary" x-text="fileBadge(f.name)"></span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900" x-text="f.name"></div>
                                            <div class="text-xs text-slate-500" x-text="formatBytes(f.size)"></div>
                                        </div>
                                    </div>
                                    <button type="button" class="text-slate-400 hover:text-red-500" @click="removeFile(idx)">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Action buttons --}}
                        <div class="mt-10 flex items-center justify-end gap-3">
                            <a href="{{ url()->previous() }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">Cancel</a>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90">
                                {{ $isEdit ? 'Update Application' : 'Submit Application' }}
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </section>

    {{-- Alpine helpers --}}
    <script>
        function applyPage(users, selectedUserId, selectedCategory, isEdit) {
            const normalize = (u) => {
                return {
                    id: Number(u.id),
                    name: u.name ?? '',
                    email: u.email ?? '',
                    university_id: u.university_id ?? null,
                    faculty: u.faculty ?? '',
                    department: u.department ?? '',
                    avatar: u.profile_url
                };
            };

            return {

                deletedAttachmentIds: [],

                removeExistingFile(id) {
                    if (!this.deletedAttachmentIds.includes(id)) {
                        this.deletedAttachmentIds.push(id);
                    }
                },

                isDeleted(id) {
                    return this.deletedAttachmentIds.includes(id);
                },

                isEdit: !!isEdit,
                users: (users || []).map(normalize),
                q: '',
                selectedUserId: selectedUserId ? Number(selectedUserId) : null,
                category: selectedCategory || @js($categoryValue['co']),

                files: [],

                get selectedUser() {
                    if (!this.selectedUserId) return null;
                    return this.users.find(x => x.id === this.selectedUserId) || null;
                },

                filteredUsers() {
                    // Edit mode: show only the selected user (and lock changes)
                    if (this.isEdit) {
                        return this.selectedUser ? [this.selectedUser] : [];
                    }

                    const q = (this.q || '').toLowerCase().trim();
                    if (!q) return this.users;

                    return this.users.filter(u => {
                        return (u.name || '').toLowerCase().includes(q)
                            || String(u.university_id || '').toLowerCase().includes(q)
                            || (u.email || '').toLowerCase().includes(q)
                            || (u.faculty || '').toLowerCase().includes(q)
                            || (u.department || '').toLowerCase().includes(q);
                    });
                },

                selectUser(id) {
                    // Block changes in edit mode
                    if (this.isEdit) return;
                    this.selectedUserId = Number(id);
                },

                handleFiles(e) {
                    this.files = Array.from(e.target.files || []);
                },

                removeFile(idx) {
                    this.files.splice(idx, 1);
                    this.$refs.fileInput.value = '';
                },

                fileBadge(filename) {
                    const ext = (filename.split('.').pop() || '').toUpperCase();
                    return ext.length <= 4 ? ext : 'FILE';
                },

                formatBytes(bytes) {
                    if (!bytes && bytes !== 0) return '';
                    const units = ['B','KB','MB','GB'];
                    let i = 0;
                    let n = bytes;
                    while (n >= 1024 && i < units.length - 1) { n /= 1024; i++; }
                    return `${n.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
                }
            }
        }
    </script>
@endsection
