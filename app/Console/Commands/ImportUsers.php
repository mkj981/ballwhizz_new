<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ImportUsers extends Command
{
    protected $signature = 'import:users
                            {--truncate : Truncate the users table before importing}
                            {--limit=0 : Limit number of users to import for testing}';

    protected $description = '👤 Import users from the old database (debug mode: imports one by one and logs errors).';

    public function handle(): int
    {
        $oldConnection = 'old_mysql';
        $limit = (int) $this->option('limit');

        $this->info("🚀 Starting import of users from old database...");

        // ⚙️ Disable strict mode & foreign key checks
        DB::statement("SET SESSION sql_mode='NO_ENGINE_SUBSTITUTION';");
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 🔹 Optionally truncate
        if ($this->option('truncate')) {
            User::truncate();
            $this->warn("⚠️ Truncated users table before import.");
        }

        // 🔹 Fetch users from old DB
        $query = DB::connection($oldConnection)->table('users')->orderBy('id');
        if ($limit > 0) {
            $query->limit($limit);
            $this->info("🔍 Debug mode active — importing only first {$limit} users.");
        }

        $users = $query->get();
        $total = $users->count();

        if ($total === 0) {
            $this->warn("⚠️ No users found in old database.");
            return Command::SUCCESS;
        }

        $this->info("📦 Found {$total} users to import...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        // 🔹 Import one by one for debugging
        foreach ($users as $old) {
            try {
                // ✅ Prepare password
                $password = $old->password;
                if (empty($password) || strlen($password) < 40) {
                    $password = Hash::make('password');
                }

                // ✅ Fallback email if invalid or missing
                $email = trim($old->email ?? '');
                if (empty($email) || $email === '-' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email = "user{$old->id}@ballwhizz.local";
                }

                // ✅ Normalize language
                $lang = is_numeric($old->lang) ? (int)$old->lang : 1;

                // ✅ Default values
                $uid = $old->uid ?? 'bw-' . $old->id;
                $role = $old->role ?? 'user';

                // ✅ Truncate long team strings
                $team = $old->team ?? null;
                if ($team && strlen($team) > 255) {
                    $team = substr($team, 0, 255);
                }

                // ✅ Insert or update user
                DB::connection('mysql')->table('users')->updateOrInsert(
                    ['id' => $old->id],
                    [
                        'name'           => $old->name ?? 'Unknown',
                        'email'          => $email,
                        'password'       => $password,
                        'uid'            => $uid,
                        'social_type'    => $old->social_type ?? null,
                        'mobile'         => $old->mobile ?? null,
                        'country_code'   => $old->country_code ?? null,
                        'team'           => $team,
                        'FCM_token'      => $old->FCM_token ?? null,
                        'lang'           => $lang,
                        'referral_code'  => $old->referral_code ?? null,
                        'comefrom'       => $old->comefrom ?? null,
                        'coins'          => (int) ($old->coins ?? 0),
                        'gem'            => (int) ($old->gem ?? 0),
                        'xp'             => (int) ($old->xp ?? 0),
                        'role'           => $role,
                        'remember_token' => $old->remember_token ?? null,
                        'created_at'     => $old->created_at ?? now(),
                        'updated_at'     => $old->updated_at ?? now(),
                    ]
                );

                $this->line("\n✅ Imported user #{$old->id} ({$old->name})");
            } catch (\Throwable $e) {
                $this->error("\n❌ Error importing user #{$old->id} ({$old->name}): " . $e->getMessage());
            }

            $bar->advance();
        }

        // ✅ Re-enable FK checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $bar->finish();

        $this->newLine(2);
        $this->info("✅ User import process completed!");
        return Command::SUCCESS;
    }
}
