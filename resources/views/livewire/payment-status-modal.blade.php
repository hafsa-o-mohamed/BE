<div>
    <button wire:click="openModal" 
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        تعديل
    </button>

    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">تعديل حالة الدفع</h3>
                    
                    <div>
                        <select wire:model.live="paymentStatus" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="paid">مدفوع</option>
                            <option value="unpaid">غير مدفوع</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-2 space-x-reverse">
                        <button type="button"
                                wire:click="closeModal" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            إلغاء
                        </button>
                        <button type="button"
                                wire:click="updateStatus" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>