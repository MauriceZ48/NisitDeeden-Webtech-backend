@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[80%] py-10">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                        <h3 class="text-sm font-bold text-red-800">Please fix the following errors:</h3>
                    </div>
                    <ul class="mt-2 list-inside list-disc text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Apply for Excellence Award</h1>
                    <p class="mt-2 text-slate-500">Step 2: Fill in the specific details for the selected award.</p>
                </div>
                <a href="{{ route('applications.create') }}" class="text-sm font-semibold text-primary hover:underline">
                    &larr; Back to Selection
                </a>
            </div>

            <form method="POST" action="{{ route('applications.store') }}" enctype="multipart/form-data"
                  class="rounded-2xl border border-slate-200 bg-white shadow-sm"
                  x-data="dynamicForm()">
                @csrf

                {{-- Hidden data from Step 1 --}}
                <input type="hidden" name="user_id" value="{{ $student->id }}">
                <input type="hidden" name="category_id" value="{{ $category->id }}">

                {{-- ===================== 1) SUMMARY (READ-ONLY) ===================== --}}
                <div class="p-6 md:p-8 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Application Summary</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Student Summary --}}
                        <div class="flex items-center gap-4 bg-white p-4 rounded-xl border border-slate-200">
                            <img src="{{ $student->profile_url }}" class="h-12 w-12 rounded-lg object-cover border border-slate-200">
                            <div>
                                <p class="text-xs text-slate-500 font-semibold">Applicant</p>
                                <p class="font-bold text-slate-900">{{ $student->name }}</p>
                                <p class="text-sm text-slate-500">{{ $student->university_id }} • {{ $student->faculty }}</p>
                            </div>
                        </div>

                        {{-- Category Summary --}}
                        <div class="flex items-center gap-4 bg-white p-4 rounded-xl border border-slate-200">

                            <div>
                                <p class="text-xs text-slate-500 font-semibold">Category</p>
                                <p class="font-bold text-slate-900">{{ $category->name }}</p>
                                <p class="text-sm text-slate-500 truncate">{{ $category->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== 2) DYNAMIC ATTRIBUTES ===================== --}}
                <div class="p-6 md:p-8 border-b border-slate-100">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-sm font-bold">1</span>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Award Specific Details</h2>
                            <p class="text-sm text-slate-500">Please provide the required information for the {{ $category->name }} category.</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @forelse($category->attributes as $attribute)
                            <div>
                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                    {{ $attribute->label }}
                                    @if($attribute->is_required)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>

                                @if($attribute->type === 'textarea')
                                    <textarea name="values[{{ $attribute->id }}]"
                                              rows="4"
                                              class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-primary/40 focus:ring-2 focus:ring-primary/20"
                                              {{ $attribute->is_required ? 'required' : '' }}>{{ old("values.{$attribute->id}") }}</textarea>

                                @elseif($attribute->type === 'file')
                                    <input type="file" name="values[{{ $attribute->id }}]"
                                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20"
                                        {{ $attribute->is_required ? 'required' : '' }}>
                                @else
                                    <input type="{{ $attribute->type ?? 'text' }}" name="values[{{ $attribute->id }}]"
                                           value="{{ old("values.{$attribute->id}") }}"
                                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-primary/40 focus:ring-2 focus:ring-primary/20"
                                        {{ $attribute->is_required ? 'required' : '' }}>
                                @endif

                                @error("values.{$attribute->id}")
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-slate-500">
                                This category does not require any specific details. Proceed to attach documents.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- ===================== 3) SUPPORTING DOCUMENTS (From Old Form) ===================== --}}
                <div class="p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-sm font-bold">2</span>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Supporting Documents</h2>
                            <p class="text-sm text-slate-500">Upload any general files to support this nomination.</p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/40 p-10 text-center">
                        <input type="file" name="attachments[]" multiple accept=".pdf,.png,.jpg,.jpeg" class="hidden" x-ref="fileInput" @change="handleFiles($event)">

                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white border border-slate-200">
                            <svg class="h-6 w-6 text-slate-600" fill="currentColor" viewBox="0 0 24 24"><path d="M19 15v4H5v-4H3v6h18v-6h-2zM11 3h2v10h3l-4 4-4-4h3V3z"/></svg>
                        </div>

                        <p class="mt-4 text-sm text-slate-800 font-semibold">Upload New Documents</p>
                        <p class="mt-1 text-xs text-slate-500">PDF, PNG, JPG (Max. 5MB each)</p>

                        <button type="button" @click="$refs.fileInput.click()" class="mt-5 inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90">
                            Choose files
                        </button>
                    </div>

                    {{-- File Preview --}}
                    <div class="mt-5 space-y-2" x-show="files.length > 0" x-cloak>
                        <p class="text-xs font-bold uppercase tracking-wider text-primary">Selected Files:</p>
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
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="p-6 md:p-8 border-t border-slate-100 flex items-center justify-end gap-3 bg-slate-50/50 rounded-b-2xl">
                    <a href="{{ route('applications.create') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-8 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90">
                        Submit Application
                    </button>
                </div>
            </form>
        </div>
    </section>

    <script>
        function dynamicForm() {
            return {
                files: [],
                handleFiles(e) {
                    this.files = Array.from(e.target.files || []);
                },
                removeFile(idx) {
                    this.files.splice(idx, 1);
                    // Reset the input so the user can re-select the same file if needed
                    const dt = new DataTransfer();
                    this.files.forEach(file => dt.items.add(file));
                    this.$refs.fileInput.files = dt.files;
                },
                fileBadge(filename) {
                    const ext = (filename.split('.').pop() || '').toUpperCase();
                    return ext.length <= 4 ? ext : 'FILE';
                },
                formatBytes(bytes) {
                    if (!bytes && bytes !== 0) return '';
                    const units = ['B','KB','MB','GB'];
                    let i = 0; let n = bytes;
                    while (n >= 1024 && i < units.length - 1) { n /= 1024; i++; }
                    return `${n.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
                }
            }
        }
    </script>
@endsection
