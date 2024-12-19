<?php

namespace App\Listeners;

use App\Models\LogActivity;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Add a log entry
        LogActivity::create([
            'user_id' => $user->id,
            'subject' => 'User logged in',
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'agent' => request()->header('User-Agent'),
            'details' => 'User logged in at ' . now() . ' with role: ' . $user->roles[0]->name,
        ]);
    }
}
