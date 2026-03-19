@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto p-6 bg-gray-50">
    <a href="/groups" class="text-blue-500 mb-4 inline-block">← Back to Task List</a>

    <div class="bg-white rounded shadow-sm border p-6">
        <div class="flex justify-between items-center border-b pb-4">
            <h1 class="text-2xl font-bold">{{ $task->name }}</h1>
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">Completed</span>
        </div>

        <div class="grid grid-cols-3 gap-4 py-4 text-sm text-gray-500 border-b">
            <div>Created: {{ $task->created_at->diffForHumans() }}</div>
            <div>Last Run: Just now</div>
            <div>Status: <span class="text-green-600 font-bold">{{ $task->status }}</span></div>
        </div>

        @forelse($task->children as $child)

            <div class="mt-6">
                <h3 class="font-bold mb-2">Prompt</h3>
                <div class="bg-gray-50 p-4 border rounded text-gray-700">{{ $child->request_content }}</div>
            </div>

            <div class="mt-6 relative">
                <h3 class="font-bold mb-2">Result</h3>
                <div class="absolute right-2 top-8">
                    <a href="/tasks/{{ $child->id }}/download" title="Download content">💾</a>
                </div>
                <pre class="bg-gray-900 text-green-400 p-4 rounded overflow-x-auto"><code>{{ $child->response_content }}</code></pre>
            </div>
            @empty
                <div class="mt-6">
                    <h3 class="font-bold mb-2">Prompt</h3>
                    <div class="bg-gray-50 p-4 border rounded text-gray-700">{{ $task->request_content }}</div>
                </div>

                <div class="mt-6 relative">
                    <h3 class="font-bold mb-2">Result</h3>
                    <div class="absolute right-2 top-8">
                        <a href="/tasks/{{ $task->id }}/download" title="Download content">💾</a>
                    </div>
                    <pre class="bg-gray-900 text-green-400 p-4 rounded overflow-x-auto"><code>{{ $task->response_content }}</code></pre>
                </div>
            @endforelse

        <form action="/tasks/child" method="POST" enctype="multipart/form-data" class="mt-8">
            @csrf
            <input type="hidden" name="reply_to_task_id" value="{{ $task->id }}">
            <h3 class="font-bold mb-2">Submit New Prompt</h3>
            <textarea name="request_content" class="w-full border p-4 rounded" rows="3"></textarea>

            <div class="flex justify-between mt-4">
                <input type="file" name="images[]" multiple class="text-sm">
                <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection
