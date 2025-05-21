@extends('dashboard.layout')

@section('content')
<div class="container mx-auto px-4 pt-10">
    <div class="flex justify-center">
        <div class="w-full">
            <div class="bg-white rounded-lg shadow-md">
                <div class="flex justify-between items-center px-6 py-4 border-b">
                    <h2 class="text-2xl font-bold text-gray-800">الفواتير</h2>
                    <button onclick="openBillModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                        إنشاء فاتورة جديدة
                    </button>
                </div>

                <!-- Add Bill Modal -->
                <div id="billModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 text-center mb-6">إنشاء فاتورة جديدة</h3>
                            <form id="billForm" method="POST" action="{{ route('bills.create-from-modal') }}">
                                @csrf
                                <!-- Bill Type Selection -->
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">نوع الفاتورة</label>
                                    <select name="bill_type" id="billType" class="form-select w-full" onchange="toggleBillFields()">
                                        <option value="">اختر نوع الفاتورة</option>
                                        <option value="contract">عقد</option>
                                        <option value="electricity">كهرباء</option>
                                        <option value="water">مياه</option>
                                    </select>
                                </div>

                                <!-- Target Type Selection -->
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">نوع الهدف</label>
                                    <select name="target_type" id="targetType" class="form-select w-full" onchange="toggleTargetFields()">
                                        <option value="">اختر نوع الهدف</option>
                                        <option value="owner">مالك</option>
                                        <option value="building">مبنى</option>
                                    </select>
                                </div>

                                <!-- Owner Selection (Hidden by default) -->
                                <div id="userField" class="mb-4 hidden">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">المالك</label>
                                    <select name="owner_id" class="form-select w-full">
                                        @foreach($owners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Building Selection (Hidden by default) -->
                                <div id="buildingField" class="mb-4 hidden">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">المبنى</label>
                                    <select name="building_id" class="form-select w-full">
                                        @foreach($buildings as $building)
                                            <option value="{{ $building->id }}">{{ $building->building_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Contract Type (Hidden by default) -->
                                <div id="contractField" class="mb-4 hidden">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">نوع العقد</label>
                                    <select name="contract_type" class="form-select w-full">
                                        @foreach($contracts as $contract)
                                            <option value="{{ $contract->id }}">{{ $contract->contract_type }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Amount Field (Hidden for contract type) -->
                                <div id="amountField" class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">المبلغ</label>
                                    <input type="number" name="amount" class="form-input w-full" step="0.01">
                                </div>

                                <div class="flex justify-end space-x-4">
                                    <button type="button" onclick="closeBillModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                                        إلغاء
                                    </button>
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                                        حفظ
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Horizontal Filter Bar -->
                    <form method="GET" action="{{ route('bills.filter') }}" class="mb-4">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Filter by Bill Type -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">نوع الفاتورة</label>
                                <select name="bill_type" class="form-select">
                                    <option value="">كل الأنواع</option>
                                    <option value="rent" {{ request('bill_type') == 'rent' ? 'selected' : '' }}>إيجار</option>
                                    <option value="contract" {{ request('bill_type') == 'contract' ? 'selected' : '' }}>عقد</option>
                                    <option value="electricity" {{ request('bill_type') == 'electricity' ? 'selected' : '' }}>كهرباء</option>
                                    <option value="water" {{ request('bill_type') == 'water' ? 'selected' : '' }}>مياه</option>
                                </select>
                            </div>
                            <!-- Filter by Building -->
                            <div>
                                <label for="building_id" class="block text-gray-700 text-sm font-bold mb-2">المبنى</label>
                                <select name="building_id" id="building_id" class="form-select">
                                    <option value="">كل المباني</option>
                                    @foreach($buildings as $building)
                                        <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                                            {{ $building->building_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Filter by Status -->
                            <div>
                                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">الحالة</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">كل الحالات</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار الدفع</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                                    تصفية
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Bills Table -->
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الرقم</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المالك</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الشقة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبنى</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bills as $bill)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $bill->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $bill->owner->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $bill->owner->apartments[0]->apartment_number}}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $bill->owner->apartments[0]->building->building_name}}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($bill->bill_type === 'rent')
                                        إيجار
                                    @elseif($bill->bill_type === 'electricity')
                                        كهرباء
                                    @elseif($bill->bill_type === 'water')
                                        مياه
                                    @else
                                        {{ $bill->bill_type }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">${{ number_format($bill->due_amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($bill->status === 'pending')
                                        بانتظار الدفع
                                    @else
                                        {{ $bill->status }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('bills.edit', $bill) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm ml-2">تعديل</a>
                                    <form action="{{ route('bills.destroy', $bill) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm" 
                                            onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        {{ $bills->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openBillModal() {
        document.getElementById('billModal').classList.remove('hidden');
    }

    function closeBillModal() {
        document.getElementById('billModal').classList.add('hidden');
    }

    function toggleBillFields() {
        const billType = document.getElementById('billType').value;
        const contractField = document.getElementById('contractField');
        const amountField = document.getElementById('amountField');

        if (billType === 'contract') {
            contractField.classList.remove('hidden');
            amountField.classList.add('hidden');
        } else {
            contractField.classList.add('hidden');
            amountField.classList.remove('hidden');
        }
    }

    function toggleTargetFields() {
        const targetType = document.getElementById('targetType').value;
        const userField = document.getElementById('userField');
        const buildingField = document.getElementById('buildingField');

        userField.classList.add('hidden');
        buildingField.classList.add('hidden');

        if (targetType === 'owner') {
            userField.classList.remove('hidden');
        } else if (targetType === 'building') {
            buildingField.classList.remove('hidden');
        }
    }
</script>
@endsection