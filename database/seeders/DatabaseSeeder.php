<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ModuleSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            PermissionsSeeder::class,
            LoanDurationSeeder::class,
            ProductServiceSeeder::class,
            LoanPurposeSeeder::class,
            DocumentTypeSeeder::class,
        ]);

        $this->createPersonalAccessClient();


    }

    private function createPersonalAccessClient()
    {
        $clientRepository = new ClientRepository();
        $clientRepository->createPersonalAccessClient(
            null, 'LMS', env('APP_URL')
        );

        $this->command->info('Personal access client created successfully.');
    }

}
