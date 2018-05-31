<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AuthMethodsTableSeeder::class);
        $this->call(BEMenuSeeder::class);
        $this->call(CBParticipatoryToolsTableSeeder::class);
        $this->call(DashboardSeeder::class);
        $this->call(FlagTypesTableSeeder::class);
        $this->call(ModulesTableSeeder::class);
        $this->call(ParametersTypesTableSeeder::class);
        $this->call(SectionsCMSTableSeeder::class);
        $this->call(SiteConfigurationsTableSeeder::class);
        $this->call(TypeParametersCMSTableSeeder::class);
        $this->call(VoteConfigurationsTableSeeder::class);
        $this->call(AddPermissionsToPermsTableSeeder::class);
        $this->call(AddCbPermissionsToPermsTableSeeder::class);
    }
}
