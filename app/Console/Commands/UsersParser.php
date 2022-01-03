<?php

namespace App\Console\Commands;

use App\Models\JobsResume;
use App\Models\JobsVacancy;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Objects\Education\Constants\Education;
use App\Objects\SalaryType\Constants\SalaryType;
use App\Objects\Time\Constants\TimeArray;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UsersParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'user-parse';

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
//        $client = new Client();
//        $response = $client->get('https://user.tapigo.ru/all-users-json', ['verify' => false]);
//        $contents = $response->getBody()->getContents();
//        $contents = json_decode($contents, true);
//        foreach ($contents as $item) {
//            $userDB = User::find($item['id']);
//            if (isset($userDB)) {
//                $user = $userDB;
//                $user->id = $item['id'];
//                $user->email = $item['email'];
//                $user->password = $item['password'];
//                $user->phone = $item['phone'];
//                $user->email_verified_at = $item['email_verified_at'];
//                $user->name = $item['profile'] ? $item['profile']['name'] : null;
//                $user->update();
//            } else {
//                $count = User::where('email', $item['email'])
//                    ->orWhere('phone', $item['phone'])
//                    ->get();
//
//                if ($count->count() === 0) {
//                    $user = new User();
//                    $user->id = $item['id'];
//                    $user->email = $item['email'];
//                    $user->password = $item['password'];
//                    $user->phone = $item['phone'];
//                    $user->email_verified_at = $item['email_verified_at'];
//                    $user->name = $item['profile'] ? $item['profile']['name'] : null;
//                    $user->save();
//                    $user->profile()->create(['id' => $item['profile']['id']]);
//                }
//            }
//        }
//
//        $client = new Client();
//        $response = $client->get('https://catalog.tapigo.ru/all-resume-json', ['verify' => false]);
//        $contents = $response->getBody()->getContents();
//        $contents = json_decode($contents, true);
//
//        foreach ($contents as $item) {
//            $userDB = User::find($item['id']);
//            if(!isset($userDB)) {
//                continue;
//            }
//            $profileID = $userDB->profile->getKey();
//            if (isset($item['posts']) && !empty($item['posts'])) {
//                foreach ($item['posts'] as $post) {
//                    if (isset($post['property']) && !empty($post['property'])) {
//                        if ($post['property_type'] === "App\\Models\\Jobs\\Resume\\JobsPostResumeProperty") {
//                            $property = $post['property'];
//                            $resume = JobsResume::find($property['id']);
//                            $slug = Str::slug($property['title'] .  ' ' . Str::random(5), '-');
//                            if ($resume) {
//                                $model = $resume;
//                            } else {
//                                $model = new JobsResume();
//                            }
//                            $model->id = $property['id'];
//                            $model->price = $property['price'];
//                            $model->description = $property['desc'];
//                            $model->title = $property['title'];
//                            $model->experience = (new TimeArray($property['experience_years'], null))->parce();
//                            $model->education = (new Education($property['education'], null))->parce();
//                            $model->schedule = (new Education($property['schedule'], null))->parce();
//                            $model->salary_type = (new SalaryType($property['salary_type'] ?? null, null))->parce();
//                            $model->alias = $slug;
//                            $model->active = true;
//                            $model->profile_id = $profileID;
//
//                            if ($resume) {
//                                $model->update();
//                            } else {
//                                $model->save();
//                            }
//                        }
//                    }
//                    if ($post['property_type'] === "App\\Models\\Jobs\\Vacancy\\JobsPostVacancyProperty") {
//                        $property = $post['property'];
//                        $resume = JobsVacancy::find($property['id']);
//                        $slug = Str::slug($property['title'] .  ' ' . Str::random(5), '-');
//                        if ($resume) {
//                            $model = $resume;
//                        } else {
//                            $model = new JobsVacancy();
//                        }
//                        $model->id = $property['id'];
//                        $model->min_price = $property['price'];
//                        $model->max_price = $property['price_max'];
//                        $model->description = $property['desc'];
//                        $model->title = $property['title'];
//                        $model->phone = $property['telefon'];
//                        $model->duties = $property['duties'];
//                        $model->demands = $property['demands'];
//                        $model->additionally = $property['additionally'];
//                        $model->experience = (new TimeArray($property['experience_years'], null))->parce();
//                        $model->education = (new Education($property['education'], null))->parce();
//                        $model->schedule = (new Education($property['schedule'], null))->parce();
//                        $model->salary_type = (new SalaryType($property['salary_type'] ?? null, null))->parce();
//                        $model->alias = $slug;
//                        $model->active = true;
//                        $model->profile_id = $profileID;
//                        if ($resume) {
//                            $model->update();
//                        } else {
//                            $model->save();
//                        }
//                    }
//                }
//            }
//        }



        $client = new Client();
        $response = $client->get('https://catalog.tapigo.ru/all-uslugi-json2', ['verify' => false]);
        $contents = $response->getBody()->getContents();
        $contents = json_decode($contents, true);
        foreach ($contents as $item) {
            $userDB = User::find($item['id']);
            if (isset($userDB)) {
                foreach ($item['uslugi'] as $relation) {
                        $alias = Str::slug(Str::limit($relation['property']['desc'], 10) . ' ' . str_random(5), '-');
                        $isModel = Service::find($relation['property']['id']);
                    if (isset($relation['cats'])) {
                        $cats = ServiceCategory::where('name', $relation['cats']['title'])->first();
                    } else {
                        $cats = null;
                    }
                        $model = $isModel ?? new Service();
                        $model->id = $relation['property']['id'];
                        $model->profile_id = $userDB->profile->getKey();
                        $model->title = $relation['property']['title'];
                        $model->contract = $relation['property']['contract'];
                        $model->guarantee = $relation['property']['guarantee'];
                        $model->consultation = $relation['property']['consultation'];
                        $model->hourly_payment = $relation['property']['hourly_payment'];
                        $model->category_id = isset($cats) ? $cats->id : null;
                        $model->alias = $alias;
                        $model->description = $relation['property']['desc'];
                        $model->price = (int) str_replace(' ', '', $relation['property']['price']);
                        $isModel ? $model->update() : $model->save();
                }
            }
        }

//        dd($contents);
        return 1;
    }
}
