<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Your App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
<header class="bg-white shadow-sm border-b p-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="/dashboard" class="text-xl font-bold text-blue-600">AI Worker</a>

        <nav class="flex items-center space-x-6">
            <a href="/groups" class="text-gray-600 hover:text-blue-600 font-medium">Groups</a>
            <a href="/processes" class="text-gray-600 hover:text-blue-600 font-medium">Process</a>

            <div class="relative group py-2"> <button class="flex items-center text-gray-600 group-hover:text-blue-600 font-medium focus:outline-none">
                    Task
                    <svg class="w-4 h-4 ml-1 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="absolute right-0 w-48 pt-2 hidden group-hover:block z-50">
                    <div class="bg-white border rounded-lg shadow-xl py-2">
                        <a href="/tasks/create" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">New Task</a>
                        <a href="/prompts/create" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Add Prompt Template</a>
                    </div>
                </div>
            </div>

            <div class="relative group py-2">
                <button class="flex items-center text-gray-600 group-hover:text-blue-600 font-medium focus:outline-none">
                    Engine
                    <svg class="w-4 h-4 ml-1 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="absolute right-0 w-48 pt-2 hidden group-hover:block z-50">
                    <div class="bg-white border rounded-lg shadow-xl py-2">
                        <a href="/engines" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Engine</a>
                        <a href="/engine/models" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Engine Models</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <main class="flex-grow flex items-center justify-center">
