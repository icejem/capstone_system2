<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UnsuspendExpiredUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:unsuspend-expired
                          {--force : Force unsuspend even if suspension might not have fully expired}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Automatically unsuspend users whose suspension period has expired';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $now = Carbon::now('Asia/Manila');

            // Find all suspended users whose suspension has expired
            $expiredUsers = User::where('account_status', 'suspended')
                ->whereNotNull('suspension_expires_at')
                ->where('suspension_expires_at', '<=', $now)
                ->get();

            if ($expiredUsers->isEmpty()) {
                $this->info('No expired suspensions to process.');
                return self::SUCCESS;
            }

            $count = 0;
            foreach ($expiredUsers as $user) {
                $user->update([
                    'account_status' => 'active',
                    'suspension_expires_at' => null,
                    'suspension_reason' => null,
                ]);

                $this->line("✓ Unsuspended user: {$user->name} ({$user->email})");
                $count++;
            }

            $this->info("Successfully unsuspended {$count} user(s).");
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error processing expired suspensions: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
