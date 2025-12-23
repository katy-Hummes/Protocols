<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ProtocolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $userIds = DB::table('people')->pluck('id')->toArray();
        $departmentIds = DB::table('departments')->pluck('id')->toArray();

        foreach (range(1, 100) as $index) {

            $timestamps = $faker->date();
            
            DB::table('protocols')->insert([
                'department_id' => $faker->randomElement($departmentIds),
                'people_id' => $faker->randomElement($userIds),
                'description' => $faker->text,
                'date' => $faker->date('Y-m-d'),
                'term' => $faker->numberBetween(5, 30),
                'created_at' => $timestamps,
                'updated_at' => $timestamps,
            ]);
        }
    }
}
