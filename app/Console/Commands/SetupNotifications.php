<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SetupNotifications extends Command
{
    protected $signature = 'setup:notifications {--enable-all}';
    protected $description = 'Setup and enable notification preferences for all parents';

    public function handle()
    {
        $this->info('🔧 Setting up notification system...');
        
        // Step 1: Enable notifications for all parents
        $parentsCount = User::where('role', 'orang_tua')->count();
        
        $this->info("📧 Found {$parentsCount} parents in the system");
        
        $updated = User::where('role', 'orang_tua')->update([
            'notify_email' => true,
            'notify_app' => true,
        ]);
        
        $this->info("✅ Enabled notifications for {$updated} parents");
        
        // Step 2: Show email configuration status
        $this->newLine();
        $this->info('📧 Email Configuration Status:');
        $this->line('  Mailer: ' . config('mail.default'));
        $this->line('  Host: ' . config('mail.mailers.smtp.host'));
        $this->line('  Port: ' . config('mail.mailers.smtp.port'));
        
        if (config('mail.default') === 'log') {
            $this->warn('⚠️  Mail is still configured to log to file!');
            $this->info('    Update .env: MAIL_MAILER=smtp');
            $this->info('    Add MAIL_USERNAME and MAIL_PASSWORD');
        }
        
        // Step 3: Check queue configuration
        $this->newLine();
        $this->info('📨 Queue Configuration:');
        $this->line('  Connection: ' . config('queue.default'));
        
        if (config('queue.default') === 'database') {
            $this->info('✅ Queue is configured to use database');
            $this->info('    To process notifications, run: php artisan queue:work');
        }
        
        // Step 4: Database check
        $this->newLine();
        $this->info('📊 Database Status:');
        
        $parentsWithPhone = User::where('role', 'orang_tua')
            ->whereNotNull('phone_number')
            ->count();
        $parentsWithEmail = User::where('role', 'orang_tua')
            ->whereNotNull('email')
            ->count();
            
        $this->line("  Parents with phone number: {$parentsWithPhone}");
        $this->line("  Parents with email: {$parentsWithEmail}");
        
        $this->newLine();
        $this->info('✅ Setup complete!');
        $this->info('');
        $this->info('Next steps:');
        $this->line('1. Update .env with Gmail SMTP credentials:');
        $this->line('   - MAIL_USERNAME=your_gmail@gmail.com');
        $this->line('   - MAIL_PASSWORD=your_app_password (not regular password)');
        
        return 0;
    }
}
