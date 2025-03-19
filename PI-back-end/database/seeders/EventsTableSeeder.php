<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            DB::table('events')->insert([
                'name' => $faker->sentence(3),
                'description' => $faker->paragraph(),
                'start_date' => $startDate = Carbon::now()->addDays(rand(1, 30)),
                'end_date' => $startDate->copy()->addDays(rand(1, 3)),
                'location' => $faker->city,
                'limit_participants' => $faker->numberBetween(50, 1000),
                'category' => $faker->randomElement(['Tecnologia', 'Negócios', 'Saúde', 'Educação', 'Entretenimento']),
                'type' => $faker->randomElement(['Presencial', 'Online', 'Híbrido']),
                'status' => $faker->randomElement(['por decorrer', 'a decorrer', 'finalizado']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
