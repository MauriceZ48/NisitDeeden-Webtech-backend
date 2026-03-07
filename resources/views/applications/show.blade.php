@php
    use App\Enums\ApplicationCategory;

    /** @var \App\Models\Application $application */

    $categoryLabel = $application->category?->label()
        ?? ($application->category?->value ?? '—');

    $user = $application->user;

    // If you have status enum later: $statusLabel = $application->status?->label() ?? '—';

    $createdAt = $application->created_at?->format('d M Y, H:i') ?? '—';
    $updatedAt = $application->updated_at?->format('d M Y, H:i') ?? '—';

    $backUrl = request('return_url') ?? url()->previous();

    // Small UI helpers
    $badge = fn ($cls) => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold border {$cls}";
    $card  = "rounded-2xl border border-slate-200 bg-white shadow-sm";
@endphp

@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[90%] lg:w-[80%] py-10 space-y-6">

            {{-- Top bar --}}
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('applications.index') }}"
                           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15.5 5 8.5 12l7 7 1.5-1.5L11.5 12 17 6.5 15.5 5z"/>
                            </svg>
                            Back
                        </a>

{{--                        <span class="{{ $badge('bg-primary/10 text-primary border-primary/20') }}">--}}
{{--                            Application #{{ $application->id }}--}}
{{--                        </span>--}}

{{--                        <span class="{{ $badge('bg-slate-50 text-slate-700 border-slate-200') }}">--}}
{{--                            {{ $categoryLabel }}--}}
{{--                        </span>--}}
                    </div>

                    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Excellence Award Application
                    </h1>
                    <p class="text-slate-500">
                        View application details, student info, and attachments.
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
{{--                    <a href="{{ route('applications.edit', ['application' => $application]) }}"--}}
{{--                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/20">--}}
{{--                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">--}}
{{--                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm18-11.5a1 1 0 0 0 0-1.41l-1.34-1.34a1 1 0 0 0-1.41 0l-1.13 1.13 3.75 3.75L21 5.75z"/>--}}
{{--                        </svg>--}}
{{--                        Edit--}}
{{--                    </a>--}}

{{--                    <form onsubmit="return confirm('Are you sure you want to delete this application?')"--}}
{{--                          action="{{ route('applications.destroy', ['application' => $application]) }}"--}}
{{--                          method="POST">--}}
{{--                        @csrf--}}
{{--                        @method('DELETE')--}}

{{--                        <button type="submit"--}}
{{--                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-5 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-200">--}}
{{--                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">--}}
{{--                                <path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"/>--}}
{{--                            </svg>--}}
{{--                            Delete--}}
{{--                        </button>--}}
{{--                    </form>--}}
                </div>
            </div>

            {{-- Main grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT: details --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Overview card --}}
                    <div class="{{ $card }}">
                        <div class="p-6 md:p-8 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Overview</h2>
                                <p class="text-sm text-slate-500">Basic information about this application.</p>
                            </div>

                            {{-- Dynamic Status Badge using your Enum methods --}}
                            @if($application->status)
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-bold border {{ $application->status->color() }}">
                {{ $application->status->label() }}
            </span>
                            @endif
                        </div>

                        <div class="p-6 md:p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {{-- Status Detail --}}
                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Current Status</p>
                                    <p class="mt-1 text-sm font-bold {{ $application->status ? explode(' ', $application->status->color())[1] : 'text-slate-900' }}">
                                        {{ $application->status ? $application->status->label() : '—' }}
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $application->applicationCategory?->name ?? '—' }}</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Academic Year</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">
                                        {{ $application->applicationRound?->academic_year ?? '—' }} (Sem {{ $application->applicationRound?->semester ?? '—' }})
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Submission Date</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $createdAt }}</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Last Activity</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $updatedAt }}</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Reference ID</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">#{{ $application->id }}</p>
                                </div>
                            </div>

                            {{-- Rejection Note (Only shows if status is REJECTED or a reason is typed) --}}
                            @if($application->status === \App\Enums\ApplicationStatus::REJECTED || $application->rejection_reason)
                                <div class="mt-6 rounded-2xl border border-red-100 bg-red-50/50 p-5">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <p class="text-xs font-bold text-red-700 uppercase tracking-wider">Decision Note</p>
                                    </div>
                                    <p class="text-sm text-red-800 leading-relaxed">{{ $application->rejection_reason ?: 'No reason provided.' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Dynamic Form Answers --}}
                    <div class="{{ $card }}">
                        <div class="p-6 md:p-8 border-b border-slate-100">
                            <h2 class="text-lg font-semibold text-slate-900">Application Details</h2>
                            <p class="text-sm text-slate-500">Specific information provided for this category.</p>
                        </div>

                        <div class="p-6 md:p-8 space-y-4">
                            @forelse($application->attributeValues as $answer)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-5">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        {{ $answer->attribute?->label ?? 'Custom Field' }}
                                    </p>

                                    <div class="mt-2">
                                        @if($answer->attribute?->type === 'file')
                                            @if($answer->value)
                                                <div class="flex items-center justify-between gap-4 rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3">
                                                    <div class="flex items-center gap-3 overflow-hidden">
                                                        <svg class="h-5 w-5 text-blue-600 flex-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                            <polyline points="13 2 13 9 20 9"></polyline>
                                                        </svg>
                                                        <span class="text-sm font-semibold text-blue-900 truncate">
                                        {{ basename($answer->value) }}
                                    </span>
                                                    </div>
                                                    <a href="{{ asset('storage/' . $answer->value) }}" target="_blank"
                                                       class="text-xs font-bold text-blue-700 hover:underline flex-none">
                                                        View File
                                                    </a>
                                                </div>
                                            @else
                                                <p class="text-sm italic text-slate-400">No file uploaded</p>
                                            @endif
                                        @else
                                            <p class="text-sm font-medium text-slate-900 leading-relaxed">
                                                {{ $answer->value ?: '—' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-sm text-slate-500">No additional form data available.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Attachments --}}
                    <div class="{{ $card }}">
                        <div class="p-6 md:p-8 border-b border-slate-100 flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Attachments</h2>
                                <p class="text-sm text-slate-500">Files uploaded to support the nomination.</p>
                            </div>

                            <span class="{{ $badge('bg-slate-50 text-slate-700 border-slate-200') }}">
                                {{ $application->attachments?->count() ?? 0 }} file(s)
                            </span>
                        </div>

                        <div class="p-6 md:p-8">
                            @if(($application->attachments?->count() ?? 0) === 0)
                                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/40 p-8 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white border border-slate-200">
                                        <svg class="h-6 w-6 text-slate-600" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 15v4H5v-4H3v6h18v-6h-2zM11 3h2v10h3l-4 4-4-4h3V3z"/>
                                        </svg>
                                    </div>
                                    <p class="mt-3 text-sm font-semibold text-slate-800">No attachments uploaded</p>
                                    <p class="mt-1 text-xs text-slate-500">You can add files by editing this application.</p>
                                </div>
                            @else
                                <div class="space-y-2">
                                    @foreach($application->attachments as $file)
                                        @php
                                            $name = $file->file_name ?? 'File';
                                            $path = $file->file_path ?? null;
                                            $sizeKb = isset($file->file_size) ? round($file->file_size / 1024, 2) : null;
                                            $ext = strtoupper(pathinfo($name, PATHINFO_EXTENSION));
                                            $ext = $ext !== '' ? $ext : 'FILE';
                                        @endphp

                                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="h-10 w-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center flex-none">
                                                    <span class="text-[11px] font-extrabold text-slate-700">
                                                        {{ strlen($ext) <= 4 ? $ext : 'FILE' }}
                                                    </span>
                                                </div>

                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $name }}</p>
                                                    <p class="text-xs text-slate-500">
                                                        {{ $sizeKb !== null ? $sizeKb.' KB' : '—' }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if($path)
                                                <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex-none">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3z"/>
                                                        <path d="M5 5h6V3H3v8h2V5zm0 14v-6H3v8h8v-2H5zm14 0h-6v2h8v-8h-2v6z"/>
                                                    </svg>
                                                    Open
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- RIGHT: student card --}}
                <div class="space-y-6">

                    <div class="{{ $card }}">
                        <div class="p-6 md:p-7 border-b border-slate-100">
                            <h2 class="text-lg font-semibold text-slate-900">Student Profile</h2>
                            <p class="text-sm text-slate-500">Owner of this application.</p>
                        </div>

                        <div class="p-6 md:p-7 space-y-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-4 min-w-0">
                                    @php
                                        $pic = $user->profile_url ?? null;
                                        $initials = collect(explode(' ', trim($user->name ?? 'User')))
                                            ->filter()
                                            ->take(2)
                                            ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                                            ->implode('');
                                    @endphp

                                    @if($pic)
                                        <img src="{{ $pic }}" alt="Profile"
                                             class="h-14 w-14 rounded-2xl object-cover border border-slate-200 bg-white flex-none">
                                    @else
                                        <div class="h-14 w-14 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-sm font-extrabold text-slate-600 flex-none">
                                            {{ $initials ?: 'U' }}
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <p class="text-base font-extrabold text-slate-900 truncate">{{ $user->name ?? '—' }}</p>
                                        <p class="text-sm text-slate-500 truncate">
                                            ID: <span class="font-semibold text-slate-700">{{ $user->university_id ?? '—' }}</span>
                                        </p>
                                    </div>
                                </div>

{{--                                --}}{{-- OPTIONAL: role badge (right-most) --}}
{{--                                @if(isset($user->role))--}}
{{--                                    @php--}}
{{--                                        // normalize role value (enum -> value)--}}
{{--                                        $roleValue = $user->role instanceof \App\Enums\UserRole--}}
{{--                                            ? $user->role->value--}}
{{--                                            : (string) $user->role;--}}

{{--                                        // label (pretty text)--}}
{{--                                        $roleLabel = $user->role instanceof \App\Enums\UserRole--}}
{{--                                            ? $user->role->label()--}}
{{--                                            : $roleValue;--}}

{{--                                        // styles by role value--}}
{{--                                        $roleStyle = match($roleValue) {--}}
{{--                                            'ADMIN'   => 'bg-red-50 text-red-700 border-red-200',--}}
{{--                                            'STAFF'   => 'bg-amber-50 text-amber-700 border-amber-200',--}}
{{--                                            'STUDENT' => 'bg-emerald-50 text-emerald-700 border-emerald-200',--}}
{{--                                            default   => 'bg-slate-50 text-slate-700 border-slate-200',--}}
{{--                                        };--}}
{{--                                    @endphp--}}

{{--                                    <span class="{{ $badge($roleStyle) }} flex-none">--}}
{{--                                        {{ $roleLabel }}--}}
{{--                                    </span>--}}
{{--                                @endif--}}

                            </div>

                            <div class="grid grid-cols-1 gap-3">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500">Faculty</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $user->faculty ?? '—' }}</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500">Department</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $user->department ?? '—' }}</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500">Email</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900 break-all">{{ $user->email ?? '—' }}</p>
                                </div>
                            </div>

{{--                            --}}{{-- Quick actions --}}
{{--                            <div class="pt-2 flex items-center gap-2">--}}
{{--                                <a href="mailto:{{ $user->email ?? '' }}"--}}
{{--                                   class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">--}}
{{--                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">--}}
{{--                                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/>--}}
{{--                                    </svg>--}}
{{--                                    Email--}}
{{--                                </a>--}}

{{--                                <a href="{{ route('applications.edit', ['application' => $application]) }}"--}}
{{--                                   class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">--}}
{{--                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">--}}
{{--                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm18-11.5a1 1 0 0 0 0-1.41l-1.34-1.34a1 1 0 0 0-1.41 0l-1.13 1.13 3.75 3.75L21 5.75z"/>--}}
{{--                                    </svg>--}}
{{--                                    Edit--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>



                </div>
            </div>
        </div>
    </section>
@endsection
