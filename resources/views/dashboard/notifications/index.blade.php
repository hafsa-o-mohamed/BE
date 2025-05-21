@extends('dashboard.layout')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">إدارة الإشعارات</h2>
        <button onclick="openNotificationModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            إرسال إشعار جديد
        </button>
    </div>
                <!-- Add Bill Modal -->
                <div id="notificationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 text-center mb-6">إرسال إشعار جديد</h3>
                            <form id="notificationForm" method="POST" action="{{ route('notifications.store') }}">
                                @csrf
                                <!-- Title Field -->
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">عنوان الإشعار</label>
                                    <input type="text" name="title" class="form-input w-full" required>
                                </div>

                                <!-- Content Field -->
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">محتوى الإشعار</label>
                                    <textarea name="content" class="form-textarea w-full" rows="3" required></textarea>
                                </div>

                                <!-- Notification Type Selection -->
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">نوع الإشعار</label>
                                    <select name="notification_type" id="notificationType" class="form-select w-full" onchange="toggleNotificationFields()" required>
                                        <option value="">اختر نوع الإشعار</option>
                                        <option value="contract">تسويق</option>
                                        <option value="electricity">إعلانات</option>
                                        <option value="water">إشعارات</option>
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
                                        <option value="">اختر المالك</option>
                                        @foreach($owners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Building Selection (Hidden by default) -->
                                <div id="buildingField" class="mb-4 hidden">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">المبنى</label>
                                    <select name="building_id" class="form-select w-full">
                                        <option value="">اختر المبنى</option>
                                        @foreach($buildings as $building)
                                            <option value="{{ $building->id }}">{{ $building->building_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                              
                            

                                <div class="flex justify-end space-x-4">
                                    <button type="button" onclick="closeNotificationModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
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


    <div>
        <h3 class="text-lg font-semibold mb-4">سجل الإشعارات</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Notifications will be listed here -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    function openNotificationModal() {
        document.getElementById('notificationModal').classList.remove('hidden');
        // Clear form when opening
        document.getElementById('notificationForm').reset();
        // Reset all conditional fields to hidden state
        document.getElementById('contractField').classList.add('hidden');
        document.getElementById('userField').classList.add('hidden');
        document.getElementById('buildingField').classList.add('hidden');
        document.getElementById('amountField').classList.remove('hidden');
    }

    function closeNotificationModal() {
        document.getElementById('notificationModal').classList.add('hidden');
    }

    function toggleNotificationFields() {
        const notificationType = document.getElementById('notificationType').value;
        const contractField = document.getElementById('contractField');
        const amountField = document.getElementById('amountField');

        if (notificationType === 'contract') {
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