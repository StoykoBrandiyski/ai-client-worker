@extends('layouts.app')

@section('content')

    <div class="min-h-[calc(100vh-70px)] flex items-center justify-center p-6">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl p-10 relative">
            
            <h2 class="text-3xl font-extrabold text-gray-800 mb-10">Register</h2>

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                    <label for="username" class="md:col-span-1 font-semibold text-gray-700">Username</label>
                    <div class="md:col-span-3">
                        <input type="text" name="username" id="username" placeholder="Username" required
                               class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                    <label for="email" class="md:col-span-1 font-semibold text-gray-700">Email</label>
                    <div class="md:col-span-3">
                        <input type="email" name="email" id="email" required
                               class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-4">
                    <label for="password" class="md:col-span-1 font-semibold text-gray-700">Password</label>
                    <div class="md:col-span-3">
                        <input type="password" name="password" id="password" placeholder="••••••••" required
                               class="w-full border border-gray-300 rounded px-4 py-2 tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="flex items-center justify-between mt-12 pt-4">
                    <p class="text-sm text-gray-500">
                        Already have an account? <a href="{{ route('login') }}" class="text-gray-600 font-medium hover:underline">Login here.</a>
                    </p>
                    <div class="space-x-2 flex">
                        <button type="submit" class="bg-[#3b6db9] hover:bg-blue-700 text-white font-medium py-2 px-6 rounded transition duration-200">
                            Create Account
                        </button>
                        <a href="{{ url('/') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-6 rounded transition duration-200">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection