@extends('dev.layout')

@section('title', 'Webhook Logs - DEV ONLY')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Header with DEV Warning -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    🔗 Webhook Logs 
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 ml-3">
                        ⚠️ DEV ONLY
                    </span>
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Monitor Tap Payments webhook notifications in real-time. This data is for development purposes only.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="location.reload()" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-lg bg-blue-500 flex items-center justify-center">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Webhooks</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-lg bg-yellow-500 flex items-center justify-center">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Last 24 Hours</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['recent_24h']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-lg bg-green-500 flex items-center justify-center">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Processed</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['processed']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-lg bg-red-500 flex items-center justify-center">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Failed</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['failed']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <form method="GET" action="{{ route('dev.payments.webhook-logs') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:gap-4">
            <div class="flex-1">
                <label for="object_type" class="block text-sm font-medium text-gray-700">Object Type</label>
                <select name="object_type" id="object_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Types</option>
                    <option value="charge" {{ request('object_type') === 'charge' ? 'selected' : '' }}>Charge</option>
                    <option value="authorize" {{ request('object_type') === 'authorize' ? 'selected' : '' }}>Authorize</option>
                    <option value="invoice" {{ request('object_type') === 'invoice' ? 'selected' : '' }}>Invoice</option>
                </select>
            </div>

            <div class="flex-1">
                <label for="processing_status" class="block text-sm font-medium text-gray-700">Processing Status</label>
                <select name="processing_status" id="processing_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="received" {{ request('processing_status') === 'received' ? 'selected' : '' }}>Received</option>
                    <option value="validated" {{ request('processing_status') === 'validated' ? 'selected' : '' }}>Validated</option>
                    <option value="processed" {{ request('processing_status') === 'processed' ? 'selected' : '' }}>Processed</option>
                    <option value="failed" {{ request('processing_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div class="flex-1">
                <label for="hours" class="block text-sm font-medium text-gray-700">Time Range</label>
                <select name="hours" id="hours" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Time</option>
                    <option value="1" {{ request('hours') === '1' ? 'selected' : '' }}>Last Hour</option>
                    <option value="24" {{ request('hours') === '24' ? 'selected' : '' }}>Last 24 Hours</option>
                    <option value="168" {{ request('hours') === '168' ? 'selected' : '' }}>Last Week</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <svg class="-ml-1 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter
                </button>
                @if(request()->hasAny(['object_type', 'processing_status', 'hours']))
                <a href="{{ route('dev.payments.webhook-logs') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Webhook Logs Table -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Webhook Logs</h3>
            <p class="mt-1 text-sm text-gray-500">
                Showing {{ $webhookLogs->firstItem() ?? 0 }} to {{ $webhookLogs->lastItem() ?? 0 }} of {{ $webhookLogs->total() }} results
            </p>
        </div>

        @if($webhookLogs->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Webhook Details
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Object Info
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Security
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Received
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($webhookLogs as $log)
                    <tr class="hover:bg-gray-50" x-data="{ expanded: false }">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $log->webhook_id ?: 'N/A' }}</div>
                            <div class="text-sm text-gray-500">ID: {{ $log->id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ ucfirst($log->object_type) }}</div>
                            <div class="text-sm text-gray-500 font-mono">{{ $log->object_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $log->formatted_amount }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $log->event_status_color }}">
                                {{ $log->event_status }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $log->status_color }} ml-1">
                                {{ ucfirst($log->processing_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->hash_valid)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Valid
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Invalid
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $log->created_at->format('M j, Y') }}</div>
                            <div>{{ $log->created_at->format('g:i A') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="expanded = !expanded" class="text-primary-600 hover:text-primary-900 transition-colors">
                                <span x-show="!expanded">View</span>
                                <span x-show="expanded" x-cloak>Hide</span>
                            </button>
                        </td>
                    </tr>
                    <tr x-show="expanded" x-collapse x-cloak>
                        <td colspan="7" class="px-6 py-4 bg-gray-50">
                            <div class="space-y-4">
                                <!-- Processing Notes -->
                                @if($log->processing_notes)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Processing Notes</h4>
                                    <p class="text-sm text-gray-700 bg-white p-3 rounded border">{{ $log->processing_notes }}</p>
                                </div>
                                @endif

                                <!-- Security Details -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Security Validation</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500">Received Hash</label>
                                            <code class="block text-xs bg-white p-2 rounded border font-mono break-all">{{ $log->received_hashstring ?: 'N/A' }}</code>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500">Calculated Hash</label>
                                            <code class="block text-xs bg-white p-2 rounded border font-mono break-all">{{ $log->calculated_hashstring ?: 'N/A' }}</code>
                                        </div>
                                    </div>
                                </div>

                                <!-- Raw Payload -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Raw Webhook Payload</h4>
                                    <pre class="text-xs bg-white p-3 rounded border overflow-auto max-h-64">{{ json_encode($log->webhook_payload, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $webhookLogs->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No webhook logs yet</h3>
            <p class="mt-1 text-sm text-gray-500">
                Create some test charges to start receiving webhooks.
            </p>
            <div class="mt-6">
                <a href="{{ route('dev.payments.charges') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Test Charge
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Development Warning -->
    <div class="mt-8 bg-red-50 border border-red-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">⚠️ Development Environment Only</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>This webhook logging system is for development and testing purposes only</li>
                        <li>Do not use the DevWebhookLog model or dev_webhook_logs table in production</li>
                        <li>All webhook data shown here is from test transactions</li>
                        <li>Implement proper webhook handling in your production controllers</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-500: #3b82f6;
        --primary-600: #2563eb;
        --primary-700: #1d4ed8;
    }
    
    .focus\:ring-primary-500:focus { --tw-ring-color: var(--primary-500); }
    .focus\:border-primary-500:focus { --tw-border-opacity: 1; border-color: var(--primary-500); }
    .bg-primary-600 { background-color: var(--primary-600); }
    .hover\:bg-primary-700:hover { background-color: var(--primary-700); }
    .text-primary-600 { color: var(--primary-600); }
    .hover\:text-primary-900:hover { color: #1e3a8a; }
    
    [x-cloak] { display: none !important; }
</style>
@endsection 