<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create demo vehicles.
        $brands = [
            ['Toyota', 'Corolla'],
            ['Ford', 'Transit'],
            ['Renault', 'Clio'],
            ['Fiat', 'Doblo'],
            ['Volkswagen', 'Caddy'],
        ];

        for ($i = 1; $i <= 10; $i++) {
            [$brand, $model] = $brands[($i - 1) % count($brands)];

            Vehicle::updateOrCreate(
                ['plate' => sprintf('34 DEM %03d', $i)],
                [
                    'brand' => $brand,
                    'model' => $model,
                    'year' => 2018 + ($i % 8),
                    'current_km' => 15000 + ($i * 12000),
                    'status' => $i === 3 ? 'in_service' : 'active',
                ]
            );
        }

        // 2. Create demo drivers.
        for ($i = 1; $i <= 10; $i++) {
            Driver::updateOrCreate(
                ['phone' => sprintf('0500000%04d', $i)],
                [
                    'name' => "Demo Driver {$i}",
                    'license_type' => $i % 3 === 0 ? 'CE' : 'B',
                    'status' => $i === 7 ? 'inactive' : 'active',
                ]
            );
        }

        // 3. Create demo trips.
        $vehicles = Vehicle::where('plate', 'like', '34 DEM %')
            ->orderBy('id')
            ->get();

        $drivers = Driver::where('name', 'like', 'Demo Driver %')
            ->orderBy('id')
            ->get();

        $cities = ['Istanbul', 'Ankara', 'Izmir', 'Bursa', 'Antalya'];

        for ($i = 0; $i < 25; $i++) {
            $start = Carbon::now()->subDays(25 - $i)->setTime(9, 0);
            $end = $start->copy()->addHours(4);

            $status = $i < 20 ? 'completed' : 'planned';

            Trip::updateOrCreate(
                ['notes' => "DEMO-TRIP-{$i}"],
                [
                    'vehicle_id' => $vehicles[$i % $vehicles->count()]->id,
                    'driver_id' => $drivers[$i % $drivers->count()]->id,
                    'start_time' => $start,
                    'end_time' => $status === 'completed' ? $end : null,
                    'origin' => $cities[$i % count($cities)],
                    'destination' => $cities[($i + 2) % count($cities)],
                    'status' => $status,
                ]
            );
        }

        $this->command->info('Demo data created successfully.');
    }
}