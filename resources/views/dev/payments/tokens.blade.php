@extends('dev.layout')

@section('title', 'Token Testing')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Token Testing</h1>
        <p class="mt-2 text-sm text-gray-600">
            Test Tap Payments token creation API. Tokens securely contain credit card details and can be used for charges.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Token Creation Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" 
                 x-data="tokenTesting()" 
                 x-init="initFromUrl()">
                
                <!-- Tab Navigation -->
                <div class="mb-6">
                    <nav class="flex space-x-8" aria-label="Tabs">
                        <button 
                            @click="activeTab = 'card'"
                            :class="activeTab === 'card' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                        >
                            Card Token
                        </button>
                        <button 
                            @click="activeTab = 'applepay'"
                            :class="activeTab === 'applepay' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                        >
                            Apple Pay Token
                        </button>
                    </nav>
                </div>

                <!-- Apple Pay Token Form -->
                <div x-show="activeTab === 'applepay'" x-transition>
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Convert Apple Pay Token</h2>
                    
                    <form @submit.prevent="createApplePayToken()">
                        <div class="space-y-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1 md:flex md:justify-between">
                                        <p class="text-sm text-blue-700">
                                            Paste the Apple Pay token data you received from Apple Pay JS. This will convert it to a Tap token for processing.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Apple Pay Token Data -->
                            <div>
                                <label for="applepay_data" class="block text-sm font-medium text-gray-700">Apple Pay Data</label>
                                <textarea 
                                    id="applepay_data"
                                    x-model="applePayForm.data"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    placeholder="Encrypted payment data from Apple Pay..."
                                    required
                                ></textarea>
                            </div>

                            <!-- Transaction ID -->
                            <div>
                                <label for="applepay_transaction_id" class="block text-sm font-medium text-gray-700">Transaction ID</label>
                                <input 
                                    type="text" 
                                    id="applepay_transaction_id"
                                    x-model="applePayForm.transactionId"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    placeholder="0c4352c073ad460044517596dbbf8fe503a837138c8c2de18fddb37ca3ec5295"
                                    required
                                >
                            </div>

                            <!-- Public Key Hash -->
                            <div>
                                <label for="applepay_public_key_hash" class="block text-sm font-medium text-gray-700">Public Key Hash</label>
                                <input 
                                    type="text" 
                                    id="applepay_public_key_hash"
                                    x-model="applePayForm.publicKeyHash"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    placeholder="LjAAyv6vb6jOEkjfG7L1a5OR2uCTHIkB61DaYdEWD+w="
                                    required
                                >
                            </div>

                            <!-- Ephemeral Public Key -->
                            <div>
                                <label for="applepay_ephemeral_public_key" class="block text-sm font-medium text-gray-700">Ephemeral Public Key</label>
                                <textarea 
                                    id="applepay_ephemeral_public_key"
                                    x-model="applePayForm.ephemeralPublicKey"
                                    rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    placeholder="MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAELAfDie0Ie1TxCcrFt69BzcQ52+F+Fhm5mDw6pMR54AzoFMgdGPRbqoLtFpoSe0FI/m0cqRMOVM2W4Bz9jVZZHA=="
                                    required
                                ></textarea>
                            </div>

                            <!-- Signature -->
                            <div>
                                <label for="applepay_signature" class="block text-sm font-medium text-gray-700">Signature</label>
                                <textarea 
                                    id="applepay_signature"
                                    x-model="applePayForm.signature"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    placeholder="Digital signature from Apple Pay..."
                                    required
                                ></textarea>
                            </div>

                            <!-- Version -->
                            <div>
                                <label for="applepay_version" class="block text-sm font-medium text-gray-700">Version</label>
                                <select 
                                    id="applepay_version"
                                    x-model="applePayForm.version"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    required
                                >
                                    <option value="">Select Version</option>
                                    <option value="EC_v1">EC_v1</option>
                                    <option value="RSA_v1">RSA_v1</option>
                                </select>
                            </div>

                            <!-- Quick Fill Button -->
                            <button 
                                type="button"
                                @click="fillSampleApplePayData()"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            >
                                Fill Sample Data
                            </button>
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
                                <span x-show="!loading">Convert Apple Pay Token</span>
                                <span x-show="loading">Converting Token...</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Card Token Form -->
                <div x-show="activeTab === 'card'" x-transition>
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Create Token</h2>
                
                    <form @submit.prevent="createToken()">
                        <!-- Card Information -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-medium text-gray-900 border-b border-gray-200 pb-2">Card Information</h3>
                            
                            <!-- Card Number -->
                            <div>
                                <label for="card_number" class="block text-sm font-medium text-gray-700">Card Number</label>
                                <input 
                                    type="text" 
                                    id="card_number"
                                    x-model="form.card_number"
                                    @input="formatCardNumber()"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    placeholder="4508 7500 1574 1019"
                                    required
                                >
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <!-- Expiry Month -->
                                <div>
                                    <label for="exp_month" class="block text-sm font-medium text-gray-700">Month</label>
                                    <select 
                                        id="exp_month"
                                        x-model="form.exp_month"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                        required
                                    >
                                        <option value="">MM</option>
                                        <option value="1">01</option>
                                        <option value="2">02</option>
                                        <option value="3">03</option>
                                        <option value="4">04</option>
                                        <option value="5">05</option>
                                        <option value="6">06</option>
                                        <option value="7">07</option>
                                        <option value="8">08</option>
                                        <option value="9">09</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                    </select>
                                </div>

                                <!-- Expiry Year -->
                                <div>
                                    <label for="exp_year" class="block text-sm font-medium text-gray-700">Year</label>
                                    <select 
                                        id="exp_year"
                                        x-model="form.exp_year"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                        required
                                    >
                                        <option value="">YYYY</option>
                                        <template x-for="year in yearOptions" :key="year">
                                            <option :value="year" x-text="year"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- CVC -->
                                <div>
                                    <label for="cvc" class="block text-sm font-medium text-gray-700">CVC</label>
                                    <input 
                                        type="text" 
                                        id="cvc"
                                        x-model="form.cvc"
                                        maxlength="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                        placeholder="123"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Cardholder Name -->
                            <div>
                                <label for="card_name" class="block text-sm font-medium text-gray-700">Cardholder Name</label>
                                <input 
                                    type="text" 
                                    id="card_name"
                                    x-model="form.card_name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    placeholder="John Doe"
                                    required
                                >
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="space-y-4 mt-6">
                            <h3 class="text-sm font-medium text-gray-900 border-b border-gray-200 pb-2">Address Information</h3>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Country -->
                                <div>
                                    <label for="address_country" class="block text-sm font-medium text-gray-700">Country</label>
                                    <input 
                                        type="text" 
                                        id="address_country"
                                        x-model="form.address_country"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                        placeholder="Kuwait"
                                        required
                                    >
                                </div>

                                <!-- City -->
                                <div>
                                    <label for="address_city" class="block text-sm font-medium text-gray-700">City</label>
                                    <input 
                                        type="text" 
                                        id="address_city"
                                        x-model="form.address_city"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                        placeholder="Kuwait City"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Address Line 1 -->
                            <div>
                                <label for="address_line1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                                <input 
                                    type="text" 
                                    id="address_line1"
                                    x-model="form.address_line1"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    placeholder="Salmiya, 21"
                                    required
                                >
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Street -->
                                <div>
                                    <label for="address_street" class="block text-sm font-medium text-gray-700">Street (Optional)</label>
                                    <input 
                                        type="text" 
                                        id="address_street"
                                        x-model="form.address_street"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                        placeholder="Salim"
                                    >
                                </div>

                                <!-- Avenue -->
                                <div>
                                    <label for="address_avenue" class="block text-sm font-medium text-gray-700">Avenue (Optional)</label>
                                    <input 
                                        type="text" 
                                        id="address_avenue"
                                        x-model="form.address_avenue"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                        placeholder="Gulf"
                                    >
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
                                <span x-show="!loading">Create Token</span>
                                <span x-show="loading">Creating Token...</span>
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
                </div>
            </div>
        </div>

        <!-- Test Cards Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Fill Test Cards</h2>
                <div class="space-y-3">
                    @foreach($testCards as $card)
                        <button 
                            @click="fillTestCard({{ json_encode($card) }})"
                            class="w-full text-left p-3 border border-gray-200 rounded-lg hover:border-gray-300 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded {{ $card['color'] }} flex items-center justify-center">
                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $card['name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $card['brand'] }}</p>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>

                <!-- Documentation Link -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Documentation</h3>
                    <p class="text-xs text-gray-600 mb-3">
                        Tap Payments Token API creates secure, single-use tokens for credit card transactions.
                    </p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>• Tokens are single-use and expire quickly</li>
                        <li>• PCI compliant token storage</li>
                        <li>• Can be used in charges and authorizations</li>
                        <li>• Card details are never stored on your servers</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function tokenTesting() {
        return {
            loading: false,
            response: null,
            error: null,
            activeTab: 'card', // Default to card tab
            form: {
                card_number: '',
                exp_month: '',
                exp_year: '',
                cvc: '',
                card_name: 'Test User',
                address_country: 'Kuwait',
                address_city: 'Kuwait City',
                address_line1: 'Salmiya, 21',
                address_street: 'Salim',
                address_avenue: 'Gulf'
            },
            applePayForm: {
                data: '',
                transactionId: '',
                publicKeyHash: '',
                ephemeralPublicKey: '',
                signature: '',
                version: 'EC_v1'
            },
            
            get yearOptions() {
                const currentYear = new Date().getFullYear();
                const years = [];
                for (let i = currentYear; i <= currentYear + 10; i++) {
                    years.push(i);
                }
                return years;
            },

            initFromUrl() {
                const urlParams = new URLSearchParams(window.location.search);
                const cardParam = urlParams.get('card');
                if (cardParam) {
                    // Find the matching test card and fill the form
                    const testCards = @json($testCards);
                    const matchingCard = testCards.find(card => card.number === cardParam);
                    if (matchingCard) {
                        this.fillTestCard(matchingCard);
                    }
                }
            },

            formatCardNumber() {
                // Remove all non-digits and add spaces every 4 digits
                const value = this.form.card_number.replace(/\D/g, '');
                const formatted = value.replace(/(\d{4})(?=\d)/g, '$1 ');
                this.form.card_number = formatted;
            },

            fillTestCard(card) {
                this.form.card_number = card.number.replace(/(\d{4})(?=\d)/g, '$1 ');
                this.form.exp_month = parseInt(card.exp_month);
                this.form.exp_year = parseInt(card.exp_year);
                this.form.cvc = card.cvc;
                this.response = null;
                this.error = null;
            },

            fillSampleApplePayData() {
                // Fill with the sample data from the curl example
                this.applePayForm.data = 'CM8i9PNK4yXtKO3xmOn6uyYOWmQ+iX9/Oc0EWHJZnPZ/IAEe2UYNCfely3dgq3veEygmQcl0s8lvMeCIZAbbBvbZWPKng9lfUwP2u3IUOFfFyI4beE9znpQ/e0nyQiVh8NFyZun8o0/YZfdFhaBy8bunveULZkWODZy3vg1LLTk0wSRfzbiFav/krgeMvztl8U85Fefl1VJVoJbW/jtShwDkusHizw/p/hkLiOFcCYSz7h9culZQMTWfqsxIfTuY3mOl+NhjAHPP+UFv4wefXrQL9MKO2cI6ttXOp5k6M6mFV/Qe0fbmJ6GnDWDMSiikW+3eL0yi0IApAKmmVgPS+uk42dyhrnSPhB6A7EJBmhEEb3ErL1I69Jq9REjDHp+VoZR0fAbDtpbjKKMo';
                this.applePayForm.transactionId = '0c4352c073ad460044517596dbbf8fe503a837138c8c2de18fddb37ca3ec5295';
                this.applePayForm.publicKeyHash = 'LjAAyv6vb6jOEkjfG7L1a5OR2uCTHIkB61DaYdEWD+w=';
                this.applePayForm.ephemeralPublicKey = 'MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAELAfDie0Ie1TxCcrFt69BzcQ52+F+Fhm5mDw6pMR54AzoFMgdGPRbqoLtFpoSe0FI/m0cqRMOVM2W4Bz9jVZZHA==';
                this.applePayForm.signature = 'bNEa18hOrgG/oFk/o0CtYR01vhm+34RbStas1T+tkFLpP0eG5A+9P7k9eYq8OL5q+V8xyRWrG8YcsV9JaHA32hNjS1UAPalnClSnrn2SXimafLGPv4OaImH/Ta9uuKPVdJAfa26UDtAYhlsXiBY5MAVytRUl+Cec5DkmihNwI7GJaR6mD1Hlz+7AFrHL31R+hPM4lVp3yJKsZYFzadUKpzZpjhub6iQG81WhN2LcBEpbf13ksOYHpUWpKaa9YHxpO2CnIGzEWwdxD8nFkWyEeCt/mFs1Lq504diIBaq57p+nNX+Iydy9LIsM4TvT4dj5Dv5gn5A3gbcEIuR3hcw+HWp';
                this.applePayForm.version = 'EC_v1';
                this.response = null;
                this.error = null;
            },

            async createToken() {
                this.loading = true;
                this.response = null;
                this.error = null;

                try {
                    const response = await fetch('{{ route("dev.payments.tokens.create") }}', {
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
                        this.error = data.error || 'Token creation failed';
                    }
                } catch (err) {
                    this.error = 'Network error: ' + err.message;
                } finally {
                    this.loading = false;
                }
            },

            async createApplePayToken() {
                this.loading = true;
                this.response = null;
                this.error = null;

                try {
                    // Prepare the token_data object according to Tap API structure
                    const tokenData = {
                        token_data: {
                            data: this.applePayForm.data,
                            header: {
                                transactionId: this.applePayForm.transactionId,
                                publicKeyHash: this.applePayForm.publicKeyHash,
                                ephemeralPublicKey: this.applePayForm.ephemeralPublicKey,
                            },
                            signature: this.applePayForm.signature,
                            version: this.applePayForm.version,
                        }
                    };

                    console.log('Sending Apple Pay token data:', tokenData);

                    const response = await fetch('{{ route("dev.payments.tokens.apple-pay") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(tokenData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.response = data;
                        console.log('Apple Pay token conversion successful:', data);
                    } else {
                        this.error = data.error || 'Apple Pay token conversion failed';
                        console.error('Apple Pay token conversion failed:', data);
                    }
                } catch (err) {
                    this.error = 'Network error: ' + err.message;
                    console.error('Apple Pay token conversion error:', err);
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
    
    [x-cloak] { display: none !important; }
</style>
@endsection 