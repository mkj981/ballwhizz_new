<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportPositionsFromApiType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *   php artisan import:positions
     */
    protected $signature = 'import:positions {--truncate : Truncate the positions table before import}';

    /**
     * The console command description.
     */
    protected $description = 'Import all positions from api_types where model_type=position into positions table using the same IDs.';

    public function handle(): int
    {
        $this->info('🚀 Starting import from api_types → positions...');

        $apiTypes = DB::table('api_types')->where('model_type', 'position')->get();

        if ($apiTypes->isEmpty()) {
            $this->warn('⚠️ No position records found in api_types.');
            return Command::SUCCESS;
        }

        if ($this->option('truncate')) {
            DB::table('positions')->truncate();
            $this->warn('🧹 positions table truncated before import.');
        }

        $existingCodes = [];
        $insertData = [];

        foreach ($apiTypes as $type) {
            // ✅ Unique code handling
            $code = $type->code ?? strtoupper(substr($type->en_name ?? 'POS', 0, 3));
            if (in_array($code, $existingCodes)) {
                $code = $code . '_' . $type->id;
            }
            $existingCodes[] = $code;

            $insertData[] = [
                'id'        => $type->id, // ✅ Keep same ID
                'code'      => $code,
                'en_name'   => $type->en_name ?? 'Unnamed',
                'ar_name'   => $type->ar_name ?? null,
                'created_at'=> now(),
                'updated_at'=> now(),
            ];
        }

        // ✅ Use upsert with ID as key
        DB::table('positions')->upsert($insertData, ['id'], ['code', 'en_name', 'ar_name', 'updated_at']);

        $this->info('✅ Positions imported successfully from api_types with same IDs!');
        $this->line('Total records processed: ' . count($insertData));

        return Command::SUCCESS;
    }
}
