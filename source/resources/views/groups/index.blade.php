@extends('layouts.app')
@section('content')
<div class="p-6 bg-gray-100 min-h-screen">
    <div class="flex justify-between mb-4">
        <div class="flex items-center space-x-2">
            <span class="text-gray-600">Filter by</span>
            <button class="bg-white border px-4 py-1 rounded shadow-sm font-bold">Group</button>
            <select class="border px-4 py-1 rounded shadow-sm"><option>All Categories</option></select>
        </div>
        <div class="flex space-x-2">
            <input type="text" placeholder="Search tasks..." class="border rounded px-4 py-1">
            <a href="/storeGroup" class="bg-blue-600 text-white px-4 py-1 rounded">New Task +</a>
        </div>
    </div>

    @foreach($groups as $group)
    <div class="bg-white rounded-lg shadow-sm mb-4 border border-gray-200">
        <div class="p-4 flex justify-between items-center bg-gray-50 rounded-t-lg">
            <div class="flex items-center space-x-2">
                <span class="text-orange-400">📁</span>
                <span class="font-bold text-gray-700">{{ $group->name }}</span>
                <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full">{{ $group->tasks_count }}</span>
            </div>
            <button class="text-gray-400">▼</button>
        </div>

        <div class="p-2">
            @foreach($group->latestThreeTasks as $task)
            <div class="flex justify-between items-center p-3 border-b last:border-0 hover:bg-gray-50">
                <div class="flex items-center space-x-3">
                    <span class="{{ $task->status == 'Completed' ? 'text-green-500' : 'text-gray-400' }}">✔</span>
                    <span class="text-gray-700">{{ $task->name }}</span>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/tasks/{{ $task->id }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">View Details</a>
                    <form action="/tasks/{{ $task->id }}" method="POST" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-red-500">🗑</button>
                    </form>
                </div>
            </div>
            @endforeach
            <div class="p-3">
                <a href="/groups/{{ $group->id }}" class="text-gray-500 text-sm font-bold bg-gray-100 px-4 py-1 rounded hover:bg-gray-200">+ More</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
