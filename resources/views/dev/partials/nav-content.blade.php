<!-- Dev Tools Logo/Header -->
<div class="flex h-16 shrink-0 items-center border-b border-gray-200 pb-4">
    <div class="flex items-center gap-3">
        <div class="h-8 w-8 rounded-lg bg-primary-600 flex items-center justify-center">
            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Dev Tools</h2>
            <p class="text-xs text-gray-500">Development Environment</p>
        </div>
    </div>
</div>

<!-- Navigation -->
<nav class="flex flex-1 flex-col pt-4">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dev.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dev.index') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:text-primary-700 hover:bg-gray-50' }} transition-colors">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dev.index') ? 'text-primary-700' : 'text-gray-400 group-hover:text-primary-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        Dashboard
                        @if(request()->routeIs('dev.index'))
                            <div class="ml-auto h-1.5 w-1.5 rounded-full bg-primary-600"></div>
                        @endif
                    </a>
                </li>
                
                <!-- API Testing -->
                <li>
                    <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-primary-700 hover:bg-gray-50 transition-colors">
                        <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                        </svg>
                        API Testing
                        <span class="ml-auto inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Soon</span>
                    </a>
                </li>

                <!-- Database Explorer -->
                <li>
                    <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-primary-700 hover:bg-gray-50 transition-colors">
                        <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        </svg>
                        Database
                        <span class="ml-auto inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Soon</span>
                    </a>
                </li>

                <!-- Log Viewer -->
                <li>
                    <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-primary-700 hover:bg-gray-50 transition-colors">
                        <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Logs
                        <span class="ml-auto inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Soon</span>
                    </a>
                </li>

                <!-- Cache Manager -->
                <li>
                    <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-primary-700 hover:bg-gray-50 transition-colors">
                        <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Cache
                        <span class="ml-auto inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Soon</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Payments Section -->
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide">Payments</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <!-- Test Cards -->
                <li>
                    <a href="{{ route('dev.payments.test-cards') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dev.payments.test-cards') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:text-primary-700 hover:bg-gray-50' }} transition-colors">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dev.payments.test-cards') ? 'text-primary-700' : 'text-gray-400 group-hover:text-primary-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Test Cards
                        @if(request()->routeIs('dev.payments.test-cards'))
                            <div class="ml-auto h-1.5 w-1.5 rounded-full bg-primary-600"></div>
                        @endif
                    </a>
                </li>

                <!-- Token Testing -->
                <li>
                    <a href="{{ route('dev.payments.tokens') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dev.payments.tokens') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:text-primary-700 hover:bg-gray-50' }} transition-colors">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dev.payments.tokens') ? 'text-primary-700' : 'text-gray-400 group-hover:text-primary-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Token Testing
                        @if(request()->routeIs('dev.payments.tokens'))
                            <div class="ml-auto h-1.5 w-1.5 rounded-full bg-primary-600"></div>
                        @endif
                    </a>
                </li>

                <!-- Charge Testing -->
                <li>
                    <a href="{{ route('dev.payments.charges') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dev.payments.charges') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:text-primary-700 hover:bg-gray-50' }} transition-colors">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dev.payments.charges') ? 'text-primary-700' : 'text-gray-400 group-hover:text-primary-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Charge Testing
                        @if(request()->routeIs('dev.payments.charges'))
                            <div class="ml-auto h-1.5 w-1.5 rounded-full bg-primary-600"></div>
                        @endif
                    </a>
                </li>

                <!-- Webhook Logs (DEV) -->
                <li>
                    <a href="{{ route('dev.payments.webhook-logs') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dev.payments.webhook-logs') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:text-primary-700 hover:bg-gray-50' }} transition-colors">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dev.payments.webhook-logs') ? 'text-primary-700' : 'text-gray-400 group-hover:text-primary-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Webhook Logs
                        <span class="ml-auto inline-flex items-center rounded-full bg-red-100 text-red-700 px-1.5 py-0.5 text-xs font-medium">DEV</span>
                        @if(request()->routeIs('dev.payments.webhook-logs'))
                            <div class="ml-auto h-1.5 w-1.5 rounded-full bg-primary-600"></div>
                        @endif
                    </a>
                </li>
            </ul>
        </li>

        <!-- Development Tools Section -->
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide">Development</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <!-- Playground -->
                <li>
                    <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-primary-700 hover:bg-gray-50 transition-colors">
                        <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Playground
                        <span class="ml-auto inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Soon</span>
                    </a>
                </li>

                <!-- Component Previews -->
                <li>
                    <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-primary-700 hover:bg-gray-50 transition-colors">
                        <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        Components
                        <span class="ml-auto inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Soon</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- System Section -->
        <li class="mt-auto">
            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide">System</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <!-- System Info -->
                <li>
                    <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-primary-700 hover:bg-gray-50 transition-colors">
                        <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        System Info
                        <span class="ml-auto inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Soon</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>

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
    
    .bg-primary-50 { background-color: var(--primary-50); }
    .bg-primary-600 { background-color: var(--primary-600); }
    .text-primary-600 { color: var(--primary-600); }
    .text-primary-700 { color: var(--primary-700); }
    .hover\:text-primary-700:hover { color: var(--primary-700); }
    .group-hover\:text-primary-700:hover { color: var(--primary-700); }
</style> 