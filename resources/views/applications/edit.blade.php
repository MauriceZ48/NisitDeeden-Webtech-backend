@extends('layouts.main')

@section('content')
    <section class="bg-background">
        <div class="container mx-auto w-[80%] py-10">

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Edit Application #{{ $application->id }}</h1>
                <a href="{{ route('applications.show', $application) }}" class="text-sm text-slate-600 hover:underline">← Back to Detail</a>

            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-slate-100 p-6 rounded-lg mb-8 border border-slate-200">
                <h2 class="text-lg font-bold mb-4 border-b pb-2 text-slate-700">Application Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <span class="block text-xs uppercase text-slate-500 font-bold">Applicant</span>
                        <span class="font-semibold">{{ $application->user->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs uppercase text-slate-500 font-bold">Category</span>
                        <span class="font-semibold">{{ $application->applicationCategory?->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs uppercase text-slate-500 font-bold">Round</span>
                        <span class="font-semibold">{{ $application->applicationRound?->academic_year }} (Sem {{ $application->applicationRound?->semester }})</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('applications.update', $application) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold text-sm text-slate-700 mb-1">Decision Status</label>
                        <select name="status" class="w-full border @error('status') border-red-500 @else border-slate-300 @enderror rounded p-2 focus:ring-2 focus:ring-slate-900 outline-none">
                            @foreach(\App\Enums\ApplicationStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ old('status', $application->status->value) === $status->value ? 'selected' : '' }}>
                                    {{ ucfirst($status->value) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-sm text-slate-700 mb-1">Rejection Reason (Internal Note)</label>
                        <textarea name="rejection_reason" rows="2" class="w-full border @error('rejection_reason') border-red-500 @else border-slate-300 @enderror rounded p-2 focus:ring-2 focus:ring-slate-900 outline-none" placeholder="Explain why if rejected...">{{ old('rejection_reason', $application->rejection_reason) }}</textarea>
                        @error('rejection_reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <hr class="border-slate-200">

                <div>
                    <h3 class="text-lg font-bold mb-4 text-slate-800">Dynamic Form Answers</h3>
                    <div class="space-y-4">
                        @foreach($application->attributeValues as $answer)
                            <div class="p-3 bg-slate-50 rounded-md border border-slate-200">
                                <label class="block font-bold text-sm text-slate-700 mb-1">
                                    {{ $answer->attribute?->label ?? 'Custom Field' }}
                                </label>

                                @if($answer->attribute?->type === 'file')
                                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                                        <a href="{{ asset('storage/' . $answer->value) }}" target="_blank" class="inline-flex items-center text-blue-600 text-xs font-bold hover:underline border border-blue-200 px-3 py-1.5 rounded bg-blue-50">
                                            📄 Current Attachment
                                        </a>
                                        <div class="flex-grow">
                                            <input type="file" name="values[{{ $answer->id }}]" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-700">
                                            @error("values.$answer->id") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                @else
                                    <input type="text" name="values[{{ $answer->id }}]" value="{{ old('values.'.$answer->id, $answer->value) }}" class="w-full border @error("values.$answer->id") border-red-500 @else border-slate-300 @enderror rounded p-2 text-sm">
                                    @error("values.$answer->id") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr class="border-slate-200">

                <div>
                    <h3 class="text-lg font-bold mb-4 text-slate-800">General Attachments</h3>

                    @if($application->attachments->count() > 0)
                        <div class="mb-6 space-y-3">
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-bold">Existing Files (Check to remove)</p>
                            @foreach($application->attachments as $attachment)
                                <div class="flex items-center p-3 border border-slate-200 rounded-md hover:bg-red-50 transition-colors group">
                                    <input type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}" id="delete_file_{{ $attachment->id }}" class="w-4 h-4 text-red-600 border-slate-300 rounded focus:ring-red-500">

                                    <label for="delete_file_{{ $attachment->id }}" class="ml-3 flex-grow flex items-center justify-between cursor-pointer">
                                        <span class="text-sm font-medium text-slate-700">{{ $attachment->file_name }}</span>
                                        <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="text-xs text-blue-600 font-bold hover:text-blue-800 underline ml-4" onclick="event.stopPropagation();">
                                            Open File
                                        </a>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="bg-slate-50 p-4 rounded-md border border-dashed border-slate-300 @error('new_attachments.*') border-red-400 @enderror">
                        <label class="block font-bold text-sm text-slate-700 mb-2">Upload New Additional Files</label>
                        <input type="file" name="new_attachments[]" multiple class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                        @error('new_attachments.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center gap-4">
                    <button type="submit" class="bg-slate-900 text-white font-bold py-2.5 px-8 rounded hover:bg-slate-800 transition-all active:scale-95 shadow-lg shadow-slate-200">
                        Sync All Changes
                    </button>
                    <p class="text-xs text-slate-400">Updates will be logged in the system history.</p>
                </div>
            </form>
        </div>
    </section>
@endsection
