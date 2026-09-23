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

    public function switchUser(int $userId): void
    {
        $user = User::find($userId);
        if ($user) {
            Auth::login($user);
            session(['active_user_id' => $userId]);
            $this->activeUserId = $userId;
            $this->dispatch('userSwitched', userId: $userId);
            $this->redirect(request()->header('Referer', '/'));
        }
    }

    public function render()
    {
        $this->activeUserId = Auth::id() ?? session('active_user_id');
        $currentUser = $this->activeUserId ? User::with('department')->find($this->activeUserId) : null;
        $users = $currentUser ? User::with('department')->get() : collect();

        return view('livewire.user-switcher', [
            'users' => $users,
            'currentUser' => $currentUser,
        ]);
    }
}
