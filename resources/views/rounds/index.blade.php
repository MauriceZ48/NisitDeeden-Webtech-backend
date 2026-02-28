@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[80%] py-8 space-y-8">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900">Application Rounds</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage timelines for student award submissions.</p>
                </div>
                <a href="{{ route('rounds.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg font-bold">+ New Round</a>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($rounds as $applicationRound) {{-- Standardized variable --}}
                <div class="relative flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                    @php $is_open = $applicationRound->status === \App\Enums\RoundStatus::OPEN; @endphp

                    <div class="mb-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border
                                {{ $is_open ? 'bg-green-100 text-green-800 border-green-200' : 'bg-slate-100 text-slate-800 border-slate-200' }}">
                                {{ $applicationRound->status->name }}
                            </span>
                    </div>

                    <div class="mb-2">
                        <a href="{{route('rounds.show', $applicationRound)}}">
                        <h2 class="text-xl font-bold text-slate-900">
                            Academic Year {{ $applicationRound->academic_year }}
                        </h2>
                        </a>
                        <p class="text-sm font-medium text-slate-500">Semester {{ $applicationRound->semester->value }}</p>
                    </div>

                    <div class="mt-4 space-y-2 border-t border-slate-100 pt-4">
                        <div class="flex items-center text-sm text-slate-600">
                            <span class="w-12 font-semibold">Start:</span>
                            <span>{{ $applicationRound->start_time->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex items-center text-sm text-slate-600">
                            <span class="w-12 font-semibold">End:</span>
                            <span>{{ $applicationRound->end_time->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    {{-- Footer with Count and Standardized Delete --}}
                    <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-500 uppercase">{{ $applicationRound->applications_count }} Apps</span>
                            @if($is_open)
                                <div class="text-xs font-bold text-primary">{{ $applicationRound->days_left }} Days Left</div>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('rounds.edit', $applicationRound) }}" class="text-sm font-semibold text-slate-700 hover:text-primary">Edit</a>

                            <form action="{{ route('rounds.destroy', ['applicationRound' => $applicationRound]) }}" method="POST"
                                  onsubmit="return confirm('Permanently delete this round?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        @if($applicationRound->applications_count > 0) disabled @endif
                                        class="text-sm font-semibold {{ $applicationRound->applications_count > 0 ? 'text-slate-300' : 'text-red-500 hover:text-red-700' }}">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="col-span-full p-12 text-center border-2 border-dashed rounded-xl">No rounds found.</div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
