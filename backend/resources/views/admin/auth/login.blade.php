@extends('admin.layouts.guest')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <div class="mx-auto w-auto flex justify-center">
            <img class="h-12 w-auto" src="{{ asset('images/logo.svg') }}" alt="Your Logo">
        </div>
        
        <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
            Admin Login
        </h2>

        @if ($errors->any())
            <div class="mt-4 bg-red-50 text-red-500 p-4 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="-space-y-px rounded-md shadow-sm">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="relative block w-full rounded-t-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                           placeholder="Email address">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required
                           class="relative block w-full rounded-b-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                           placeholder="Password">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Sign in
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 