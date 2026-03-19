@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#f1f5f9] py-12">
        <div class="max-w-3xl mx-auto px-4">

            <div class="mb-6">
                <a href="/groups" class="text-sm text-gray-500 hover:text-blue-600 flex items-center gap-1 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Group Listing
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ isset($group) ? 'Edit Group: ' . $group->name : 'Create New Group' }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Groups act as categories to organize your AI tasks efficiently.
                    </p>
                </div>

                <form action="/groups" method="POST" class="p-8 space-y-8">
                    @csrf

                    @if(isset($group))
                        <input type="hidden" name="id" value="{{ $group->id }}">
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                        <label for="name" class="font-semibold text-gray-700">Group Name</label>
                        <div class="md:col-span-3">
                            <input type="text" name="name" id="name" required
                                   value="{{ old('name', $group->name ?? '') }}"
                                   placeholder="e.g., Content Writing"
                                   class="w-full border border-gray-300 rounded px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 items-start gap-4">
                        <label for="description" class="font-semibold text-gray-700 pt-2">Description</label>
                        <div class="md:col-span-3">
                        <textarea name="description" id="description" rows="3" required
                                  placeholder="Briefly describe the purpose of this group..."
                                  class="w-full border border-gray-300 rounded px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">{{ old('description', $group->description ?? '') }}</textarea>
                            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                        <label for="parent_id" class="font-semibold text-gray-700">Parent Group</label>
                        <div class="md:col-span-3">
                            <select name="parent_id" id="parent_id"
                                    class="w-full border border-gray-300 rounded px-4 py-2.5 bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                                <option value="">-- No Parent (Root Group) --</option>
                                @foreach($allGroups as $g)
                                    {{-- Logic: Prevent a group from being its own parent in Edit mode --}}
                                    @if(!isset($group) || $g->id != $group->id)
                                        <option value="{{ $g->id }}"
                                            {{ (old('parent_id', $group->parent_id ?? '') == $g->id) ? 'selected' : '' }}>
                                            {{ $g->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-400 mt-2 italic">Nesting groups allows for hierarchical task management.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="/groups" class="px-6 py-2 rounded text-gray-600 hover:bg-gray-100 transition font-medium">
                            Cancel
                        </a>
                        <button type="submit" class="bg-[#3b6db9] hover:bg-blue-700 text-white font-bold py-2 px-8 rounded shadow-sm transition duration-200">
                            {{ isset($group) ? 'Update Group' : 'Create Group' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
