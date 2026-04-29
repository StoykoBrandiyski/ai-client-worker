@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-100 py-10">
        <div class="max-w-4xl mx-auto px-4">

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($model) ? 'Edit Model: ' . $model->name : 'Register New Engine Model' }}
                </h1>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <form action="{{ url('/engine/models') }}" method="POST" class="p-8 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                        <label class="font-semibold text-gray-700">Identifier (ID)</label>
                        <div class="md:col-span-3">
                            <input type="text" name="identifier" value="{{ old('identifier', $model->identifier ?? '') }}"
                                   {{ isset($model) ? 'readonly' : 'required' }}
                                   placeholder="The identifier will be append with engine name (e.t 2.5-flash -> gemini-2.5-flash)"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 {{ isset($model) ? 'bg-gray-50 text-gray-500' : 'focus:ring-2 focus:ring-blue-500' }} outline-none font-mono">
                            @if(isset($model))
                                <input type="hidden" name="id" value="{{ $model->identifier }}">
                                <p class="text-[10px] text-red-400 mt-1 uppercase font-bold">Primary identifiers cannot be changed after creation.</p>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                        <label class="font-semibold text-gray-700">Provider Engine</label>
                        <div class="md:col-span-3">
                            <select name="engine_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Select Engine --</option>
                                @foreach($engines as $engine)
                                    <option value="{{ $engine->id }}" {{ (old('engine_id', $model->engine_id ?? '') == $engine->id) ? 'selected' : '' }}>
                                        {{ $engine->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="font-semibold text-gray-700">Display Name</label>
                            <input type="text" name="name" value="{{ old('name', $model->name ?? '') }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="space-y-2">
                            <label class="font-semibold text-gray-700">Model URL (Optional)</label>
                            <input type="text" name="url" value="{{ old('url', $model->url ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="font-semibold text-gray-700">Initial System Prompt</label>
                        <textarea name="initial_prompt" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-blue-500">{{ old('initial_prompt', $model->initial_prompt ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                        <label class="font-semibold text-gray-700">Interface Type</label>
                        <div class="md:col-span-3 flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="use_chat" value="1" {{ old('use_chat', $model->use_chat ?? 0) == 1 ? 'checked' : '' }}>
                                <span class="text-sm">Chat Completion</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="use_chat" value="0" {{ old('use_chat', $model->use_chat ?? 0) == 0 ? 'checked' : '' }}>
                                <span class="text-sm">Standard Completion</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t">
                        <a href="{{ url('/engine/models') }}" class="px-6 py-2 text-gray-500 hover:bg-gray-50 rounded">Cancel</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-10 rounded shadow transition">
                            {{ isset($model) ? 'Save Changes' : 'Create Model' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
