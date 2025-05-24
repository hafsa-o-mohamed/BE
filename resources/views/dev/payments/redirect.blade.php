@extends('dev.layout')

@section('title', 'Payment Result')

@section('content')
<div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
    <div class="text-center">
        <!-- Payment Status Icon -->
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-6
            {{ $status === 'completed' ? 'bg-green-100' : 'bg-red-100' }}">
            @if($status === 'completed')
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            @else
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            @endif
        </div>

        <!-- Status Message -->
        <h1 class="text-2xl font-bold mb-2
            {{ $status === 'completed' ? 'text-green-900' : 'text-red-900' }}">
            @if($status === 'completed')
                Payment Successful!
            @else
                Payment {{ ucfirst($status) }}
            @endif
        </h1>

        <p class="text-gray-600 mb-8">
            @if($status === 'completed')
                Your payment has been processed successfully. You will receive a confirmation email shortly.
            @elseif($status === 'failed')
                Your payment could not be processed. Please try again or contact support.
            @else
                Payment status: {{ ucfirst($status) }}
            @endif
        </p>

        <!-- Charge Details -->
        @if($chargeId)
        <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Charge ID:</dt>
                    <dd class="text-sm text-gray-900 font-mono">{{ $chargeId }}</dd>
                </div>
                @foreach($params as $key => $value)
                    @if($key !== 'status' && $key !== 'tap_id' && !empty($value))
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}:</dt>
                        <dd class="text-sm text-gray-900">{{ $value }}</dd>
                    </div>
                    @endif
                @endforeach
            </dl>
        </div>
        @endif

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('dev.payments.charges') }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Another Charge
            </a>
            
            <a href="{{ route('dev.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>

        <!-- Development Info -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3 text-left">
                    <h3 class="text-sm font-medium text-blue-800">Development Mode</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>This is the redirect page that users see after payment completion. In production, customize this page with your branding and business logic.</p>
                        @if(!empty($params))
                        <details class="mt-2">
                            <summary class="cursor-pointer font-medium">View all redirect parameters</summary>
                            <pre class="mt-2 text-xs bg-blue-100 p-2 rounded overflow-auto">{{ json_encode($params, JSON_PRETTY_PRINT) }}</pre>
                        </details>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-600: #2563eb;
        --primary-700: #1d4ed8;
    }
    
    .bg-primary-600 { background-color: var(--primary-600); }
    .hover\:bg-primary-700:hover { background-color: var(--primary-700); }
</style>
@endsection 