<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\JobsResume;
use App\Models\JobsVacancy;
use App\Models\Service;
use Carbon\Exceptions\InvalidTimeZoneException;
use Illuminate\Console\Command;

class SyncCitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $vacancies = JobsVacancy::withTrashed()->where('city_id', null)->get();
        $city = City::where('alias', 'perm')->first();
        $vacancies->each(function ($vacancy) use ($city) {
            $vacancy->city_id =  $city->getKey();
            $vacancy->update();
        });

        $resumes = JobsResume::withTrashed()->where('city_id', null)->get();
        $resumes->each(function ($resume) use ($city) {
            $resume->city_id =  $city->getKey();
            $resume->update();
        });

        $services = Service::withTrashed()->where('city_id', null)->get();
        $services->each(function ($service) use ($city) {
            $service->city_id =  $city->getKey();
            $service->update();
        });
    }
}
