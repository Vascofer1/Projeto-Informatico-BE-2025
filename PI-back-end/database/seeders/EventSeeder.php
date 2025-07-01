<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Participant;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_PT');

        for ($i = 0; $i < 10; $i++) {
            // Tipo de evento: passado, presente ou futuro
            $tipoEvento = $faker->randomElement(['passado', 'presente', 'futuro']);

            if ($tipoEvento === 'passado') {
                $startDate = Carbon::now()->subDays(rand(5, 30));
                $endDate = (clone $startDate)->addDays(rand(0, 4));
            } elseif ($tipoEvento === 'presente') {
                $startDate = Carbon::now();
                $endDate = (clone $startDate)->addDays(rand(0, 2));
            } else { // futuro
                $startDate = Carbon::now()->addDays(rand(1, 30));
                $endDate = (clone $startDate)->addDays(rand(0, 4));
            }

            $startTime = Carbon::createFromTime(rand(8, 18), rand(0, 59));
            $endTime = (clone $startTime)->addHours(rand(1, 4))->addMinutes(rand(0, 59));

            $event = Event::create([
                'name' => $faker->sentence(3),
                'description' => $faker->paragraph,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'start_time' => $startTime->format('H:i'),
                'end_time' => $endTime->format('H:i'),
                'location' => $faker->city,
                'limit_participants' => $faker->numberBetween(10, 100),
                'category' => $faker->randomElement(['Sports', 'Technology', 'Health']),
                'type' => $faker->randomElement(['Workshop', 'Lecture', 'Conference']),
                'image' => null,
                'custom_background' => null,
            ]);

            // Criar participantes
            for ($j = 0; $j < rand(5, 15); $j++) {
                // Status com base no tipo de evento
                if ($tipoEvento === 'futuro') {
                    $status = 'Unconfirmed';
                } else {
                    $status = $faker->randomElement(['Confirmed', 'Unconfirmed']);
                }

                Participant::create([
                    'event_id' => $event->id,
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'phone' => $faker->phoneNumber,
                    'qrcode' => null,
                    'status' => $status,
                    'code' => strtoupper(Str::random(8)),
                ]);
            }
        }
    }
}
