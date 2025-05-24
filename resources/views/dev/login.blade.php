@extends('dev.layout')

@section('title', 'Dev Login')

@section('content')
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <div class="mx-auto h-12 w-12 rounded-lg bg-primary-600 flex items-center justify-center">
            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
            Developer Access
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Enter the development password to access dev tools
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-sm">
        <div class="bg-white py-8 px-6 shadow rounded-lg sm:px-8">
            <form class="space-y-6" action="{{ route('dev.authenticate') }}" method="POST">
                @csrf
                
                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                        Development Password
                    </label>
                    <div class="mt-2">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            required 
                            autofocus
                            autocomplete="current-password"
                            class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('password') ring-red-500 focus:ring-red-500 @enderror"
                            placeholder="Enter development password"
                        >
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button 
                        type="submit"
                        class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors"
                    >
                        Access Dev Tools
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    This is a development environment. Access is restricted.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-50: #eff6ff;
        --primary-100: #dbeafe;
        --primary-200: #bfdbfe;
        --primary-300: #93c5fd;
        --primary-400: #60a5fa;
        --primary-500: #3b82f6;
        --primary-600: #2563eb;
        --primary-700: #1d4ed8;
        --primary-800: #1e40af;
        --primary-900: #1e3a8a;
    }
    
    .bg-primary-600 { background-color: var(--primary-600); }
    .hover\:bg-primary-500:hover { background-color: var(--primary-500); }
    .ring-primary-600 { --tw-ring-color: var(--primary-600); }
    .focus\:ring-primary-600:focus { --tw-ring-color: var(--primary-600); }
    .focus-visible\:outline-primary-600:focus-visible { outline-color: var(--primary-600); }
</style>
@endsection 