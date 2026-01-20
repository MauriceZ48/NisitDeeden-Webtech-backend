@extends('layouts.main')

@section('content')
    <div>
        <h1>{{ $application->id }}</h1>
        <h2>{{ $application->category->value }}</h2>
        <h2>{{ $application->user->name }}</h2>
        <h2>Create at: {{ $application->timestamps }}</h2>

        <h3>Attachments:</h3>
        <ul>
            @foreach($application->attachments as $file)
                <li>
                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">
                        {{ $file->file_name }} ({{ round($file->file_size / 1024, 2) }} KB)
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="flex flex-col">
        <a class="text-cyan-500" href=" {{ route('applications.edit', ['application' => $application]) }}">
            Edit Application
        </a>

        <form onsubmit="return confirm('Are you sure?')"
        action="{{ route('applications.destroy', ['application' => $application]) }}" method='POST'>
            @csrf
            @method('DELETE')
            <button class="text-red-500" type="submit">Delete Application</button>
        </form>
    </div>

@endsection
