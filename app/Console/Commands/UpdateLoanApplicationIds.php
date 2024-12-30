<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoanApplication;

class UpdateLoanApplicationIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan-applications:update-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update application_id for all loan applications based on the latest logic';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Fetch all loan applications
        $loanApplications = LoanApplication::with('user.province')->get();

        foreach ($loanApplications as $loanApplication) {
            $authUser = $loanApplication->user;
            if (!$authUser) {
                $this->warn("Skipping loan application ID {$loanApplication->id} (user not found)");
                continue;
            }

            $userProvince = $authUser->province->name ?? null;
            $userId = $authUser->id;
            $year = date('y');

            $provincePrefixes = [
                'Punjab' => 'PJ',
                'Sindh' => 'SN',
                'KPK' => 'KP',
                'Balochistan' => 'BL',
                'Gilgit–Baltistan' => 'GB',
                'AJK' => 'AJK',
                'Federal' => 'ISB',
            ];

            $prefix = $provincePrefixes[$userProvince] ?? 'NA';
            $baseApplicationId = sprintf('%s%s%04d', $prefix, $year, $userId);

            // Find the next unique ID based on all records (including updated ones)
            $count = 1;
            do {
                $newApplicationId = sprintf('%s-%03d', $baseApplicationId, $count);
                $existingApplication = LoanApplication::where('application_id', $newApplicationId)
                    ->where('id', '!=', $loanApplication->id)
                    ->exists();
                $count++;
            } while ($existingApplication);

            // Only update if the ID is different
            if ($loanApplication->application_id !== $newApplicationId) {
                $loanApplication->application_id = $newApplicationId;
                $loanApplication->save();

                $this->info("Updated loan application ID {$loanApplication->id} to {$newApplicationId}");
            } else {
                $this->info("Loan application ID {$loanApplication->id} is already up to date.");
            }
        }

        $this->info('All loan applications have been updated successfully.');
        return 0;
    }

}
