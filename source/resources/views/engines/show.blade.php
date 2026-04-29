@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto py-8">
        <a href="{{ url('/engines') }}" class="text-blue-500 mb-4 inline-block">&larr; Back to List</a>
        <div class="bg-white p-6 rounded shadow">
            <h1 class="text-2xl font-bold border-b pb-4 mb-4">Engine: {{ $engine->name }}</h1>

            <div class="grid grid-cols-2 gap-y-4 text-gray-700">
                <div class="font-semibold">ID:</div> <div>{{ $engine->id }}</div>
                <div class="font-semibold">Base URL:</div> <div>{{ $engine->base_url }}</div>
                <div class="font-semibold">Max Tasks Count:</div> <div>{{ $engine->max_tasks_count }}</div>
                <div class="font-semibold">Task Timeout:</div> <div>{{ $engine->task_timeout }} seconds</div>
                <div class="font-semibold">Created At:</div> <div>{{ $engine->created_at }}</div>
            </div>
        </div>
    </div>
@endsection
