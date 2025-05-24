@extends('dev.layout')

@section('title', 'Developer Dashboard')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Server Time Card -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-lg bg-primary-600 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Server Time</dt>
                        <dd class="text-lg font-medium text-gray-900" x-data="{ time: '{{ $serverTime }}' }" x-init="
                            setInterval(() => {
                                time = new Date().toLocaleString('en-US', {
                                    year: 'numeric',
                                    month: '2-digit',
                                    day: '2-digit',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit',
                                    timeZoneName: 'short'
                                });
                            }, 1000)
                        " x-text="time"></dd>
                    </dl>
                </div>
                <div class="ml-5 flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Live
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Development Info Grid -->
    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Environment Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-md bg-blue-500 flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">Environment</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ app()->environment() }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laravel Version Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-md bg-red-500 flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">Laravel Version</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ app()->version() }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- PHP Version Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-md bg-purple-500 flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">PHP Version</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ phpversion() }}</dd>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="mt-8 bg-gradient-to-r from-gray-900 to-gray-800 rounded-lg shadow-lg border border-gray-700">
        <div class="px-6 py-8 sm:px-8">
            <div class="text-center">
                <h3 class="text-2xl font-bold text-white">🎉 Welcome to Your Dev Environment!</h3>
                <p class="mt-2 text-gray-300 max-w-2xl mx-auto">
                    You now have a dedicated development area where you can test features, debug issues, and experiment freely without affecting the main application.
                </p>
                <div class="mt-6">
                    <div class="inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700 transition-colors">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ready to build!
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Development Tips</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Add new routes to <code class="bg-blue-100 px-1 rounded">routes/dev.php</code></li>
                        <li>Create views in <code class="bg-blue-100 px-1 rounded">resources/views/dev/</code></li>
                        <li>Use the <code class="bg-blue-100 px-1 rounded">dev.auth</code> middleware for protected routes</li>
                        <li>Password: <code class="bg-blue-100 px-1 rounded">SuperSecretPassword123!</code></li>
                    </ul>
                </div>
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
    
    .bg-primary-500 { background-color: var(--primary-500); }
    .bg-primary-600 { background-color: var(--primary-600); }
    .text-primary-100 { color: var(--primary-100); }
    .text-primary-600 { color: var(--primary-600); }
    .from-primary-500 { --tw-gradient-from: var(--primary-500); }
    .to-primary-600 { --tw-gradient-to: var(--primary-600); }
</style>
@endsection 