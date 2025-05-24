@extends('dev.layout')

@section('title', 'Test Cards')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Test Cards</h1>
        <p class="mt-2 text-sm text-gray-600">
            Use these test cards for payment testing. These cards are safe and will not result in actual charges.
        </p>
    </div>

    <!-- Test Cards Grid -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($testCards as $card)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow" x-data="{ copied: '' }">
                <!-- Card Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg {{ $card['color'] }} flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $card['name'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $card['brand'] }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $card['color'] }} text-white">
                        {{ str_contains($card['scenario'], 'Success') ? 'Success' : (str_contains($card['scenario'], '3D') ? '3D Secure' : 'Decline') }}
                    </span>
                </div>

                <!-- Card Details -->
                <div class="space-y-3">
                    <!-- Card Number -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Number:</span>
                        <div class="flex items-center gap-2">
                            <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ chunk_split($card['number'], 4, ' ') }}</code>
                            <button 
                                @click="navigator.clipboard.writeText('{{ $card['number'] }}'); copied = 'number'; setTimeout(() => copied = '', 2000)"
                                class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
                                :class="{ 'text-green-600': copied === 'number' }"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="copied !== 'number'">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="copied === 'number'" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Expiry -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Expiry:</span>
                        <div class="flex items-center gap-2">
                            <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $card['exp_month'] }}/{{ $card['exp_year'] }}</code>
                            <button 
                                @click="navigator.clipboard.writeText('{{ $card['exp_month'] }}'); copied = 'month'; setTimeout(() => copied = '', 2000)"
                                class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
                                :class="{ 'text-green-600': copied === 'month' }"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="copied !== 'month'">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="copied === 'month'" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- CVC -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">CVC:</span>
                        <div class="flex items-center gap-2">
                            <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $card['cvc'] }}</code>
                            <button 
                                @click="navigator.clipboard.writeText('{{ $card['cvc'] }}'); copied = 'cvc'; setTimeout(() => copied = '', 2000)"
                                class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
                                :class="{ 'text-green-600': copied === 'cvc' }"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="copied !== 'cvc'">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="copied === 'cvc'" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Scenario -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Expected Result:</span><br>
                        {{ $card['scenario'] }}
                    </p>
                </div>

                <!-- Quick Actions -->
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('dev.payments.tokens') }}?card={{ urlencode($card['number']) }}" 
                       class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Test Token
                    </a>
                    <button 
                        @click="navigator.clipboard.writeText('{{ $card['number'] }}{{ $card['exp_month'] }}{{ $card['exp_year'] }}{{ $card['cvc'] }}'); copied = 'all'; setTimeout(() => copied = '', 3000)"
                        class="px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                        :class="{ 'border-green-500 text-green-700 bg-green-50': copied === 'all' }"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="copied !== 'all'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="copied === 'all'" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Usage Instructions -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">How to Use Test Cards</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Click the copy button next to any field to copy it to your clipboard</li>
                        <li>Use "Test Token" to quickly navigate to token creation with this card</li>
                        <li>Green cards simulate successful payments</li>
                        <li>Red cards simulate various decline scenarios</li>
                        <li>Yellow cards require additional authentication (3D Secure)</li>
                        <li>These are safe test cards - no real money will be charged</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection 