<?php

namespace App\Livewire;

use Livewire\Component;

class CreateNotification extends Component
{
    public $type = 'all';
    public $title;
    public $content;
    public $showModal = false;

    protected $listeners = ['openCreateNotificationModal' => 'openModal'];

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function send()
    {
        $this->validate([
            'type' => 'required|in:all,tenants,owners',
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        // Add your notification sending logic here

        $this->showModal = false;
        $this->dispatch('notificationSent');
        session()->flash('success', 'Notification sent successfully');
    }

    public function render()
    {
        return view('livewire.create-notification');
    }
}