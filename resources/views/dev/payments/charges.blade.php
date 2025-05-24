@extends('dev.layout')

@section('title', 'Charge Testing')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Charge Testing</h1>
        <p class="mt-2 text-sm text-gray-600">
            Test Tap Payments charge creation API. Create charges using tokens from the token testing page.
        </p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" x-data="chargeTesting()">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Create Charge</h2>
        
        <form @submit.prevent="createCharge()">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Payment Details -->
                <div class="space-y-4">
                    <h3 class="text-sm font-medium text-gray-900 border-b border-gray-200 pb-2">Payment Details</h3>
                    
                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                        <div class="mt-1 relative">
                            <input 
                                type="number" 
                                id="amount"
                                x-model="form.amount"
                                step="0.01"
                                min="0.1"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm pr-12"
                                placeholder="1.00"
                                required
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-gray-500 sm:text-sm" x-text="form.currency"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                        <select 
                            id="currency"
                            x-model="form.currency"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            required
                        >
                            <option value="KWD">KWD - Kuwaiti Dinar</option>
                            <option value="USD">USD - US Dollar</option>
                            <option value="EUR">EUR - Euro</option>
                            <option value="GBP">GBP - British Pound</option>
                            <option value="AED">AED - UAE Dirham</option>
                            <option value="SAR">SAR - Saudi Riyal</option>
                        </select>
                    </div>

                    <!-- Token ID -->
                    <div>
                        <label for="token_id" class="block text-sm font-medium text-gray-700">Token ID</label>
                        <input 
                            type="text" 
                            id="token_id"
                            x-model="form.token_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="tok_..."
                            required
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            Use a token generated from the <a href="{{ route('dev.payments.tokens') }}" class="text-primary-600 hover:text-primary-800">Token Testing</a> page
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                        <input 
                            type="text" 
                            id="description"
                            x-model="form.description"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="Test charge from dev environment"
                        >
                    </div>
                </div>

                <!-- Customer Details -->
                <div class="space-y-4">
                    <h3 class="text-sm font-medium text-gray-900 border-b border-gray-200 pb-2">Customer Details</h3>
                    
                    <!-- Customer Name -->
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700">Customer Name</label>
                        <input 
                            type="text" 
                            id="customer_name"
                            x-model="form.customer_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="John Doe"
                            required
                        >
                    </div>

                    <!-- Customer Email -->
                    <div>
                        <label for="customer_email" class="block text-sm font-medium text-gray-700">Customer Email</label>
                        <input 
                            type="email" 
                            id="customer_email"
                            x-model="form.customer_email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="john@example.com"
                            required
                        >
                    </div>

                    <!-- Quick Fill Buttons -->
                    <div class="pt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quick Fill</label>
                        <div class="space-y-2">
                            <button 
                                type="button"
                                @click="fillCustomer('Test User', 'test@example.com')"
                                class="w-full text-left px-3 py-2 border border-gray-200 rounded-md hover:bg-gray-50 text-sm transition-colors"
                            >
                                Test User (test@example.com)
                            </button>
                            <button 
                                type="button"
                                @click="fillCustomer('Demo Customer', 'demo@company.com')"
                                class="w-full text-left px-3 py-2 border border-gray-200 rounded-md hover:bg-gray-50 text-sm transition-colors"
                            >
                                Demo Customer (demo@company.com)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button 
                    type="submit"
                    :disabled="loading"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!loading">Create Charge</span>
                    <span x-show="loading">Creating Charge...</span>
                </button>
            </div>
        </form>

        <!-- Response Display -->
        <div x-show="response" class="mt-6 p-4 bg-gray-50 rounded-lg" x-transition>
            <h3 class="text-sm font-medium text-gray-900 mb-2">API Response</h3>
            <pre class="text-xs bg-white p-3 rounded border overflow-auto max-h-96" x-text="JSON.stringify(response, null, 2)"></pre>
        </div>

        <!-- Error Display -->
        <div x-show="error" class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg" x-transition>
            <h3 class="text-sm font-medium text-red-800 mb-2">Error</h3>
            <p class="text-sm text-red-700" x-text="error"></p>
        </div>

        <!-- Documentation -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Charge API Information</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Charges initiate payment requests using tokens</li>
                            <li>Minimum amount is $0.10 for any currency</li>
                            <li>3D Secure authentication may be required</li>
                            <li>Successful charges return status "CAPTURED"</li>
                            <li>Failed charges will show appropriate error codes</li>
                            <li>Redirect URLs handle payment completion flows</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function chargeTesting() {
        return {
            loading: false,
            response: null,
            error: null,
            form: {
                amount: '1.00',
                currency: 'KWD',
                token_id: '',
                customer_name: 'Test User',
                customer_email: 'test@example.com',
                description: ''
            },

            fillCustomer(name, email) {
                this.form.customer_name = name;
                this.form.customer_email = email;
            },

            async createCharge() {
                this.loading = true;
                this.response = null;
                this.error = null;

                try {
                    const response = await fetch('{{ route("dev.payments.charges.create") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.response = data;
                    } else {
                        this.error = data.error || 'Charge creation failed';
                    }
                } catch (err) {
                    this.error = 'Network error: ' + err.message;
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>

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
    .bg-primary-700 { background-color: var(--primary-700); }
    .focus\:ring-primary-500:focus { --tw-ring-color: var(--primary-500); }
    .focus\:border-primary-500:focus { --tw-border-opacity: 1; border-color: var(--primary-500); }
    .hover\:bg-primary-700:hover { background-color: var(--primary-700); }
    .focus\:ring-primary-500:focus { --tw-ring-color: var(--primary-500); }
    .text-primary-600 { color: var(--primary-600); }
    .hover\:text-primary-800:hover { color: var(--primary-800); }
    
    [x-cloak] { display: none !important; }
</style>
@endsection 