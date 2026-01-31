@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[80%] py-8 space-y-8">

            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900">Application Rounds</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        View and manage the timeline for student award submissions.
                    </p>
                </div>
                {{-- Only show if you want a button to create new rounds --}}
                <a href="{{ route('rounds.create') }}" class="btn btn-primary">
                    + New Round
                </a>
            </div>

            {{-- Rounds Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($rounds as $round)
                    <div class="relative flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-hover hover:shadow-md">

                        {{-- Round Status Badge --}}
                        <div class="mb-4">
                            @php
                                $statusLabel = $round->status->name; // OPEN or CLOSED
                                $is_open = $round->status === \App\Enums\RoundStatus::OPEN;
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border
                                {{ $is_open ? 'bg-green-100 text-green-800 border-green-200' : 'bg-slate-100 text-slate-800 border-slate-200' }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        {{-- Year and Semester --}}
                        <div class="mb-2">
                            <h2 class="text-xl font-bold text-slate-900">
                                <a href="{{ route('rounds.show', $round) }}" class="hover:text-primary transition-colors">
                                    Academic Year {{ $round->academic_year }}
                                    <span class="text-slate-400 group-hover:translate-x-1 inline-block transition-transform">→</span>
                                </a>                            </h2>
                            <p class="text-sm font-medium text-slate-500">
                                Semester {{ $round->semester->value }}
                            </p>
                        </div>

                        {{-- Timeline Info --}}
                        <div class="mt-4 space-y-2 border-t border-slate-100 pt-4">
                            <div class="flex items-center text-sm text-slate-600">
                                <span class="w-12 font-semibold">Start:</span>
                                <span>{{ $round->start_time->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex items-center text-sm text-slate-600">
                                <span class="w-12 font-semibold">End:</span>
                                <span>{{ $round->end_time->format('d M Y, H:i') }}</span>
                            </div>
                        </div>

                        {{-- Dynamic "Days Remaining" or "Closed" info --}}
                        <div class="mt-6 flex items-center justify-between">
                            @if($is_open)
                                <div class="text-xs font-bold text-primary">
                                    {{ $round->days_left }} Days Remaining
                                </div>
                            @endif
                            <a href="{{ route('rounds.edit', $round) }}" class="text-sm font-semibold text-slate-700 hover:text-primary">
                                Edit Details &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-xl border-2 border-dashed border-slate-200 p-12 text-center">
                        <p class="text-slate-500">No application rounds found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
