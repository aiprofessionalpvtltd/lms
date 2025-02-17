<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
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
            CountrySeeder::class,
            ProvinceSeeder::class,
            DistrictSeeder::class,
            CitySeeder::class,

            ModuleSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            PermissionsSeeder::class,
            LoanDurationSeeder::class,
            ProductServiceSeeder::class,
            LoanPurposeSeeder::class,
            DocumentTypeSeeder::class,

            ProductSeeder::class,
            GenderSeeder::class,
            MaritalStatusSeeder::class,
            NationalitySeeder::class,
            EmploymentStatusSeeder::class,
            IncomeSourceSeeder::class,

            EducationSeeder::class,
            RelationshipSeeder::class,
            JobTitleSeeder::class,
            ResidenceTypeSeeder::class,
            ResidenceDurationSeeder::class,
            ExistingLoanSeeder::class,
            BanksTableSeeder::class,

            VendorProductsSeeder::class,


        ]);

        $this->createPersonalAccessClient();
        $this->runPermissionUpdateCommand();


    }

    private function createPersonalAccessClient()
    {
        $clientRepository = new ClientRepository();
        $clientRepository->createPersonalAccessClient(
            null, 'LMS', env('APP_URL')
        );

        $this->command->info('Personal access client created successfully.');
    }

    private function runPermissionUpdateCommand()
    {
        Artisan::call('permission:update');
        $this->command->info('Permissions updated successfully.');
    }

}
