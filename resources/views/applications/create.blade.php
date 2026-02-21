@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[80%] py-10">

            {{-- Page title --}}
            <div class="mb-8">
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Apply for Excellence Award</h1>
                <p class="mt-2 text-slate-500">Step 1: Select the student profile and nomination category.</p>
            </div>

            {{-- Main wrapper using Alpine --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm"
                 x-data="selectionPage({{ Js::from($users) }}, {{ Js::from($categories) }})">

                {{-- ===================== 1) GENERAL INFORMATION (USER TABLE) ===================== --}}
                <div class="p-6 md:p-8 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-sm font-bold">1</span>
                        <div class="flex items-center gap-2">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">General Information</h2>
                                <p class="text-sm text-slate-500">Select the student profile to apply with.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Search + Table --}}
                    <div class="mt-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div class="text-sm text-slate-500">Find and select a student from the list.</div>
                            <div class="w-full md:w-[380px]">
                                <div class="relative">
                                    <input type="text" x-model="q" placeholder="Search name / university id / email..."
                                           class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="currentColor">
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
                                        <tr class="transition cursor-pointer"
                                            :class="selectedUserId === u.id ? 'bg-primary/5' : 'hover:bg-slate-50/70'"
                                            @click="selectUser(u.id)">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center">
                                                    <div class="h-6 w-6 rounded-full border flex items-center justify-center"
                                                         :class="selectedUserId === u.id ? 'border-primary bg-primary' : 'border-slate-300 bg-white'">
                                                        <svg x-show="selectedUserId === u.id" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-3">
                                                    <img class="h-9 w-9 rounded-xl object-cover border border-slate-200 bg-white" :src="u.avatar" alt="">
                                                    <div><div class="font-semibold text-slate-900" x-text="u.name"></div></div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-slate-700" x-text="u.university_id ?? '-'"></td>
                                            <td class="px-4 py-3 text-slate-700" x-text="u.faculty"></td>
                                            <td class="px-4 py-3 text-slate-700" x-text="u.department"></td>
                                            <td class="px-4 py-3 text-slate-700" x-text="u.email"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="filteredUsers().length === 0">
                                        <td colspan="6" class="px-4 py-10 text-center text-slate-500">No results found.</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== 🌟 SELECTED USER PREVIEW CARD 🌟 ===================== --}}
                    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50/40 p-5">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <template x-if="selectedUser">
                                    <img class="h-12 w-12 rounded-2xl object-cover border border-slate-200 bg-white" :src="selectedUser.avatar" alt="User avatar">
                                </template>
                                <template x-if="!selectedUser">
                                    <div class="h-12 w-12 rounded-2xl bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-400 text-xs font-semibold">N/A</div>
                                </template>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm text-slate-500">Selected Student</p>
                                        <span x-show="selectedUserId" class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-xs font-semibold text-primary">Selected</span>
                                    </div>
                                    <p class="mt-1 text-lg font-semibold text-slate-900" x-text="selectedUser ? selectedUser.name : 'No student selected'"></p>
                                    <p class="text-sm text-slate-500" x-text="selectedUser ? (selectedUser.university_id ?? '-') : ''"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full md:w-auto">
                                <div class="rounded-xl bg-white border border-slate-200 px-4 py-3">
                                    <p class="text-xs font-semibold text-slate-500">Faculty</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900" x-text="selectedUser ? selectedUser.faculty : '-'"></p>
                                </div>
                                <div class="rounded-xl bg-white border border-slate-200 px-4 py-3">
                                    <p class="text-xs font-semibold text-slate-500">Department</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900" x-text="selectedUser ? selectedUser.department : '-'"></p>
                                </div>
                                <div class="rounded-xl bg-white border border-slate-200 px-4 py-3">
                                    <p class="text-xs font-semibold text-slate-500">Email</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900 truncate" x-text="selectedUser ? selectedUser.email : '-'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== 2) NOMINATION CATEGORY (Dynamic Loop) ===================== --}}
                <div class="p-6 md:p-8 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-sm font-bold">2</span>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Nomination Category</h2>
                            <p class="text-sm text-slate-500">Choose one category.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <template x-for="cat in categories" :key="cat.id">
                            <button type="button" @click="selectedCategorySlug = cat.slug"
                                    class="relative rounded-2xl border p-5 text-left transition"
                                    :class="selectedCategorySlug === cat.slug ? 'border-primary ring-2 ring-primary/15 bg-primary/5' : 'border-slate-200 hover:border-slate-300'">
                                <div class="flex items-center justify-between">
                                    <div class="h-11 w-11 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center p-2 overflow-hidden">
                                        <img :src="'/storage/' + cat.icon" class="w-full h-full object-contain">
                                    </div>
                                    <div class="h-5 w-5 rounded-full border flex items-center justify-center"
                                         :class="selectedCategorySlug === cat.slug ? 'border-primary bg-primary' : 'border-slate-300 bg-white'">
                                        <svg x-show="selectedCategorySlug === cat.slug" class="h-3 w-3 text-white" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="font-semibold text-slate-900" x-text="cat.name"></div>
                                    <div class="text-sm text-slate-500 truncate" x-text="cat.description"></div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="p-6 md:p-8 flex items-center justify-end gap-3">
                    <a href="{{ route('applications.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">Cancel</a>
                    <button type="button" @click="goToForm()"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="!selectedUserId || !selectedCategorySlug">
                        Next Step
                    </button>
                </div>

            </div>
        </div>
    </section>

    {{-- Alpine helpers --}}
    <script>
        function selectionPage(users, categories) {
            const normalize = (u) => ({
                id: Number(u.id),
                name: u.name ?? '',
                email: u.email ?? '',
                university_id: u.university_id ?? null,
                faculty: u.faculty ?? '',
                department: u.department ?? '',
                avatar: u.profile_url // Maps the appended profile_url from your UserRepo
            });

            return {
                users: (users || []).map(normalize),
                categories: categories,
                q: '',
                selectedUserId: null,
                selectedCategorySlug: null,

                get selectedUser() {
                    if (!this.selectedUserId) return null;
                    return this.users.find(x => x.id === this.selectedUserId) || null;
                },

                filteredUsers() {
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
                    this.selectedUserId = Number(id);
                },

                goToForm() {
                    if (this.selectedUserId && this.selectedCategorySlug) {
                        window.location.href = `/applications/form/${this.selectedCategorySlug}?student_id=${this.selectedUserId}`;
                    }
                }
            }
        }
    </script>
@endsection
