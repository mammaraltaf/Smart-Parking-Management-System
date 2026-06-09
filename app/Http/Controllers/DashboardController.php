<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return match ($user->role) {
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            User::ROLE_GUARD => redirect()->route('guard.dashboard'),
            default => redirect()->route('driver.dashboard'),
        };
    }
}
