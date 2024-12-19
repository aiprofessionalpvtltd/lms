<?php

namespace App\Console\Commands;

use App\Models\LogActivity;
use Illuminate\Console\Command;

class CleanOldLogs extends Command
{
    protected $signature = 'logs:clean';
    protected $description = 'Delete logs older than 30 days';

    public function handle()
    {
        $deleted = LogActivity::where('created_at', '<', now()->subDays(30))->forceDelete();
        $this->info('Old logs cleaned.');
    }
}
