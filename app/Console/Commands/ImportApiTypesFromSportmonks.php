<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ApiType;

class ImportApiTypesFromSportmonks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *   php artisan import:api-types
     */
    protected $signature = 'import:api-types';

    /**
     * The console command description.
     */
    protected $description = 'Import all API types (positions, highlights, stats, etc.) from SportMonks with full pagination.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("🚀 Starting import of API types from SportMonks...");

        $page = 1;
        $totalImported = 0;

        while (true) {
            $url = "https://api.sportmonks.com/v3/core/types?locale=ar&page={$page}";
            $this->line("📄 Fetching page {$page}...");

            $response = Http::get("https://api.sportmonks.com/v3/core/types", [
                'api_token' => env('SPORTMONKS_API_TOKEN'),
                'locale'    => 'ar',
                'page'      => $page,
            ]);

            if ($response->failed()) {
                $this->error("❌ Failed to fetch page {$page}: " . $response->body());
                break;
            }

            $data = $response->json();

            if (empty($data['data'])) {
                $this->warn("⚠️ No data found on page {$page}. Stopping import.");
                break;
            }

            foreach ($data['data'] as $item) {
                ApiType::updateOrCreate(
                    [
                        'developer_name' => $item['developer_name'] ?? null,
                    ],
                    [
                        'en_name'       => $this->translateName($item['developer_name']),
                        'ar_name'       => $item['name'] ?? null,
                        'model_type'    => $item['model_type'] ?? null,
                    ]
                );
                $totalImported++;
            }

            $this->info("✅ Page {$page} imported ({$totalImported} total so far).");

            // Pagination control
            if (isset($data['pagination']['has_more']) && $data['pagination']['has_more'] === true) {
                $page++;
                sleep(1); // optional small delay to avoid hitting rate limit
            } else {
                break;
            }
        }

        $this->info("🎯 Import completed successfully. Total records imported/updated: {$totalImported}");
        return Command::SUCCESS;
    }

    /**
     * Optional helper to provide readable English names if needed.
     */
    private function translateName(?string $developerName): ?string
    {
        return match($developerName) {
            'ATTACKER' => 'Attacker',
            'UNKNOWN' => 'Unknown',
            'VIDEO' => 'Video',
            'CLIP' => 'Clip',
            'FILE' => 'File',
            'PROBABILITY' => 'Probability',
            'VALUEBET' => 'Value Bet',
            'CORNERS' => 'Corners',
            'NEUTRAL_VENUE' => 'Neutral Venue',
            'KICKOFF' => 'Kickoff',
            'MATCH_DETAILS' => 'Match Details',
            'ET_2ND_HALF' => 'Extra Time (2nd Half)',
            'CAPTAIN' => 'Captain',
            'SHOTS_OFF_TARGET' => 'Shots Off Target',
            'SHOTS_TOTAL' => 'Total Shots',
            'ATTACKS' => 'Attacks',
            'DANGEROUS_ATTACKS' => 'Dangerous Attacks',
            'BALL_POSSESSION' => 'Ball Possession',
            'BALL_SAFE' => 'Ball Safe',
            'PENALTIES' => 'Penalties',
            'SHOTS_INSIDEBOX' => 'Shots Inside Box',
            'SHOTS_OUTSIDEBOX' => 'Shots Outside Box',
            'OFFSIDES' => 'Offsides',
            'GOALS' => 'Goals',
            'GOAL_KICKS' => 'Goal Kicks',
            default => $developerName,
        };
    }
}
