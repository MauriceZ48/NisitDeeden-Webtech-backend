@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[80%] py-8 space-y-8">

            {{-- Page header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900">Applications</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Manage and review submitted applications.
                    </p>
                </div>

                <a href="{{ route('applications.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <span class="text-lg leading-none">+</span>
                    New Application
                </a>
            </div>

            {{-- Summary cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                {{-- Total --}}
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total
                                Applications</p>
                            <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $totalCount }}</p>
                            <p class="mt-1 text-xs text-slate-400">All submitted applications</p>
                        </div>
                        {{--                        <div class="rounded-xl bg-primary/10 p-3 text-primary">--}}
                        {{--                            --}}{{-- icon --}}
                        {{--                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">--}}
                        {{--                                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4zM3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />--}}
                        {{--                            </svg>--}}
                        {{--                        </div>--}}
                    </div>
                    {{--                    <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-primary/10"></div>--}}
                </div>

                {{-- Pending (safe: compute from collection, no DB needed) --}}
                {{--                @php--}}
                {{--                    $pendingCount = $applications->filter(function ($a) {--}}
                {{--                        // adjust these to your real field/value names if you have them--}}
                {{--                        return isset($a->status) && in_array(strtolower($a->status), ['pending', 'pending review']);--}}
                {{--                    })->count();--}}
                {{--                @endphp--}}
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $pendingCount }}</p>
                    <p class="mt-1 text-xs text-slate-400">Awaiting review</p>
                    <div class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full"
                         style="background: color-mix(in oklab, theme(colors.pending) 20%, transparent);"></div>
                </div>

                {{-- Approved (same idea) --}}
                {{--                @php--}}
                {{--                    $approvedCount = $applications->filter(function ($a) {--}}
                {{--                        return isset($a->status) && in_array(strtolower($a->status), ['approved', 'accepted']);--}}
                {{--                    })->count();--}}
                {{--                @endphp--}}
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Approved</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $approvedCount }}</p>
                    <p class="mt-1 text-xs text-slate-400">Finalized applications</p>
                    <div class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full"
                         style="background: color-mix(in oklab, theme(colors.approved) 18%, transparent);"></div>
                </div>
            </div>

            {{-- Table card --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">



                {{-- Toolbar --}}
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-200 bg-slate-50/60 p-4">

                    {{-- ✅ ครอบทั้ง search + buttons --}}
                    <form method="GET"
                          action="{{ route('applications.index') }}"
                          class="w-full flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                        <div class="w-full lg:w-96">
                            <label class="relative block">
                                <span class="sr-only">Search</span>
                                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                              d="M9 3a6 6 0 104.472 10.03l2.249 2.25a1 1 0 001.414-1.415l-2.25-2.249A6 6 0 009 3zm-4 6a4 4 0 118 0 4 4 0 01-8 0z"
                              clip-rule="evenodd"/>
                    </svg>
                </span>

                                <input
                                    name="q"
                                    value="{{ request('q') }}"
                                    class="block w-full rounded-lg border-slate-200 bg-white pl-10 pr-3 py-2 text-sm placeholder:text-slate-400 focus:border-primary focus:ring-primary/20"
                                    placeholder="Search by ID, user, email, category, status..."
                                    type="text"
                                />
                            </label>

                            @if(request('q'))
                                <div class="mt-2">
                                    <a href="{{ route('applications.index') }}"
                                       class="text-xs font-semibold text-slate-500 hover:text-slate-700">
                                        Clear search
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- ✅ อยู่ใน form แล้ว submit ได้ --}}
                        <div class="flex gap-2 justify-end">
                            <button type="submit"
                                    class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                Apply
                            </button>

                            <a href="{{ route('applications.index') }}"
                               class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Reset
                            </a>
                        </div>

                    </form>
                </div>



                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
{{--                            <th class="px-6 py-3">#</th>--}}
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Status</th> {{-- NEW --}}
                            <th class="px-6 py-3">Created At</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                        </thead>


                        <tbody class="divide-y divide-slate-100 bg-white">

                        @forelse($applications as $application)
                            <tr class="hover:bg-slate-50/70 transition-colors">
{{--                                <td class="px-6 py-4 text-sm text-slate-500">--}}
{{--                                    #{{ $loop->iteration }}--}}
{{--                                </td>--}}

                                <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                    <a href="{{ route('applications.show', ['application' => $application]) }}"
                                       class="text-primary hover:underline">
                                        {{ $application->id }}
                                    </a>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-slate-200 overflow-hidden border border-gray-100">
                                            <img src="{{ $application->user->profile_url }}"
                                                 alt="{{ $application->user->name }}"
                                                 class="h-full w-full object-cover">
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ $application->user->name }}</p>
                                            {{-- optional: email --}}
                                            @if(!empty($application->user->email))
                                                <p class="text-xs text-slate-500">{{ $application->user->email }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full border border-primary/20 bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">
                                        {{ ucfirst(strtolower($application->category->value)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php

                                        $status = $application->status?->label() ?? 'pending';

                                       $statusStyles = [
                                           'Pending'  => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                           'Approved' => 'bg-green-100 text-green-700 border-green-200',
                                           'Rejected' => 'bg-red-100 text-red-700 border-red-200',
                                       ];
                                    @endphp

                                    <span
                                        class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold
                                        {{ $statusStyles[$status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>


                                <td class="px-6 py-4 text-sm text-slate-500">
                                    {{ $application->created_at->format('M d, Y') }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="relative inline-block text-left"
                                         x-data="{ open: false }"
                                         @keydown.escape.window="open = false">

                                        {{-- Trigger (⋯) --}}
                                        <button type="button"
                                                @click="open = !open"
                                                class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </button>

                                        {{-- Dropdown --}}
                                        <div x-cloak
                                             x-show="open"
                                             @click.outside="open = false"
                                             x-transition
                                             class="absolute right-0 z-20 mt-2 w-40 origin-top-right rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden">

                                            <a href="{{ route('applications.show', $application) }}"
                                               class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                                View
                                            </a>

                                            <a href="{{ route('applications.edit', $application)  }}"
                                               class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                                Edit
                                            </a>

                                            <div class="border-t border-slate-100"></div>

                                            <form method="POST"
                                                  action="{{ route('applications.destroy', $application) }}"
                                                  onsubmit="return confirm('Delete this application?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="w-full text-left px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>



                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-14 text-center">
                                    <div class="mx-auto max-w-sm">
                                        <div
                                            class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-base font-semibold text-slate-900">No applications found</p>
                                        <p class="mt-1 text-sm text-slate-500">Create a new application to get
                                            started.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer (optional summary) --}}
                <div
                    class="flex items-center justify-between border-t border-slate-200 px-6 py-4 text-sm text-slate-500">
                    <span>Total: <span class="font-semibold text-slate-900">{{ $totalCount }}</span></span>
                    {{-- If you later use pagination, replace with {{ $applications->links() }} --}}
                    <span class="text-xs">{{ $applications->appends(request()->query())->links() }}</span>

                </div>
            </div>

        </div>
    </section>
@endsection
