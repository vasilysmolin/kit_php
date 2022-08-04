<?php

namespace App\Console\Commands;

use App\Mail\InvalidSslMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Spatie\SslCertificate\SslCertificate;

class CheckSslCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-ssl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check ssl';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $certificate = SslCertificate::createForHostName(config('app.domain'));
        if (!$certificate->isValidUntil(Carbon::now()->addDays(7))) {

                Mail::to(config('app.mail_errors'))
                    ->cc(config('app.mail_errors_tapigo'))
                    ->queue(new InvalidSslMail($certificate));
        }
    }
}
