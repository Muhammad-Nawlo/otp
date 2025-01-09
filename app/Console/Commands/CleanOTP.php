<?php

namespace App\Console\Commands;

use App\Models\Otp;
use Illuminate\Console\Command;

class CleanOTP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean Otp database, remove all old otps that is expired or used.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $otps = Otp::where('valid', 0)->delete();

            $this->info("Found {$otps} expired otps.");
            $this->info($otps ? "Expired tokens deleted" : "No tokens were deleted");
        } catch (\Exception $e) {
            $this->error("Error:: {$e->getMessage()}");
        }

        return 0;
    }
}
