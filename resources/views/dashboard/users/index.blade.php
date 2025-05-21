@extends('dashboard.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">إدارة المستخدمين</h2>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الدور</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            @switch($user->role)
                                @case('admin')
                                    مدير
                                    @break
                                @case('accountant')
                                    محاسب
                                    @break
                                @case('owner')
                                    مالك
                                    @break
                            @endswitch
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button type="button" 
                                onclick="openModal('editRole{{ $user->id }}')"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            تعديل الدور
                        </button>

                        <!-- Modal -->
                        <div id="editRole{{ $user->id }}" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity hidden">
                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                    <div class="relative transform overflow-hidden rounded-lg bg-white text-right shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                        <form action="{{ route('users.update.role', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="px-4 py-3 border-b border-gray-200">
                                                <h5 class="text-lg font-medium text-gray-900">تعديل دور {{ $user->name }}</h5>
                                                <button type="button" onclick="closeModal('editRole{{ $user->id }}')" class="absolute top-3 left-3 text-gray-400 hover:text-gray-500">
                                                    <span class="sr-only">إغلاق</span>
                                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="px-4 py-4">
                                                <select name="role" class="mt-1 block w-full pr-3 pl-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>مدير</option>
                                                    <option value="accountant" {{ $user->role === 'accountant' ? 'selected' : '' }}>محاسب</option>
                                                    <option value="owner" {{ $user->role === 'owner' ? 'selected' : '' }}>مالك</option>
                                                </select>
                                            </div>
                                            <div class="px-4 py-3 bg-gray-50 text-right space-x-2">
                                                <button type="button" 
                                                        onclick="closeModal('editRole{{ $user->id }}')"
                                                        class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    إلغاء
                                                </button>
                                                <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    حفظ التغييرات
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scrolling when modal is closed
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('fixed')) {
            closeModal(event.target.id);
        }
    }

    // Close modal with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('[role="dialog"]');
            modals.forEach(modal => {
                if (!modal.classList.contains('hidden')) {
                    closeModal(modal.id);
                }
            });
        }
    });
</script>
