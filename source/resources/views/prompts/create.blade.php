@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg mt-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Create Prompt Template</h2>

    <form action="/prompts" method="POST" class="space-y-6">
        @csrf
        
        <div>
            <label class="block text-sm font-medium text-gray-700">Template Name</label>
            <input type="text" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Template Group</label>
                <select name="template_group_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Select Group --</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="bg-blue-50 p-4 rounded-md border border-blue-200">
                <label class="block text-sm font-medium text-blue-800">OR Create New Group</label>
                <input type="text" name="new_group_name" placeholder="New Group Name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm mb-2">
                <input type="text" name="new_group_description" placeholder="Description" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Prompt Content</label>
            <textarea name="content" rows="6" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm font-mono text-sm" placeholder="Enter your AI prompt instructions here..."></textarea>
        </div>

        <div class="flex justify-end space-x-3">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Cancel</button>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 shadow-sm">Save Template</button>
        </div>
    </form>
</div>
@endsection