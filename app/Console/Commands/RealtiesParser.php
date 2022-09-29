<?php

namespace App\Console\Commands;

use App\Models\CatalogAd;
use App\Models\CatalogAdCategory;
use App\Models\CatalogParameter;
use App\Models\JobsResume;
use App\Models\JobsResumeCategory;
use App\Models\JobsVacancy;
use App\Models\Profile;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Objects\Education\Constants\Education;
use App\Objects\Files;
use App\Objects\SalaryType\Constants\SalaryType;
use App\Objects\Schedule\Constants\Schedule;
use App\Objects\Time\Constants\TimeArray;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RealtiesParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'realty-parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'realty-parse';

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

    }
}
