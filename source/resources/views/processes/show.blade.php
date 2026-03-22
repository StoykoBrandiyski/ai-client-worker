@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto py-10 px-4">

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <a href="/processes" class="text-sm text-blue-600 hover:underline flex items-center gap-1 mb-2">
                    &larr; Back to Process List
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $process->name }}</h1>
                <div class="flex items-center gap-3 mt-2">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $process->status === 'Active' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                    {{ $process->status }}
                </span>
                    <span class="text-sm {{ $process->is_enabled ? 'text-green-600' : 'text-red-500' }} font-semibold">
                    {{ $process->is_enabled ? '● Scheduled & Enabled' : '○ Schedule Disabled' }}
                </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="/processes/edit/{{ $process->id }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-5 py-2 rounded-lg font-semibold transition shadow-sm">
                    Edit Configuration
                </a>
                <form action="/processes" method="POST" onsubmit="return confirm('Are you sure? This will delete the process and its model sequences.');">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $process->id }}">
                    <button class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-5 py-2 rounded-lg font-semibold transition border border-red-200">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Selection Logic (Condition)
                    </h3>
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-5">
                        <div class="text-sm text-blue-800 font-bold mb-1 uppercase tracking-tight">{{ $process->condition->name }}</div>
                        <div class="flex items-center gap-2 font-mono text-gray-700 bg-white px-3 py-2 rounded border border-blue-200 inline-block mt-2">
                            <span class="text-purple-600">{{ $process->condition->entity_type }}</span>
                            <span class="text-gray-400">WHERE</span>
                            <span class="font-bold">{{ $process->condition->field_key }}</span>
                            <span class="text-blue-600 font-bold">{{ $process->condition->operator }}</span>
                            <span class="text-green-600">'{{ $process->condition->value }}'</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Failover Strategy (Priority Sequence)
                    </h3>
                    <div class="space-y-3">
                        @forelse($process->models as $pm)
                            <div class="flex items-center gap-4 p-4 border rounded-lg {{ $loop->first ? 'bg-blue-50 border-blue-200 ring-1 ring-blue-100' : 'bg-gray-50 border-gray-200' }}">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold {{ $loop->first ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                                    {{ $pm->sort_order }}
                                </div>
                                <div class="flex-grow">
                                    <div class="font-bold text-gray-800">{{ $pm->engineModel->name ?? 'Unknown Model' }}</div>
                                    <div class="text-xs text-gray-500 font-mono">{{ $pm->model_id }}</div>
                                </div>
                                @if($loop->first)
                                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest bg-white px-2 py-1 rounded shadow-sm border border-blue-200">Primary</span>
                                @else
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Failover {{ $loop->index }}</span>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400 italic border-2 border-dashed rounded-lg">No models assigned to this process.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">

                <div class="bg-gray-900 text-white rounded-xl shadow-lg p-6">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Execution Schedule</h3>
                    <div class="text-2xl font-mono text-blue-400 mb-2">{{ $process->schedule }}</div>

                    <hr class="my-4 border-gray-800">

                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-xs">Job Timeout</span>
                            <span class="text-sm font-bold">{{ $process->timeout }}s</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-xs">Task Limit</span>
                            <span class="text-sm font-bold">{{ $process->limit_tasks }} tasks</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="space-y-3">
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">System ID</span>
                            <span class="text-sm font-mono text-gray-600"># {{ $process->id }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Created At</span>
                            <span class="text-sm text-gray-600">{{ $process->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Last Modified</span>
                            <span class="text-sm text-gray-600">{{ $process->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
