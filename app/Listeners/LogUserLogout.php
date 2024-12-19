<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\LogActivity;

class LogUserLogout
{
    /**
     * Handle the logout event.
     */
    public function handle(Logout $event): void
    {
        $user = $event->user;

        LogActivity::create([
            'subject' => 'User logged out',
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'agent' => request()->header('User-Agent'),
            'user_id' => $user->id,
            'details' => 'User logged out at ' . now() . ' with role: ' . $user->roles[0]->name,
        ]);
    }
}

