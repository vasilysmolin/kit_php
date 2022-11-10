<?php

namespace App\Console\Commands;

use App\Mail\ErrorMail;
use App\Models\Feed;
use App\Services\ImportFeedService;
use Doctrine\DBAL\ConnectionException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RealtyImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'realty-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $importFeedService = resolve(ImportFeedService::class);
        Feed::query()->has('profile')->chunk(100, function($feeds) use ($importFeedService) {
            $feeds->each(function($feed) use ($importFeedService) {
                try{
                    $importFeedService->import($feed, $feed->profile);
                } catch(\Exception | ConnectionException | RequestException $exception) {
                    if (config('app.env') === 'production') {
                        $dataErrors = collect([
                            'user' => $feed->profile->getKey() ?? null,
                            'code' => $exception->getCode(),
                            'getTraceAsString' => $exception->getTraceAsString(),
                            'getMessage' => $exception->getMessage(),
                        ]);
                        Mail::to(config('app.mail_errors'))
                            ->cc(config('app.mail_errors_tapigo'))
                            ->queue(new ErrorMail($dataErrors, 'errorCommand'));
                    }
                }

            });

        });
        return 0;
    }
}
