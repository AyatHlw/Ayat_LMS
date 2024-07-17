<?php

namespace App\Console\Commands;

use App\Models\PremiumUsers;
use Illuminate\Console\Command;

class DeletePremiumUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-premium-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        PremiumUsers::query()->where('end_date', '<', now())->delete();
    }
}
