@extends('layouts.main')

@section('content')
    <div>
        <h1>{{ $application->id }}</h1>
        <h2>{{ $application->category->value }}</h2>
        <h2>{{ $application->user->name }}</h2>
        <h2>Create at: {{ $application->timestamps }}</h2>
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
