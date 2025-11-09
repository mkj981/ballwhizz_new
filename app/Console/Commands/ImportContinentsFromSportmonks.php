<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Continent;

class ImportContinentsFromSportmonks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage examples:
     *   php artisan import:continents en
     *   php artisan import:continents ar
     */
    protected $signature = 'import:continents {lang=en}';

    /**
     * The console command description.
     */
    protected $description = 'Import continents from Sportmonks API (English or Arabic)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $lang = $this->argument('lang');

        $this->info("🌍 Importing continents from Sportmonks (lang: {$lang})...");

        $response = $this->getContinents($lang);

        if (empty($response['data'])) {
            $this->error('❌ No continent data found.');
            return Command::FAILURE;
        }

        foreach ($response['data'] as $continent) {
            $existing = Continent::find($continent['id']);

            if ($existing) {
                // Update Arabic name if language = ar
                if ($lang === 'ar') {
                    $existing->ar_name = $continent['name'] ?? null;
                    $existing->save();
                    $this->line("🔄 Updated Arabic name for: {$existing->id}");
                }
            } else {
                // Insert new record (English version)
                $insert = new Continent();
                $insert->id = $continent['id'];
                $insert->en_name = $continent['name'] ?? null;
                $insert->code = $continent['code'] ?? null;
                $insert->save();

                $this->line("✅ Inserted continent: {$insert->en_name}");
            }
        }

        $this->info('🎉 Continents imported successfully!');
        return Command::SUCCESS;
    }

    /**
     * Fetch continents from the Sportmonks API.
     */
    protected function getContinents(string $lang): ?array
    {
        $token = config('services.sportmonks.token', 'esEnL4WkxrGr46O6ABq99INL1Eww4iTDYz8rtABQwapL3QX8ujNiktU4hnH0');
        $url = "https://api.sportmonks.com/v3/core/continents?api_token={$token}&locale={$lang}";

        $response = Http::timeout(30)->get($url);

        if ($response->failed()) {
            $this->error('❌ Failed to fetch continents from API.');
            return [];
        }

        return $response->json();
    }
}
