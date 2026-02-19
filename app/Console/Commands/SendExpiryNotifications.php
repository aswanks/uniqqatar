<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\RegistrationController; // change this to your actual controller name

class SendExpiryNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:send-expiry';

    /**
     * The console command description.
     */
    protected $description = 'Send membership expiry notifications to users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = app()->call([RegistrationController::class, 'sendNotification']);
        $this->info('Response: ' . print_r($response, true));
        $this->info('✅ Expiry notifications processed successfully.');
    }
}