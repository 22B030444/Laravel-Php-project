<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([BuildingRoomSeeder::class]);
        $this->call(AdminSeeder::class);
//        $this->call(EmployeeSeeder::class);
//        $this->call(RepairRequestSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(NewsSeeder::class);
        $this->call(CategorySeeder::class);
    }
}
