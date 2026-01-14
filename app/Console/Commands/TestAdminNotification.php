<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\Booking;
use App\Notifications\AdminNewBookingNotification;

class TestAdminNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:admin-notification {--booking-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test admin FCM notification with detailed logging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Admin FCM Notification');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        // Step 1: Check admins with tokens
        $this->info('📋 Step 1: Checking admins with FCM tokens...');
        $admins = Admin::whereNotNull('fcm_token')->get();
        
        if ($admins->isEmpty()) {
            $this->error('❌ No admins found with FCM tokens!');
            $this->info('Total admins in DB: ' . Admin::count());
            return 1;
        }
        
        $this->info("✅ Found {$admins->count()} admin(s) with FCM tokens:");
        foreach ($admins as $admin) {
            $tokenPreview = substr($admin->fcm_token, 0, 20) . '...';
            $this->line("   • {$admin->email} (ID: {$admin->id}) - Token: {$tokenPreview}");
        }
        $this->newLine();
        
        // Step 2: Get or select booking
        $this->info('📋 Step 2: Getting booking...');
        $bookingId = $this->option('booking-id');
        
        if ($bookingId) {
            $booking = Booking::find($bookingId);
            if (!$booking) {
                $this->error("❌ Booking ID {$bookingId} not found!");
                return 1;
            }
        } else {
            $booking = Booking::latest()->first();
            if (!$booking) {
                $this->error('❌ No bookings found in database!');
                return 1;
            }
        }
        
        $this->info("✅ Using booking ID: {$booking->id}");
        $this->line("   Order Number: {$booking->order_number}");
        $this->line("   Status: {$booking->status}");
        $this->newLine();
        
        // Step 3: Check queue configuration
        $this->info('📋 Step 3: Checking queue configuration...');
        $queueDriver = config('queue.default');
        $this->line("   Queue Driver: {$queueDriver}");
        
        if ($queueDriver === 'sync') {
            $this->warn('⚠️  Queue driver is "sync" - notifications will be sent immediately');
        } else {
            $this->info("ℹ️  Queue driver is '{$queueDriver}' - make sure queue worker is running:");
            $this->line('   Command: php artisan queue:work');
        }
        $this->newLine();
        
        // Step 4: Check Firebase configuration
        $this->info('📋 Step 4: Checking Firebase configuration...');
        $credentialsPath = env('FIREBASE_CREDENTIALS');
        
        if (!$credentialsPath) {
            $this->error('❌ FIREBASE_CREDENTIALS not set in .env!');
            return 1;
        }
        
        if (!file_exists($credentialsPath)) {
            $this->error("❌ Firebase credentials file not found at: {$credentialsPath}");
            return 1;
        }
        
        $this->info("✅ Firebase credentials file exists");
        $this->line("   Path: {$credentialsPath}");
        $this->newLine();
        
        // Step 5: Send test notification
        $this->info('📋 Step 5: Sending test notification...');
        $this->info('This will trigger all the logging we added.');
        $this->newLine();
        
        try {
            $notification = new AdminNewBookingNotification($booking);
            
            foreach ($admins as $admin) {
                $this->line("   Sending to: {$admin->email}...");
                $admin->notify($notification);
            }
            
            $this->info('✅ Notification queued/sent successfully!');
            $this->newLine();
            
        } catch (\Exception $e) {
            $this->error('❌ Exception occurred:');
            $this->error('   ' . $e->getMessage());
            $this->newLine();
            $this->error('Stack trace:');
            $this->line($e->getTraceAsString());
            return 1;
        }
        
        // Step 6: Instructions
        $this->info('📋 Step 6: Next steps');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        if ($queueDriver === 'sync') {
            $this->line('1. Check Laravel logs for detailed output:');
            $this->line('   tail -f storage/logs/laravel.log');
        } else {
            $this->line('1. Make sure queue worker is running:');
            $this->line('   php artisan queue:work');
            $this->line('');
            $this->line('2. Check Laravel logs for detailed output:');
            $this->line('   tail -f storage/logs/laravel.log');
        }
        
        $this->line('');
        $this->line('3. Look for these log entries:');
        $this->line('   - "BookingObserver: created event triggered"');
        $this->line('   - "FirebaseChannel: send() called"');
        $this->line('   - "FirebaseChannel: Firebase::messaging()->send() completed"');
        
        $this->line('');
        $this->line('4. Check admin device for push notification');
        
        $this->newLine();
        $this->info('✅ Test complete! Check the logs above for any errors.');
        
        return 0;
    }
}
