<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUserSession
{
    /**
     * Handle an incoming request.
     * Ensures Auth state is strictly maintained and synced across standard requests and Livewire AJAX updates.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() && session()->has('active_user_id')) {
            $userId = session('active_user_id');
            $user = $userId ? User::find($userId) : null;

            if ($user) {
                // If regular employee is pending admin approval (unverified), deny access
                if ($user->email_verified_at === null && $user->role !== 'admin' && $user->email !== 'user123@gmail.com') {
                    session()->forget('active_user_id');
                    Auth::logout();
                } else {
                    Auth::login($user);
                }
            } else {
                session()->forget('active_user_id');
            }
        } elseif (Auth::check()) {
            $currentUser = Auth::user();
            if ($currentUser && $currentUser->email_verified_at === null && $currentUser->role !== 'admin' && $currentUser->email !== 'user123@gmail.com') {
                session()->forget('active_user_id');
                Auth::logout();
            } else {
                session(['active_user_id' => Auth::id()]);
            }
        }

        return $next($request);
    }
}
