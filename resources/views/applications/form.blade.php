@extends('layouts.main')

@section('content')
    @php
        // Check if we are editing or creating
        $isEdit = $artist->exists;
        $title = $isEdit ? 'Edit Application' : 'Add New Application';
        $route = $isEdit ? route('applications.update', $artist) : route('applications.store');
    @endphp

        <h1 class="text-2xl font-bold mb-6">{{ $title }}</h1>

        <form action="{{ $route }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- 1. Method Spoofing: Required for Update (PUT/PATCH) --}}
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="space-y-4">
                {{-- 2. Value Handling: Use old() with the model value as fallback --}}
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Application Category</label>
                    <select name="category" id="category"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('category') border-red-500 @enderror">

                        <option value="">Select a Category</option>

                        @foreach($categories as $category)
                            <option value="{{ $category->value }}"
                                {{ old('category', $application->category?->value ?? $application->category) == $category->value ? 'selected' : '' }}>
                                {{ ucfirst(strtolower($category->name)) }}
                            </option>
                        @endforeach

                    </select>
                    @error('category')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                    {{ $isEdit ? 'Update Application' : 'Save Application' }}
                </button>
            </div>
        </form>
@endsection
