<div>
    <button wire:click="openModal" 
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        تعديل 
    </button>

    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">تعديل حالة الطلب</h3>
                    
                    <div>
                        <select wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="pending">قيد الانتظار</option>
                            <option value="requested">تم الطلب</option>
                            <option value="completed">مكتمل</option>
                            <option value="cancelled">ملغي</option>
                        </select>
                    </div>

                    <div>
                        <label for="due_price" class="block text-sm font-medium text-gray-700">السعر المستحق</label>
                        <input type="number" 
                               wire:model="due_price" 
                               id="due_price" 
                               step="0.01"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="أدخل السعر">
                    </div>
                    @error('status') <span class="error">{{ $message }}</span> @enderror
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