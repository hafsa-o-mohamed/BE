@extends('dashboard.layout')

@section('content')
    <div class="flex justify-between items-center m-8 mb-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            فواتير الكهرباء 
        </h2>
        <button id="addBillButton" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
            إضافة فاتورة جديدة
        </button>
        
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($electricityBills->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبنى</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الرصيد الافتراضي</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ المخصوم</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الرصيد الحالي</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاريخ الإنشاء</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($electricityBills as $bill)
                                    <tr class="{{ $bill->current_balance < 0 ? 'bg-red-100' : 'bg-green-100' }}">
                                        <td class="px-6 py-4">{{ $bill->building->building_name }}</td>
                                        <td class="px-6 py-4">{{ $bill->default_balance }}</td>
                                        <td class="px-6 py-4">{{ $bill->subtracted_amount }}</td>
                                        <td class="px-6 py-4 {{ $bill->current_balance < 0 ? 'text-red-600' : '' }}">{{ $bill->current_balance }}</td>
                                        <td class="px-6 py-4">{{ $bill->created_at->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4">
                                            <button onclick="editBill({{ $bill->id }})" class="text-blue-600 hover:text-blue-900">
                                                تعديل
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center text-gray-500 py-4">لا توجد فواتير كهرباء</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addBillModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900 text-right mb-4">إضافة فاتورة جديدة</h3>
                <form action="{{ route('electricity-bills.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2 text-right" for="building">
                            المبنى
                        </label>
                        
                        <select name="building_id" id="building" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}">{{ $building->building_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2 text-right" for="default_balance">
                            الرصيد الافتراضي
                        </label>
                        <input type="number" step="0.01" name="default_balance" id="default_balance" value="1000" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2 text-right" for="current_balance">
                            الرصيد الحالي
                        </label>
                        <input type="number" step="0.01" name="current_balance" id="current_balance" value="{{ $current_balance ?? '' }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2 text-right" for="subtracted_amount">
                            المبلغ المخصوم
                        </label>
                        <input type="number" step="0.01" name="subtracted_amount" id="subtracted_amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <button type="button" onclick="closeAddBillModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            إلغاء
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 

@section('scripts')
<script>
    // Global functions
    function openAddBillModal() {
        const modal = document.getElementById('addBillModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeAddBillModal() {
        const modal = document.getElementById('addBillModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function calculateCurrentBalance() {
        const defaultBalance = 1000; // Hard-coded default balance
        const subtractedAmount = parseFloat(document.getElementById('subtracted_amount').value) || 0;
        const currentBalanceInput = document.getElementById('current_balance');
        const originalCurrentBalance = parseFloat(currentBalanceInput.dataset.originalBalance) || defaultBalance;

        if (subtractedAmount === 0) {
            currentBalanceInput.value = originalCurrentBalance.toFixed(2);
        } else {
            const newBalance = originalCurrentBalance - subtractedAmount;
            currentBalanceInput.value = newBalance.toFixed(2);
        }
    }

    function fetchLastElectricityBill(buildingId) {
        fetch(`/electricity-bills/last?building_id=${buildingId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('default_balance').value = 1000; // Always set default balance to 1000
                document.getElementById('current_balance').value = data.current_balance.toFixed(2);
                
                // Reset subtracted amount when changing buildings
                document.getElementById('subtracted_amount').value = '';
            })
            .catch(error => console.error('Error fetching last electricity bill:', buildingId));
    }

    // Wait for the DOM to be fully loaded
    window.addEventListener('load', function() {
        const addBillButton = document.getElementById('addBillButton');
        const modal = document.getElementById('addBillModal');
        const buildingSelect = document.getElementById('building');

        if (buildingSelect) {
            // Fetch initial electricity bill data when modal opens
            buildingSelect.addEventListener('change', function() {
                fetchLastElectricityBill(this.value);
            });
            
            // Trigger initial fetch for the first selected building
            if (buildingSelect.value) {
                fetchLastElectricityBill(buildingSelect.value);
            }
        }

        // Add event listener for subtracted amount changes
        document.getElementById('subtracted_amount').addEventListener('input', calculateCurrentBalance);

        if (addBillButton) {
            addBillButton.addEventListener('click', openAddBillModal);
        } else {
            console.error('Add Bill button not found');
        }

        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeAddBillModal();
                }
            });
        } else {
            console.error('Modal not found');
        }

        // Store the original current balance in a data attribute
        const currentBalanceInput = document.getElementById('current_balance');
        if (currentBalanceInput) {
            currentBalanceInput.dataset.originalBalance = currentBalanceInput.value;
        }
    });
</script>
@endsection 