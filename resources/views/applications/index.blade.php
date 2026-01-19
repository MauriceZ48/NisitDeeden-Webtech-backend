@extends('layouts.main')

@section('content')
    <section class="container mx-auto w-[80%]">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium tracking-wide">Total Applications</h3>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-3xl font-bold text-gray-900">{{ count($applications) }}</span>
                    </div>
                    <p class="text-gray-400 text-xs mt-1">From ___ academic year</p>
                </div>
                <div class="bg-blue-50 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4zM3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" /></svg>
                </div>
            </div>
        </div>
        <h1>Applications</h1>
        <div class="my-2">
            <a href="{{ route('applications.create') }}" class="px-4 py-2 border bg-blue-200">
                + Application
            </a>
        </div>

        <table class="w-full border-collapse border border-gray-200 shadow-sm rounded-lg overflow-hidden">
            <thead class="bg-gray-50 text-gray-700 text-sm uppercase">
            <tr>
                <th class="px-6 py-3 border-b text-left font-semibold">#</th>
                <th class="px-6 py-3 border-b text-left font-semibold">ID</th>
                <th class="px-6 py-3 border-b text-left font-semibold">User</th>
                <th class="px-6 py-3 border-b text-left font-semibold">Category</th>
                <th class="px-6 py-3 border-b text-left font-semibold">Created At</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
            {{-- The @forelse starts here --}}
            @forelse($applications as $application)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-600">
                        #{{ $loop->iteration }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                        <a href="{{ route('applications.show', ['application' => $application]) }}" class="text-blue-600 hover:underline">
                            {{ $application->id }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        #{{ $application->user->name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-bold ">
                        {{ ucfirst(strtolower($application->category->value))}}
                    </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 italic">
                        {{ $application->created_at->format('M d, Y') }}
                    </td>
                </tr>
            @empty
                {{-- This code runs ONLY if $applications is empty --}}
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-medium">No applications found</p>
                            <p class="text-sm">Start by creating a new application above.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

    </section>


@endsection
