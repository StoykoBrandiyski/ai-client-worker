@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-100 py-10">
        <div class="max-w-4xl mx-auto px-4">

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($engine) ? 'Edit Engine: ' . $engine->name : 'Create New Engine' }}
                </h1>
                <a href="/engines" class="text-sm text-blue-600 hover:underline">&larr; Back to List</a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <form action="/engines" method="POST" class="p-8 space-y-6">
                    @csrf
                    @if(isset($engine))
                        <input type="hidden" name="id" value="{{ $engine->id }}">
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                        <label class="font-semibold text-gray-700">Engine Name</label>
                        <div class="md:col-span-3">
                            <input type="text" name="name" value="{{ old('name', $engine->name ?? '') }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                        <label class="font-semibold text-gray-700">Base URL</label>
                        <div class="md:col-span-3">
                            <input type="url" name="base_url" value="{{ old('base_url', $engine->base_url ?? '') }}" required
                                   placeholder="https://api.provider.com/v1"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 items-start gap-4">
                        <label class="font-semibold text-gray-700 pt-2">Auth Token</label>
                        <div class="md:col-span-3">
                            <input type="password" name="auth_token" placeholder="{{ isset($engine) ? '•••••••• (Leave blank to keep current)' : 'Enter API Key' }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                            <p class="text-xs text-gray-400 mt-1 italic">Note: Token is encrypted using SHA-256 before storage.</p>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-4">
                            <label class="font-semibold text-gray-700">Max Tasks</label>
                            <input type="number" name="max_tasks_count" value="{{ old('max_tasks_count', $engine->max_tasks_count ?? 0) }}"
                                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-4">
                            <label class="font-semibold text-gray-700">Timeout (sec)</label>
                            <input type="number" name="task_timeout" value="{{ old('task_timeout', $engine->task_timeout ?? 30) }}"
                                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t">
                        <button type="submit" class="bg-[#3b6db9] hover:bg-blue-700 text-white font-bold py-2 px-10 rounded shadow transition">
                            {{ isset($engine) ? 'Update Engine' : 'Create Engine' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
