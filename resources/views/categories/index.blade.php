@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[60%] py-8 space-y-8">
            <h1 class="text-3xl font-bold text-gray-800">Application Category</h1>
            <p class="text-gray-600">Manage category.</p>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <a href="{{ route('categories.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg font-bold">+ New Category</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('categories.show', $category->slug) }}">
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col items-center text-center space-y-4">

                        <h2 class="text-xl font-semibold text-gray-900">{{ $category->name }}</h2>
                        <div class="text-primary">
                            {{-- We strip "lucide:" from the string so "lucide:lightbulb" becomes "lightbulb" --}}
                            <i data-lucide="{{ str_replace('lucide:', '', $category->icon) }}" class="w-10 h-10"></i>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-3">{{ $category->description }}</p>

                    </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <script>
        lucide.createIcons();
    </script>
@endsection
