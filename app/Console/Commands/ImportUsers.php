<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *   php artisan import:users
     *
     * Optional:
     *   php artisan import:users --truncate
     */
    protected $signature = 'import:users {--truncate : Truncate the users table before importing}';

    /**
     * The console command description.
     */
    protected $description = '👤 Import users from the old database into the new users table.';

    public function handle(): int
    {
        $oldConnection = 'old_mysql'; // ⚙️ must be defined in config/database.php

        $this->info("🚀 Starting import of users from old database...");

        if ($this->option('truncate')) {
            User::truncate();
            $this->warn("⚠️ Cleared existing users table before import.");
        }

        // Count total first
        $query = DB::connection($oldConnection)->table('users');
        $total = $query->count();

        if ($total === 0) {
            $this->warn("⚠️ No users found in old database.");
            return Command::SUCCESS;
        }

        $this->info("📦 Found {$total} users to import.");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        // ✅ Process users in chunks to save memory
        $query->orderBy('id')->chunk(1000, function ($chunk) use ($bar) {
            $insertData = [];

            foreach ($chunk as $old) {
                $insertData[] = [
                    'id'             => $old->id,
                    'name'           => $old->name ?? 'Unknown',
                    'email'          => $old->email ?? null,
                    'password'       => $old->password ?? bcrypt('password'),
                    'uid'            => $old->uid ?? null,
                    'social_type'    => $old->social_type ?? null,
                    'mobile'         => $old->mobile ?? null,
                    'country_code'   => $old->country_code ?? null,
                    'team'           => $old->team ?? null,
                    'FCM_token'      => $old->FCM_token ?? null,
                    'lang'           => $old->lang ?? 'en',
                    'referral_code'  => $old->referral_code ?? null,
                    'comefrom'       => $old->comefrom ?? null,
                    'coins'          => $old->coins ?? 0,
                    'gem'            => $old->gem ?? 0,
                    'xp'             => $old->xp ?? 0,
                    'role'           => $old->role ?? 'user',
                    'remember_token' => $old->remember_token ?? null,
                    'created_at'     => $old->created_at ?? now(),
                    'updated_at'     => $old->updated_at ?? now(),
                ];

                $bar->advance();
            }

            // ✅ Insert batch (ignore duplicates)
            User::upsert(
                $insertData,
                ['id'],
                [
                    'name',
                    'email',
                    'password',
                    'uid',
                    'social_type',
                    'mobile',
                    'country_code',
                    'team',
                    'FCM_token',
                    'lang',
                    'referral_code',
                    'comefrom',
                    'coins',
                    'gem',
                    'xp',
                    'role',
                    'remember_token',
                    'updated_at'
                ]
            );
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Users import completed successfully!");

        return Command::SUCCESS;
    }
}
