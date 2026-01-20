@extends('layouts.main')

@section('content')
    @php
        $isEdit = $application->exists;
        $route  = $isEdit
            ? route('applications.update', $application)
            : route('applications.store');

        // 2-step flow
        $steps = [
            ['title' => 'Select Student'],
            ['title' => 'Award Category'],
        ];
        $currentStep = 1; // set 1 or 2 based on your flow

        // Small class helpers
        $card = 'bg-white border border-gray-200 rounded-2xl p-6 shadow-sm';
        $title = 'text-gray-900 font-semibold';
        $muted = 'text-gray-500';
    @endphp

    <section class="bg-background">
        <div class="container mx-auto w-[80%] py-6">

            {{-- Header --}}
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Application Setup</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Select the student candidate and choose the award category to begin.
                </p>
            </div>

            {{-- Stepper (2 steps) --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm mb-6">
                <div class="flex items-center gap-4">
                    @foreach($steps as $i => $step)
                        @php
                            $stepNumber = $i + 1;
                            $isActive = $stepNumber === $currentStep;
                            $isDone   = $stepNumber < $currentStep;

                            $dotClass = $isDone
                                ? 'bg-approved text-white'
                                : ($isActive ? 'bg-primary text-white' : 'bg-gray-200 text-gray-600');

                            $lineClass = $isDone ? 'bg-approved' : 'bg-gray-200';
                            $labelClass = $isActive ? 'text-gray-900' : 'text-gray-500';
                        @endphp

                        <div class="flex items-center flex-1 min-w-0">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold {{ $dotClass }}">
                                    {{ $stepNumber }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold {{ $labelClass }}">
                                        {{ $step['title'] }}
                                    </div>
                                </div>
                            </div>

                            @if($stepNumber !== count($steps))
                                <div class="flex-1 h-[2px] mx-4 {{ $lineClass }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Main content --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Left: Select Student --}}
                <div class="{{ $card }}">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">1</div>
                        <h2 class="text-lg {{ $title }}">Select Student</h2>
                    </div>

                    {{-- Search --}}
                    <div class="mb-4">
                        <label class="sr-only" for="student_search">Search</label>
                        <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                <path d="M21 21l-4.3-4.3m1.8-5.2a7 7 0 11-14 0 7 7 0 0114 0z"
                                      stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>

                            <input
                                id="student_search"
                                type="text"
                                placeholder="Search by student name, ID, or faculty..."
                                class="w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl
                                   focus:ring-2 focus:ring-primary focus:border-primary"
                            />
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-hidden border border-gray-200 rounded-xl">
                        <div class="grid grid-cols-12 bg-gray-50 px-4 py-2 text-xs font-semibold text-gray-500">
                            <div class="col-span-4">NAME</div>
                            <div class="col-span-3">STUDENT ID</div>
                            <div class="col-span-3">FACULTY</div>
                            <div class="col-span-2 text-right">ACTION</div>
                        </div>

                        {{-- Row (selected example) --}}
                        <div class="grid grid-cols-12 px-4 py-3 items-center border-t border-gray-200">
                            <div class="col-span-4 text-sm font-medium text-gray-900">Marcus Thorne</div>
                            <div class="col-span-3 text-sm text-gray-600">ST-2023-0892</div>
                            <div class="col-span-3 text-sm text-gray-600">Science & Tech</div>
                            <div class="col-span-2 text-right">
                            <span class="inline-flex items-center gap-2 text-approved text-sm font-semibold">
                                Selected
                                <span class="w-5 h-5 rounded-full bg-approved text-white inline-flex items-center justify-center">✓</span>
                            </span>
                            </div>
                        </div>

                        {{-- Row --}}
                        <div class="grid grid-cols-12 px-4 py-3 items-center border-t border-gray-200">
                            <div class="col-span-4 text-sm font-medium text-gray-900">Sarah Jenkins</div>
                            <div class="col-span-3 text-sm text-gray-600">ST-2023-0041</div>
                            <div class="col-span-3 text-sm text-gray-600">Faculty of Arts</div>
                            <div class="col-span-2 text-right">
                                <button
                                    type="button"
                                    class="px-3 py-1.5 rounded-lg border border-primary text-primary text-sm font-semibold
                                       hover:bg-primary/10"
                                >
                                    Select
                                </button>
                            </div>
                        </div>
                    </div>

                    <p class="mt-4 text-xs {{ $muted }}">
                        Tip: You can later replace this table with your real loop and “selected student” state.
                    </p>
                </div>

                {{-- Right: Award Category --}}
                <div class="{{ $card }}">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">2</div>
                        <h2 class="text-lg {{ $title }}">Award Category</h2>
                    </div>

                    <form action="{{ $route }}" method="POST" class="space-y-3">
                        @csrf
                        @if($isEdit) @method('PUT') @endif

                        @foreach($categories as $category)
                            @php
                                $value = $category->value;
                                $checked = old('category', $application->category?->value ?? $application->category) == $value;
                            @endphp

                            <label class="block">
                                <input
                                    type="radio"
                                    name="category"
                                    value="{{ $value }}"
                                    class="peer sr-only"
                                    {{ $checked ? 'checked' : '' }}
                                />

                                <div class="flex items-center justify-between gap-4 border rounded-2xl p-4 bg-white
                                        border-gray-200
                                        hover:border-primary/60
                                        peer-checked:border-primary peer-checked:bg-primary/10">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-600
                                                peer-checked:bg-primary/15 peer-checked:text-primary">
                                            <span class="text-lg">★</span>
                                        </div>

                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ ucfirst(strtolower($category->name)) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Choose this category for the application.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center
                                            peer-checked:border-primary peer-checked:bg-primary">
                                        <span class="text-white text-xs peer-checked:inline hidden">✓</span>
                                    </div>
                                </div>
                            </label>
                        @endforeach

                        @error('category')
                        <p class="text-xs text-rejected">{{ $message }}</p>
                        @enderror

                        {{-- Bottom actions --}}
                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100">
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 inline-flex items-center gap-2">
                                ← Back to Dashboard
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 bg-primary hover:opacity-95 text-white
                                   px-5 py-2.5 rounded-xl font-semibold shadow-sm"
                            >
                                Continue
                                →
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
@endsection
