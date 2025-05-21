<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ServiceRequest;

class PaymentStatusModal extends Component
{
    public $showModal = false;
    public $paymentId;
    public $paymentStatus;

    public function mount(ServiceRequest $payment)
    {
        $this->paymentId = $payment->id;
        $this->paymentStatus = $payment->payment_status;
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function updateStatus()
    {
        $payment = ServiceRequest::findOrFail($this->paymentId);
        $payment->update([
            'payment_status' => $this->paymentStatus
        ]);

        $this->showModal = false;
        $this->dispatch('payment-updated');
        session()->flash('success', 'تم تحديث الحالة بنجاح');
    }

    public function render()
    {
        return view('livewire.payment-status-modal');
    }
}