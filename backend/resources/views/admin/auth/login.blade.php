@extends('admin.layouts.guest')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800">
    <!-- Theme Toggle -->
    <div class="fixed top-4 right-4">
        <button 
            type="button" 
            x-data 
            @click="$store.darkMode.toggle()"
            class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
        >
            <svg x-show="!$store.darkMode.on" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg x-show="$store.darkMode.on" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    </div>

    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 shadow-2xl rounded-2xl p-8 space-y-8">
            <!-- Logo -->
            <div class="flex justify-center">
                <img class="h-16 w-auto" src="{{ asset('images/logo.svg') }}" alt="Logo">
            </div>

            <!-- Title -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Welcome Back
                </h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Sign in to your admin account
                </p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/50 text-red-600 dark:text-red-400 p-4 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Email address
                        </label>
                        <div class="mt-1">
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                required 
                                class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary dark:focus:ring-primary-dark dark:focus:border-primary-dark transition-colors duration-200"
                                placeholder="Enter your email"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Password
                        </label>
                        <div class="mt-1">
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                required 
                                class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary dark:focus:ring-primary-dark dark:focus:border-primary-dark transition-colors duration-200"
                                placeholder="Enter your password"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-primary focus:ring-primary dark:focus:ring-primary-dark"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Remember me
                        </label>
                    </div>
                </div>

                <button 
                    type="submit"
                    class="w-full flex justify-center py-3 px-4 rounded-lg text-white bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-gray-900 transition-all duration-200"
                >
                    Sign in
                </button>
            </form>
        </div>
    </div>
</div>
@endsection 