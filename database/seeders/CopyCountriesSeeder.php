<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CopyCountriesSeeder extends Seeder
{
    public function run(): void
    {
        // 🗂 Get data from the old database
        $oldCountries = DB::connection('old_mysql')->table('countries')->get();

        $inserted = 0;

        foreach ($oldCountries as $country) {
            // 🧭 Adjust continent_id mapping
            $newContinentId = match ($country->continent_id) {
                6 => 5,
                7 => 6,
                default => $country->continent_id,
            };

            // ⚙️ Insert into new database
            DB::table('countries')->insert([
                'en_name'      => $country->en_name ?? '',
                'ar_name'      => $country->ar_name ?? '',
                'continent_id' => $newContinentId ?? null,
                'fifa_name'    => $country->fifa_name ?? null,
                'iso2'         => $country->iso2 ?? null,
                'iso3'         => $country->iso3 ?? null,
                'latitude'     => $country->latitude ?? null,
                'longitude'    => $country->longitude ?? null,
                'borders'      => $country->borders ?? null,
                'image_path'   => $country->image_path ?? null,
                'status'       => $country->status ?? 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            $inserted++;
        }

        echo "✅ Successfully copied {$inserted} countries with updated continent mapping.\n";
    }
}
