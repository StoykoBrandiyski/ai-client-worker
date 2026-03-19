@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Create / Edit Engine</h1>

        <form action="/engines" method="POST" class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <div>
                <label class="block font-semibold">Engine Name</label>
                <input type="text" name="name" required class="w-full border rounded p-2" value="{{ old('name') }}">
            </div>

            <div>
                <label class="block font-semibold">Base URL</label>
                <input type="url" name="base_url" required class="w-full border rounded p-2" value="{{ old('base_url') }}">
            </div>

            <div>
                <label class="block font-semibold">Auth Token (Optional)</label>
                <input type="text" name="auth_token" class="w-full border rounded p-2" placeholder="Will be SHA-256 hashed">
                <p class="text-xs text-gray-500">Leave blank if editing and you don't want to change the token.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold">Max Tasks Count</label>
                    <input type="number" name="max_tasks_count" required class="w-full border rounded p-2" value="{{ old('max_tasks_count', 0) }}">
                </div>
                <div>
                    <label class="block font-semibold">Task Timeout (seconds)</label>
                    <input type="number" name="task_timeout" required class="w-full border rounded p-2" value="{{ old('task_timeout', 0) }}">
                </div>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded mt-4">Save Engine</button>
        </form>
    </div>
@endsection
