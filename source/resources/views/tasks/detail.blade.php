@extends('layouts.app')
@section('content')
    <div class="max-w-5xl mx-auto p-6 bg-gray-50">
        <a href="/groups/{{ $task->group_id }}" class="text-blue-500 mb-4 inline-block hover:underline">← Back to Group Tasks</a>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex justify-between items-center border-b pb-4">
                <h1 class="text-2xl font-bold">{{ $task->name }}</h1>
                <span class="px-3 py-1 rounded-full text-sm font-semibold border {{ $task->status_color }}">
                {{ ucfirst($task->status) }}
            </span>
            </div>

            <div class="grid grid-cols-3 gap-4 py-4 text-sm text-gray-500 border-b">
                <div>Created: {{ $task->created_at->diffForHumans() }}</div>
                <div>Group: {{ $task->group?->name ?? 'N/A' }}</div>
                <div>Status: <span class="font-bold {{ $task->status === 'error' ? 'text-red-600' : 'text-green-600' }}">{{ strtoupper($task->status) }}</span></div>
            </div>

            <div class="mt-6">
                <h3 class="font-bold mb-2 text-gray-800">Prompt</h3>
                <div class="bg-gray-50 p-4 border rounded text-gray-700 whitespace-pre-wrap">{{ $task->request_content }}</div>

                @if($task->images->count() > 0)
                    <div class="mt-4 flex gap-2">
                        @foreach($task->images as $image)
                            <a href="{{ route('image.show', basename($image->path)) }}" target="_blank">
                                <img src="{{ route('image.show', basename($image->path)) }}" class="w-24 h-24 object-cover rounded border hover:opacity-75 transition">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($task->response_content)
                <div class="mt-6 relative">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-bold">Result</h3>
                        <div class="flex items-center gap-3">
                            <button onclick="toggleResult('result-{{ $task->id }}', this)" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                Hide Result
                            </button>
                            <a href="/tasks/{{ $task->id }}/download" title="Download content" class="text-lg">💾</a>
                        </div>
                    </div>
                    <div id="result-{{ $task->id }}">
                        <pre class="bg-gray-900 text-green-400 p-4 rounded overflow-x-auto"><code>{{ $task->response_content }}</code></pre>
                    </div>
                </div>
            @endif

            @foreach($task->children as $child)
                <div class="mt-10 pt-6 border-t border-dashed">
                    <h3 class="font-bold mb-2 text-gray-800">Reply Prompt</h3>
                    <div class="bg-blue-50 p-4 border border-blue-100 rounded text-gray-700 whitespace-pre-wrap">{{ $child->request_content }}</div>

                    @if($child->images->count() > 0)
                        <div class="mt-4 flex gap-2">
                            @foreach($child->images as $image)
                                <img src="{{ route('image.show', basename($image->path)) }}" class="w-20 h-20 object-cover rounded border">
                            @endforeach
                        </div>
                    @endif

                    @if($child->response_content)
                        <div class="mt-6 relative">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-bold">Result</h3>
                                <button onclick="toggleResult('result-{{ $child->id }}', this)" class="text-xs text-blue-600 font-medium">Hide Result</button>
                            </div>
                            <div id="result-{{ $child->id }}">
                                <pre class="bg-gray-900 text-green-400 p-4 rounded overflow-x-auto"><code>{{ $child->response_content }}</code></pre>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            <form action="/tasks/child" method="POST" enctype="multipart/form-data" class="mt-12 p-6 bg-blue-50 rounded-xl border border-blue-200">
                @csrf
                <input type="hidden" name="reply_to_task_id" value="{{ $task->id }}">
                <h3 class="font-bold mb-2 text-blue-800">Submit New Prompt</h3>
                <textarea name="request_content" class="w-full border-blue-200 p-4 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" rows="3" placeholder="Continue the conversation..."></textarea>

                <div class="flex justify-between items-center mt-4">
                    <input type="file" name="images[]" multiple class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-100 file:text-blue-700">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded-lg font-bold shadow-md transition">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleResult(id, btn) {
            const target = document.getElementById(id);
            if (target.classList.contains('hidden')) {
                target.classList.remove('hidden');
                btn.innerText = 'Hide Result';
            } else {
                target.classList.add('hidden');
                btn.innerText = 'Show Result';
            }
        }
    </script>
@endsection
