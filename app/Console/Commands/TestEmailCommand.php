<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test {email}';
    protected $description = 'Send a test email to verify SMTP configuration';

    public function handle()
    {
        $email = $this->argument('email');

        try {
            Mail::raw('This is a test email from STA application. If you receive this, your SMTP configuration is working correctly!', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email from STA');
            });

            $this->info("Test email sent successfully to {$email}!");
            $this->info('Please check the inbox (and spam folder) to confirm delivery.');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to send test email!');
            $this->error('Error: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
