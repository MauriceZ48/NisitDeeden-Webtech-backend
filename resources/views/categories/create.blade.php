@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[60%] py-8 space-y-8">
            <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-6">
                @csrf

                <div class="space-y-6 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name') }}"
                               placeholder="e.g. Academic Excellence"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all
               @error('name') border-red-500 @else border-gray-300 @enderror">
                        @error('name')
                        <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Lucide Icon Name</label>
                        <div class="relative">
                            <input type="text"
                                   name="icon"
                                   id="icon"
                                   value="{{ old('icon') }}"
                                   placeholder="e.g. lucide:award"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all
                   @error('icon') border-red-500 @enderror">
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="info" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <p class="text-gray-400 text-[10px] mt-1">Use format 'lucide:icon-name'</p>
                        @error('icon')
                        <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description"
                                  id="description"
                                  rows="3"
                                  placeholder="Describe the requirements for this category..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all
                  @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                <div x-data="{
    attributes: {{ json_encode(old('attributes', [])) }},
    init() {
        // Watch for changes in the attributes array
        this.$watch('attributes', (value) => {
            value.forEach(attr => {
                if (attr.type === 'file') {
                    attr.is_required = true;
                }
            });
        }, { deep: true });
    }
}">

                    <template x-for="(attr, index) in attributes" :key="index">
                        <div class="flex gap-4 items-end bg-gray-50 p-4 rounded-lg border mb-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-slate-700">Field Label</label>
                                <input type="text" :name="`attributes[${index}][label]`" x-model="attr.label"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="e.g. Transcript, GPA">
                            </div>

                            <div class="w-1/4">
                                <label class="block text-sm font-medium text-slate-700">Type</label>
                                <select :name="`attributes[${index}][type]`" x-model="attr.type"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="text">Short Text</option>
                                    <option value="textarea">Text Area</option>
                                    <option value="file">File Upload</option>
                                </select>
                            </div>

                            <div class="flex flex-col items-center px-2">
                                <label class="text-xs font-bold text-slate-500 mb-1">Required?</label>
                                <input type="checkbox"
                                       :name="`attributes[${index}][is_required]`"
                                       x-model="attr.is_required"
                                       :disabled="attr.type === 'file'"
                                       class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                       value="1">

                                <input type="hidden" :name="`attributes[${index}][is_required]`" value="1" x-if="attr.type === 'file'">
                            </div>

                            <button type="button" @click="attributes.splice(index, 1)" class="text-red-500 hover:text-red-700 mb-2 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </template>

                    <button type="button" @click="attributes.push({label: '', type: 'text', is_required: false})"
                            class="mt-4 inline-flex items-center bg-slate-800 text-white px-4 py-2 rounded-lg hover:bg-slate-700 transition-colors shadow-sm font-medium">
                        <span class="mr-2 text-lg">+</span> Add New Field
                    </button>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:opacity-90 transition-opacity shadow-sm">
                        Create Category
                    </button>
                </div>

            </form>
        </div>
    </section>
@endsection
