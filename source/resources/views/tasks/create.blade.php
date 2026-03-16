@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-10">
    <div class="max-w-4xl mx-auto px-4">
        
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Create New Task</h1>
            <a href="/dashboard" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <form action="/tasks" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                    <label for="name" class="font-semibold text-gray-700">Task Name</label>
                    <div class="md:col-span-3">
                        <input type="text" name="name" id="name" required placeholder="e.g. Generate API Documentation"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                    <label for="executed_count" class="font-semibold text-gray-700">Execute Count</label>
                    <div class="md:col-span-3">
                        <input type="text" name="executed_count" id="executed_count" required placeholder="e.g. Generate API Documentation"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                    <label for="sort_order" class="font-semibold text-gray-700">Sort Order</label>
                    <div class="md:col-span-3">
                        <input type="number" name="sort_order" id="sort_order" required placeholder="e.g. Generate API Documentation"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-4">
                        <label for="group_id" class="font-semibold text-gray-700">Task Group</label>
                        <select name="group_id" id="group_id" required class="border border-gray-300 rounded-lg px-4 py-2 bg-white">
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-4">
                        <label for="status" class="font-semibold text-gray-700">Status</label>
                        <select name="status" id="status" required class="border border-gray-300 rounded-lg px-4 py-2 bg-white">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Failed">Failed</option>
                        </select>
                    </div>
                </div>

                <hr class="border-gray-100">

                <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                    <label for="template_select" class="font-semibold text-gray-700">Use Template</label>
                    <div class="md:col-span-3">
                        <select id="template_select" name="prompt_template_id" class="w-full border border-blue-200 bg-blue-50 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Choose a Prompt Template (Optional) --</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" data-content="{{ $template->content }}">
                                    {{ $template->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="request_content" class="font-semibold text-gray-700">Prompt Content</label>
                    <textarea name="request_content" id="request_content" rows="6" required
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 font-mono text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                              placeholder="Describe your request in detail..."></textarea>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-dashed border-gray-300">
                    <label class="block font-semibold text-gray-700 mb-2">Upload Images (Optional - Max 3)</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-2 text-xs text-gray-400 italic">Supported formats: JPG, PNG. Max file size: 2MB.</p>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                    <button type="reset" class="px-6 py-2 text-gray-600 hover:text-gray-800 font-medium">
                        Clear Form
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-10 rounded-lg shadow-md transition-all">
                        Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const templateSelect = document.getElementById('template_select');
        const contentTextarea = document.getElementById('request_content');

        templateSelect.addEventListener('change', function() {
            // Find the selected option
            const selectedOption = this.options[this.selectedIndex];
            
            // Get the content from the data attribute
            const content = selectedOption.getAttribute('data-content');

            if (content) {
                // Smooth transition of content
                contentTextarea.value = content;
                
                // Visual feedback
                contentTextarea.classList.add('ring-2', 'ring-blue-400');
                setTimeout(() => {
                    contentTextarea.classList.remove('ring-2', 'ring-blue-400');
                }, 1000);
            }
        });

        // Validation for Max 3 Images
        const imageInput = document.getElementById('images');
        imageInput.addEventListener('change', function() {
            if (this.files.length > 3) {
                alert("You can only upload a maximum of 3 images.");
                this.value = "";
            }
        });
    });
</script>
@endsection