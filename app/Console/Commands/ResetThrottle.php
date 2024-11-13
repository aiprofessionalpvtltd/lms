<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ResetThrottle extends Command
{
    protected $signature = 'account:reset {mobile_no?}';
    protected $description = 'Reset the login throttle for a specific user or all users';

    public function handle()
    {
        $mobile_no = $this->argument('mobile_no');

        if ($mobile_no) {
            // Reset for a specific user
            Cache::forget($this->throttleKey($mobile_no));
            $this->info("Throttle reset for mobile number: $mobile_no");
        } else {
            // Reset for all users (this may require custom logic if using a unique throttle key per user)
            Cache::flush();
            $this->info("Throttle reset for all users.");
        }
    }

    protected function throttleKey($mobile_no)
    {
        return 'login:' . $mobile_no;
    }
}
