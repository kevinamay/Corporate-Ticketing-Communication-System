<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UserSwitcher extends Component
{
    public ?int $activeUserId = null;

    public function mount(): void
    {
        $this->activeUserId = session('active_user_id', User::first()?->id ?? 1);
    }

    public function switchUser(int $userId): void
    {
        session(['active_user_id' => $userId]);
        $this->activeUserId = $userId;
        $this->dispatch('userSwitched', userId: $userId);
        $this->redirect(request()->header('Referer', '/'));
    }

    public function render()
    {
        $users = User::with('department')->get();
        $currentUser = User::with('department')->find($this->activeUserId) ?? $users->first();

        return view('livewire.user-switcher', [
            'users' => $users,
            'currentUser' => $currentUser,
        ]);
    }
}
