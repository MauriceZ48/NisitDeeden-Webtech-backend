@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[60%] py-8 space-y-8">
            {{-- Breadcrumbs & Header --}}
            <div>
                <a href="{{ route('rounds.index') }}" class="text-sm text-slate-500 hover:text-primary">&larr; Back to Rounds</a>
                <h1 class="text-3xl font-extrabold text-slate-900 mt-2">Edit Application Round</h1>
                <p class="text-slate-500">Update the timeline or status for the {{ $applicationRound->academic_year }} cycle.</p>
            </div>

            <form action="{{ route('rounds.update', ['applicationRound' => $applicationRound]) }}" method="POST" class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-6">
                @csrf
                @method('PUT')

                {{-- Row 1: Read-only Year & Semester --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Academic Year</label>
                        {{-- Visible field for the user (No name attribute) --}}
                        <input type="text" value="{{ $applicationRound->academic_year }}" readonly
                               class="mt-1 block w-full rounded-lg border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">

                        {{-- Hidden field for the server --}}
                        <input type="hidden" name="academic_year" value="{{ $applicationRound->academic_year }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Semester</label>
                        {{-- Visible field for the user --}}
                        <input type="text" value="Semester {{ $applicationRound->semester->value }}" readonly
                               class="mt-1 block w-full rounded-lg border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">

                        {{-- Hidden field for the server --}}
                        <input type="hidden" name="semester" value="{{ $applicationRound->semester->value }}">
                    </div>
                </div>

                {{-- Row 2: Status (Open/Closed) --}}
                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700">Round Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20">
                        @foreach(\App\Enums\RoundStatus::cases() as $status)
                            <option value="{{ $status->value }}"
                                {{ old('status', $applicationRound->status->value) === $status->value ? 'selected' : '' }}>
                                {{ ucfirst(strtolower($status->name)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Row 3: Calendars (Period) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_time" class="block text-sm font-semibold text-slate-700">Start Date & Time</label>
                        <input type="datetime-local" name="start_time" id="start_time" required
                               value="{{ old('start_time', $applicationRound->start_time->format('Y-m-d\TH:i')) }}"
                               class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20">
                        @error('start_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-semibold text-slate-700">End Date & Time</label>
                        <input type="datetime-local" name="end_time" id="end_time" required
                               value="{{ old('end_time', $applicationRound->end_time->format('Y-m-d\TH:i')) }}"
                               class="mt-1 block w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20">
                        @error('end_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-4 flex items-center justify-between gap-4">
                    <button type="submit" class="flex-1 bg-primary text-white font-bold py-3 rounded-xl hover:opacity-90 transition-opacity shadow-sm">
                        Update Round
                    </button>
                    <a href="{{ route('rounds.index') }}" class="px-6 py-3 text-sm font-semibold text-slate-600 hover:text-slate-900">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
@endsection
