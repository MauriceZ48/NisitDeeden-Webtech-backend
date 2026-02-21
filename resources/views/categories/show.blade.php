@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[60%] py-8 space-y-8">
            <h1 class="text-3xl font-bold text-gray-800">Category Detail: {{ $category->name }}</h1>

            <ul class="space-y-2">
                @foreach($category->attributes as $attribute)
                    <li class="p-3 bg-white border rounded shadow-sm">
                        <span class="font-bold">{{ $attribute->label }}</span>
                        <span class="text-sm text-gray-500">({{ $attribute->type }})</span>
                    </li>
                @endforeach
            </ul>

            <div class="flex gap-4">
                <form action="{{ route('categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="{{ $category->is_active ? 0 : 1 }}">
                    <button type="submit" class="px-4 py-2 rounded font-bold {{ $category->is_active ? 'bg-orange-500 text-white' : 'bg-green-500 text-white' }}">
                        {{ $category->is_active ? 'Deactivate Category' : 'Activate Category' }}
                    </button>
                </form>

                @if(!$category->hasApplications())
                    <form action="{{ route('categories.destroy', $category) }}" method="POST"
                          onsubmit="return confirm('Permanently delete this category?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 border border-red-500 text-red-500 rounded hover:bg-red-50">
                            Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
