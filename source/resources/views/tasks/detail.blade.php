@extends('layouts.app')
@section('content')
    <div class="max-w-5xl mx-auto p-6 bg-gray-50">
        @php
            $statusClasses = [
                'completed' => 'bg-green-600 text-white',
                'progress' => 'bg-orange-500 text-white',
                'pending' => 'bg-gray-400 text-white',
                'failed' => 'bg-red-500 text-white',
            ];
            $badgeClass = $statusClasses[$taskStatus] ?? 'bg-gray-200 text-gray-700';
        @endphp
        <a href="{{ url('/groups/'.$task->group_id) }}" class="text-blue-500 mb-4 inline-block hover:underline">← Back to Group Tasks</a>
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex justify-between items-center border-b pb-4">
                <h1 class="text-2xl font-bold">{{ $task->name }}</h1>
                <span class="px-3 py-1 rounded-full text-sm font-semibold border {{ $badgeClass }}">
                                    {{ $taskStatus }}
                </span>
            </div>

            <div class="grid grid-cols-3 gap-4 py-4 text-sm text-gray-500 border-b">
                <div>Created: {{ $task->created_at->diffForHumans() }}</div>
                <div>Group: {{ $task->group?->name ?? 'N/A' }}</div>
                <div>Status: <span class="font-bold  {{ $badgeClass }}">{{ strtoupper($taskStatus) }}</span></div>
                @if($task->response_content)
                    <div>
                        <form action="{{ route('task.deploy', $task->id) }}" method="POST"
                              onsubmit="return confirm('Do you want to delpoy?');">
                            @csrf
                            <button type="submit" class="px-3 py-1 rounded-full text-sm font-semibold border bg-red-600 text-white">
                                Deploy
                            </button>
                        </form>
                    </div>
                @endif
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
                            <button onclick="copyToClipboard('result-{{ $task->id }}', this)"
                                    class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded transition">
                                Copy
                            </button>
                            <button onclick="toggleResult('result-{{ $task->id }}', this)" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                Show Result
                            </button>
                            <a href="{{ url('/tasks/'.$task->id.'/download') }}" title="Download content" class="text-lg">💾</a>
                        </div>
                    </div>
                    <div id="result-{{ $task->id }}" class="hidden">
                        <pre class="bg-gray-900 text-green-400 p-4 rounded overflow-x-auto"><code
                                contenteditable="true"
                                class="editable-code outline-none focus:ring-2 focus:ring-green-500 rounded block min-h-[1.5rem]"
                                data-task-id="{{ $task->id }}"
                            >{{ $task->response_content }}</code></pre>
                                        </div>
                </div>
            @endif

            @foreach($task->children as $child)
                <div class="mt-10 pt-6 border-t border-dashed">
                    <h3 class="font-bold mb-2 text-gray-800">Reply Prompt</h3>
                    <form action="{{ route('tasks.destroy', $child->id) }}" method="POST"
                          onsubmit="return confirm('Delete this specific reply?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition">
                            Delete Reply
                        </button>
                    </form>
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
                                <button onclick="copyToClipboard('result-{{ $task->id }}', this)"
                                        class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded transition">
                                    Copy
                                </button>
                                <button onclick="toggleResult('result-{{ $child->id }}', this)" class="text-xs text-blue-600 font-medium">Show Result</button>
                            </div>
                            <div id="result-{{ $child->id }}" class="hidden">
                                <pre class="bg-gray-900 text-green-400 p-4 rounded overflow-x-auto"><code
                                        contenteditable="true"
                                        class="editable-code outline-none focus:ring-2 focus:ring-green-500 rounded block min-h-[1.5rem]"
                                        data-task-id="{{ $child->id }}"
                                    >{{ $child->response_content }}</code></pre>

                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            <form action="{{ url('/tasks/child') }}" method="POST" enctype="multipart/form-data" class="mt-12 p-6 bg-blue-50 rounded-xl border border-blue-200">
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
        document.addEventListener('DOMContentLoaded', function() {
            const editableElements = document.querySelectorAll('.editable-code');

            editableElements.forEach(element => {
                // Store the original text so we don't send useless requests if nothing changed
                let originalText = element.innerText;

                element.addEventListener('blur', function() {
                    const currentText = this.innerText;
                    const taskId = this.dataset.taskId;

                    // Only save if the text actually changed
                    if (currentText !== originalText) {
                        saveContent(taskId, currentText, this);
                        originalText = currentText; // Update the reference
                    }
                });
            });

            function saveContent(taskId, content, element) {
                // Fetch the CSRF token from the meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                    || document.querySelector('input[name="_token"]')?.value;

                if (!csrfToken) {
                    console.error('CSRF token missing!');
                }

                // Visual feedback (optional): dim text while saving
                element.classList.add('opacity-50');

                fetch(`{{ url('') }}/tasks/${taskId}/update-content`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        response_content: content
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        element.classList.remove('opacity-50');
                        if (!data.success) {
                            alert('Failed to save changes.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        element.classList.remove('opacity-50');
                        alert('An error occurred while saving.');
                    });
            }
        });
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
        function copyToClipboard(elementId, btn) {
            const text = document.getElementById(elementId).innerText;

            // 1. Try the modern Clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text)
                    .then(() => showSuccess(btn))
                    .catch(err => console.error('Clipboard API failed', err));
            } else {
                // 2. Fallback: The "Hidden Textarea" Method
                const textArea = document.createElement("textarea");
                textArea.value = text;

                // Ensure the textarea isn't visible
                textArea.style.position = "fixed";
                textArea.style.left = "-9999px";
                textArea.style.top = "0";
                document.body.appendChild(textArea);

                textArea.focus();
                textArea.select();

                try {
                    const successful = document.execCommand('copy');
                    if (successful) {
                        showSuccess(btn);
                    }
                } catch (err) {
                    console.error('Fallback copy failed', err);
                }

                document.body.removeChild(textArea);
            }
        }

        // Separate function for the button visual feedback
        function showSuccess(btn) {
            const originalText = btn.innerText;
            btn.innerText = 'Copied!';
            btn.classList.replace('bg-gray-200', 'bg-green-500');
            btn.classList.add('text-white');

            setTimeout(() => {
                btn.innerText = originalText;
                btn.classList.replace('bg-green-500', 'bg-gray-200');
                btn.classList.remove('text-white');
            }, 2000);
        }
    </script>
@endsection
