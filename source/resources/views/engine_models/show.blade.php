@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto py-10 px-4">

        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="/engine/models" class="text-sm text-blue-600 hover:underline mb-2 inline-block">&larr; Back to Models List</a>
                <h1 class="text-3xl font-bold text-gray-800">{{ $model->name }}</h1>
                <p class="text-gray-500 font-mono mt-1 text-sm">ID: {{ $model->identifier }}</p>
            </div>

            <div class="flex gap-3">
                <a href="{{ url('/engine/models/edit/'. $model->identifier)}}" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2 rounded-lg shadow transition">
                    Edit
                </a>
                <form action="{{ route('engine.model.store') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this model? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $model->identifier }}">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg shadow transition">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-8">
                <h2 class="text-xl font-bold border-b border-gray-100 pb-4 mb-6 text-gray-800">Model Configuration</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-8">

                    <div>
                        <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Unique Identifier</span>
                        <span class="text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded text-sm">{{ $model->identifier }}</span>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Provider Engine</span>
                        <span class="text-gray-900 font-medium">
                        <a href="/engines/{{ $model->engine_id }}" class="text-blue-600 hover:underline flex items-center gap-1">
                            {{ $model->engine->name ?? 'Unknown Engine' }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </span>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Interface Type</span>
                        @if($model->use_chat)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                            Chat Completion
                        </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                            Standard Completion
                        </span>
                        @endif
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Endpoint URL Override</span>
                        @if($model->url)
                            <a href="{{ $model->url }}" target="_blank" class="text-blue-600 hover:underline break-all text-sm">{{ $model->url }}</a>
                        @else
                            <span class="text-gray-400 italic text-sm">Inherits from Engine Base URL</span>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Initial System Prompt</span>
                        @if($model->initial_prompt)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-gray-700 whitespace-pre-wrap font-mono text-sm leading-relaxed">
                                {{ $model->initial_prompt }}
                            </div>
                        @else
                            <div class="bg-gray-50 border border-dashed border-gray-300 rounded-lg p-4 text-center">
                                <span class="text-gray-400 italic text-sm">No initial prompt configured for this model.</span>
                            </div>
                        @endif
                    </div>

                    <div class="md:col-span-2 grid grid-cols-2 gap-4 pt-6 border-t border-gray-100 mt-2">
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Created At</span>
                            <span class="text-sm text-gray-600">{{ $model->created_at->format('M d, Y - H:i:s') }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Last Updated</span>
                            <span class="text-sm text-gray-600">{{ $model->updated_at->format('M d, Y - H:i:s') }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
