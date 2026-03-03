@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[60%] py-8 space-y-8">
            {{-- Header --}}
            <div>
                <a href="{{ route('rounds.index') }}" class="text-sm text-slate-500 hover:text-primary">&larr; Back to Rounds</a>
                <h1 class="text-3xl font-extrabold text-slate-900 mt-2">Create New Round</h1>
                <p class="text-slate-500">Set up the next academic cycle for award nominations.</p>
            </div>@extends('layouts.main')

            @section('content')
                <section class="bg-background">
                    <div class="container mx-auto w-[60%] py-8 space-y-8">
                        {{-- Header --}}
                        <div>
                            <a href="{{ route('rounds.index') }}" class="text-sm text-slate-500 hover:text-primary">&larr; Back to Rounds</a>
                            <h1 class="text-3xl font-extrabold text-slate-900 mt-2">Create New Round</h1>
                            <p class="text-slate-500">Set up the next academic cycle for award nominations.</p>
                        </div>

                        <form action="{{ route('rounds.store') }}" method="POST" class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-6">
                            @csrf

                            {{-- Row 1: Read-only Year & Semester (Enforcing Sequence) --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700">Academic Year</label>
                                    <input type="text" name="academic_year" value="{{ $expectedYear }}" readonly
                                           class="mt-1 block w-full rounded-lg border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed focus:ring-0">
                                    <p class="mt-1 text-xs text-slate-400 font-medium">Automatically set to maintain sequence.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700">Semester</label>
                                    {{-- We send the hidden value but show the label for UX --}}
                                    <input type="hidden" name="semester" value="{{ $expectedSemester->value }}">
                                    <input type="text" value="Semester {{ $expectedSemester->value }}" readonly
                                           class="mt-1 block w-full rounded-lg border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                                </div>
                            </div>

                            {{-- Row 2: Status Dropdown --}}
                            <div class="w-full">
                                <label for="status" class="block text-sm font-semibold text-slate-700">Initial Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20">
                                    <option value="{{ \App\Enums\RoundStatus::DRAFT->value }}">Draft Mode</option>
                                    <option value="{{ \App\Enums\RoundStatus::OPEN->value }}">Open (Go Live Immediately)</option>
                                </select>
                                @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- Row 3: Calendars (Period) --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="start_time" class="block text-sm font-semibold text-slate-700">Start Date & Time</label>
                                    <input type="datetime-local" name="start_time" id="start_time" required
                                           value="{{ old('start_time') }}"
                                           class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20">
                                    @error('start_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="end_time" class="block text-sm font-semibold text-slate-700">End Date & Time</label>
                                    <input type="datetime-local" name="end_time" id="end_time" required
                                           value="{{ old('end_time') }}"
                                           class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20">
                                    @error('end_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Error Alert for Logic Guards (e.g., Another Round Active) --}}
                            @if($errors->has('academic_year'))
                                <div class="p-4 bg-red-50 border border-red-100 rounded-xl text-red-700 text-sm">
                                    {{ $errors->first('academic_year') }}
                                </div>
                            @endif

                            {{-- Submit Button --}}
                            <div class="pt-4">
                                <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:opacity-90 transition-opacity shadow-sm">
                                    Create Application Round
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            @endsection


            <form action="{{ route('rounds.store') }}" method="POST" class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-6">
                @csrf

                {{-- Row 1: Read-only Year & Semester (Enforcing Sequence) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Academic Year</label>
                        <input type="text" name="academic_year" value="{{ $expectedYear }}" readonly
                               class="mt-1 block w-full rounded-lg border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed focus:ring-0">
                        <p class="mt-1 text-xs text-slate-400 font-medium">Automatically set to maintain sequence.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Semester</label>
                        {{-- We send the hidden value but show the label for UX --}}
                        <input type="hidden" name="semester" value="{{ $expectedSemester->value }}">
                        <input type="text" value="Semester {{ $expectedSemester->value }}" readonly
                               class="mt-1 block w-full rounded-lg border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                    </div>
                </div>

                {{-- Row 2: Status Dropdown --}}
                <div class="w-full">
                    <label for="status" class="block text-sm font-semibold text-slate-700">Initial Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20">
                        <option value="{{ \App\Enums\RoundStatus::CLOSED->value }}">Closed (Draft Mode)</option>
                        <option value="{{ \App\Enums\RoundStatus::OPEN->value }}">Open (Go Live Immediately)</option>
                    </select>
                    @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Row 3: Calendars (Period) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_time" class="block text-sm font-semibold text-slate-700">Start Date & Time</label>
                        <input type="datetime-local" name="start_time" id="start_time" required
                               value="{{ old('start_time') }}"
                               class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20">
                        @error('start_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-semibold text-slate-700">End Date & Time</label>
                        <input type="datetime-local" name="end_time" id="end_time" required
                               value="{{ old('end_time') }}"
                               class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20">
                        @error('end_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Error Alert for Logic Guards (e.g., Another Round Active) --}}
                @if($errors->has('academic_year'))
                    <div class="p-4 bg-red-50 border border-red-100 rounded-xl text-red-700 text-sm">
                        {{ $errors->first('academic_year') }}
                    </div>
                @endif

                {{-- Submit Button --}}
                <div class="pt-4">
                    <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:opacity-90 transition-opacity shadow-sm">
                        Create Application Round
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
