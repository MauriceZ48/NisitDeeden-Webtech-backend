@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[60%] py-8 space-y-8">
            <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-6">
                @csrf

            <div>
                <div>
                    <label for="name">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="@error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="icon">Icon</label>
                    <input type="file" name="icon" accept=".png, .jpg, .jpeg" >
                    @error('icon')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="description">Description</label>
                    <input type="text" name="description" value="{{ old('description') }}" >
                </div>
            </div>


                <div x-data="{ attributes: {{ json_encode(old('attributes', [])) }} }">
                    @if($errors->has('attributes.*.label'))
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                            <p class="text-red-700 font-bold">Please check your fields:</p>
                            <ul class="list-disc ml-5 text-red-600 text-sm">
                                @foreach($errors->get('attributes.*.label') as $message)
                                    <li>{{ $message[0] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                <template x-for="(attr, index) in attributes" :key="index">
                    <div class="flex gap-4 items-end bg-gray-50 p-4 rounded-lg border">
                        <div class="flex-1">
                            <label class="block text-sm font-medium">Field Label</label>
                            <input type="text" :name="`attributes[${index}][label]`" x-model="attr.label" class="w-full rounded-md border-gray-300">
                        </div>

                        <div class="w-1/4">
                            <label class="block text-sm font-medium">Type</label>
                            <select :name="`attributes[${index}][type]`" x-model="attr.type" class="w-full rounded-md border-gray-300">
                                <option value="text">Short Text</option>
                                <option value="number">Number</option>
                                <option value="file">File Upload</option>
                            </select>
                        </div>
                        <div class="flex flex-col items-center">
                            <label class="text-xs">Required?</label>
                            <input type="checkbox" :name="`attributes[${index}][is_required]`" x-model="attr.is_required" value="1">
                        </div>
                        <button type="button" @click="attributes.splice(index, 1)" class="text-red-500 mb-2">Remove</button>
                    </div>

                </template>

                <button type="button" @click="attributes.push({label: '', type: 'text', is_required: false})"
                        class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg">
                    + Add Field
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
