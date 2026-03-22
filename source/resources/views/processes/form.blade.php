@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto py-10">
        <h1 class="text-2xl font-bold mb-6">{{ isset($process) ? 'Edit Process' : 'Create Process' }}</h1>

        <form action="/processes" method="POST" class="bg-white p-8 rounded shadow space-y-6">
            @csrf
            @if(isset($process)) <input type="hidden" name="id" value="{{ $process->id }}"> @endif

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold">Process Name</label>
                    <input type="text" name="name" required class="w-full border rounded p-2" value="{{ old('name', $process->name ?? '') }}">
                </div>
                <div>
                    <label class="block font-semibold">Status</label>
                    <select name="status" class="w-full border rounded p-2 bg-white">
                        <option value="Active" {{ old('status', $process->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Draft" {{ old('status', $process->status ?? '') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block font-semibold">Schedule (Cron)</label>
                    <input type="text" name="schedule" required placeholder="*/5 * * * *" class="w-full border rounded p-2 font-mono" value="{{ old('schedule', $process->schedule ?? '') }}">
                </div>
                <div>
                    <label class="block font-semibold">Timeout (sec)</label>
                    <input type="number" name="timeout" required class="w-full border rounded p-2" value="{{ old('timeout', $process->timeout ?? 60) }}">
                </div>
                <div>
                    <label class="block font-semibold">Task Limit</label>
                    <input type="number" name="limit_tasks" required class="w-full border rounded p-2" value="{{ old('limit_tasks', $process->limit_tasks ?? 10) }}">
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer mt-4">
                    <input type="hidden" name="is_enabled" value="0">
                    <input type="checkbox" name="is_enabled" value="1" {{ old('is_enabled', $process->is_enabled ?? 0) ? 'checked' : '' }} class="w-5 h-5">
                    <span class="font-semibold text-gray-700">Enable Process Schedule</span>
                </label>
            </div>

            <hr>

            <h2 class="text-xl font-bold">Process Condition</h2>

            <div x-data="{ conditionType: '{{ old('condition_id', $process->condition_id ?? '') ? 'existing' : 'new' }}' }">
                <div class="flex gap-4 mb-4">
                    <label class="flex items-center gap-2"><input type="radio" x-model="conditionType" value="existing" name="cond_toggle"> Select Existing Condition</label>
                    <label class="flex items-center gap-2"><input type="radio" x-model="conditionType" value="new" name="cond_toggle"> Create New Condition</label>
                </div>

                <div x-show="conditionType === 'existing'">
                    <select name="condition_id" class="w-full border rounded p-2 bg-white">
                        <option value="">-- Choose Condition --</option>
                        @foreach($conditions as $cond)
                            <option value="{{ $cond->id }}" {{ old('condition_id', $process->condition_id ?? '') == $cond->id ? 'selected' : '' }}>
                                {{ $cond->name }} ({{ $cond->field_key }} {{ $cond->operator }} {{ $cond->value }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="conditionType === 'new'" class="bg-gray-50 p-4 border rounded grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold">Condition Name</label>
                        <input type="text" name="new_condition_name" class="w-full border rounded p-2" placeholder="e.g. Find pending tasks">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Entity Type</label>
                        <input type="text" name="new_condition_entity_type" class="w-full border rounded p-2" placeholder="Task">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Field Key</label>
                        <input type="text" name="new_condition_field_key" class="w-full border rounded p-2" placeholder="status">
                    </div>
                    <div class="flex gap-2">
                        <div class="w-1/3">
                            <label class="block text-sm font-semibold">Operator</label>
                            <select name="new_condition_operator" class="w-full border rounded p-2">
                                <option>=</option><option>!=</option><option>></option><option><</option>
                            </select>
                        </div>
                        <div class="w-2/3">
                            <label class="block text-sm font-semibold">Value</label>
                            <input type="text" name="new_condition_value" class="w-full border rounded p-2" placeholder="pending">
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-8 border-t pt-8" x-data="{
    selectedModels: {{ isset($selectedModels) ? $selectedModels->toJson() : '[]' }},
    addModel() {
        let select = $refs.modelSelect;
        let id = select.value;
        let name = select.options[select.selectedIndex].text;
        if(id && !this.selectedModels.find(m => m.identifier === id)) {
            this.selectedModels.push({ identifier: id, name: name });
        }
    },
    removeModel(index) {
        this.selectedModels.splice(index, 1);
    },
    moveUp(index) {
        if(index > 0) {
            let item = this.selectedModels.splice(index, 1)[0];
            this.selectedModels.splice(index - 1, 0, item);
        }
    },
    moveDown(index) {
        if(index < this.selectedModels.length - 1) {
            let item = this.selectedModels.splice(index, 1)[0];
            this.selectedModels.splice(index + 1, 0, item);
        }
    }
}">
                <h2 class="text-xl font-bold mb-4">Model Priority Sequence</h2>
                <p class="text-sm text-gray-500 mb-4 italic">The process will attempt the first model. If it fails, it will automatically switch to the next one in the list.</p>

                <div class="flex gap-4 mb-6">
                    <select x-ref="modelSelect" class="flex-1 border rounded p-2 bg-white">
                        <option value="">-- Choose a Model to Add --</option>
                        @foreach($allModels as $em)
                            <option value="{{ $em->identifier }}">{{ $em->name }} ({{ $em->identifier }})</option>
                        @endforeach
                    </select>
                    <button type="button" @click="addModel()" class="bg-green-600 text-white px-4 py-2 rounded font-bold">+ Add to Sequence</button>
                </div>

                <div class="space-y-2">
                    <template x-for="(model, index) in selectedModels" :key="model.identifier">
                        <div class="flex items-center justify-between bg-gray-50 border p-3 rounded-lg shadow-sm">
                            <div class="flex items-center gap-4">
                                <span class="bg-blue-600 text-white w-6 h-6 flex items-center justify-center rounded-full text-xs font-bold" x-text="index + 1"></span>
                                <div>
                                    <div class="font-bold text-gray-800" x-text="model.name"></div>
                                    <div class="text-xs text-gray-500 font-mono" x-text="model.identifier"></div>
                                </div>
                            </div>

                            <input type="hidden" name="models[]" :value="model.identifier">

                            <div class="flex gap-2">
                                <button type="button" @click="moveUp(index)" class="p-1 hover:bg-gray-200 rounded text-gray-600">▲</button>
                                <button type="button" @click="moveDown(index)" class="p-1 hover:bg-gray-200 rounded text-gray-600">▼</button>
                                <button type="button" @click="removeModel(index)" class="p-1 hover:text-red-600 text-gray-400 ml-4">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="selectedModels.length === 0" class="text-center py-8 border-2 border-dashed rounded-lg text-gray-400">
                        No models assigned. Please add at least one model to run this process.
                    </div>
                </div>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded mt-4">Save Process</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
