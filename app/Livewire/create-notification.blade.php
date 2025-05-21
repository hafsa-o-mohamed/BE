<div>
    @if($showModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="notification-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">إرسال إشعار جديد</h3>
                <form wire:submit.prevent="send" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">نوع الإشعار</label>
                        <select wire:model.defer="type" class="w-full rounded-md border-gray-300">
                            <option value="all">جميع المستخدمين</option>
                            <option value="tenants">المستأجرين</option>
                            <option value="owners">الملاك</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الإشعار</label>
                        <input type="text" wire:model.defer="title" class="w-full rounded-md border-gray-300" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">محتوى الإشعار</label>
                        <textarea wire:model.defer="content" rows="4" class="w-full rounded-md border-gray-300" required></textarea>
                    </div>

                    <div class="flex justify-between">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            إرسال الإشعار
                        </button>
                        <button type="button" wire:click="closeModal" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>