<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserSwitcher extends Component
{
    public ?int $activeUserId = null;

    public function mount(): void
    {
        $this->activeUserId = Auth::id() ?? session('active_user_id');
    }

    public function render()
    {
        $this->activeUserId = Auth::id() ?? session('active_user_id');
        $currentUser = $this->activeUserId ? User::with('department')->find($this->activeUserId) : null;

        return view('livewire.user-switcher', [
            'currentUser' => $currentUser,
        ]);
    }
}
