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
//        Storage::disk('local')->put('all-users-json.txt', $contents);
//        $contents = json_decode($contents, true);
//        foreach ($contents as $item) {
//            $userDB = User::find($item['id']);
//            if (isset($userDB)) {
//                $user = $userDB;
//                $user->id = $item['id'];
//                $user->email = Str::lower($item['email']);
//                $user->password = $item['password'];
//                $user->phone = $item['phone'];
//                $user->email_verified_at = $item['email_verified_at'];
//                $user->name = $item['profile'] ? $item['profile']['name'] : null;
//                $user->update();
//            } else {
//                $count = User::where('email', $item['email'])
////                    ->orWhere('phone', $item['phone'])
//                    ->get();
////                if($item['id'] == 3109) {
////                    var_dump($user);
////                    dd(1);
////                }
//                if ($count->count() === 0) {
//                    $user = new User();
//                    $user->id = $item['id'];
//                    $user->email = $item['email'];
//                    $user->password = $item['password'];
//                    $user->phone = $item['phone'];
//                    $user->email_verified_at = $item['email_verified_at'];
//                    $user->name = $item['profile'] ? $item['profile']['name'] : null;
//                    $user->save();
//                }
//            }
////              if($item['id'] == 3109) {
////                  $profileID = $item['profile'] ? $item['profile']['id'] : null;
////                    var_dump(isset($user) && empty($user->profile));
////                  $isProfile = Profile::find($profileID);
////                    dd($isProfile);
////                }
//            if (isset($user) && empty($user->profile)) {
//                $profileID = $item['profile'] ? $item['profile']['id'] : null;
//                $isProfile = Profile::find($profileID);
//                if (!isset($isProfile)) {
//                    if ($profileID !== null) {
//                        $profile = new Profile();
//                        $profile->id = $profileID;
//                        $profile->user_id = $user->getKey();
//                        $profile->save();
//                    }
//                }
//            }
//        }
//
//        $client = new Client();
//        $response = $client->get('https://catalog.tapigo.ru/all-category-declarations-json', ['verify' => false]);
//        $contents = $response->getBody()->getContents();
//        Storage::disk('local')->put('all-category-ads-json.txt', $contents);
//        $contents = Storage::disk('local')->get('all-category-ads-json.txt');
//        $contents = json_decode($contents, true);
//        $i = 1;
//        foreach ($contents as $item) {
//                $isModel = CatalogAdCategory::find($item['id']);
//                $meta = $isModel ?? new CatalogAdCategory();
//                $meta->id = $item['id'];
//                $meta->name = $item['title'];
//                var_dump('22 ' . $item['title']);
//
//                $meta->parent_id = ($item['parent_id'] === 0) ? null : $item['parent_id'];
//                $meta->alias = $item['slug']  . '_' . Str::random(5);
//                $meta->active = 1;
//                $isModel ? $meta->update() : $meta->save();
//        }
//        $rooms = CatalogParameter::where('value', '1 комнатные')->first();

//        $client = new Client();
//        $response = $client->get('https://catalog.tapigo.tech/all-ads-comfort-json', [
//            'verify' => false,
//            'auth' => [
//                'ktotam',
//                'eto_tapigo',
//            ],
//        ]);
//        $contentsComfort = $response->getBody()->getContents();
//        $contentsComfort = collect(json_decode($contentsComfort, true));


        $client = new Client();
        $response = $client->get('https://catalog.tapigo.tech/all-ads-json', [
            'verify' => false,
            'auth' => [
                'ktotam',
                'eto_tapigo',
            ],
        ]);
        $contents = $response->getBody()->getContents();
//        Storage::disk('local')->put('all-ads-json.txt', $contents);
//        App\\Models\\Catalog\\Flat\\AdFlatProperty
//Инпуты
//"total_area": "18", общая объект Area от 1 до 100
//"kitchen_area": "3", кухня
//"living_area": "14", жилая
//"ad_flat_type_id": null,
//"ad_flat_type_novelty_id": 1, 1 вторичка/2 новостройка
//"ad_flat_type_seller_id": 1, 1 собственник/2 посредник
//"ad_flat_type_building_id": 2, 1 панельный 2 кирпичный 3 деревянный 4 шлакоблоки
//"floor": "1", 1 этаж объект Area от 1 до 100
//"floors_in_house": "5", из 5 объект Floofs от 1 до 20
//"rooms": "1" - комнат объект Rooms от 1 до 7


//        $contents = json_decode($contents, true);
//        $i = 1;
//        foreach ($contents as $item) {
//            $userDB = User::find($item['id']);
//            if (isset($userDB)  || isset($userDB->profile)) {
//                foreach ($item['ads'] as $relation) {
//                    $next = true;
//                    if ($relation['property_type'] === "App\\Models\\Catalog\\Flat\\AdFlatProperty") {
//                        $next = false;
//                        $slug = '-bye';
//                    }
//                    if ($relation['property_type'] === "App\\Models\\Catalog\\Flat\\AdFlatRentProperty") {
//                        $next = false;
//                        $slug = '-rent';
//                    }
//                    if ($next === true) {
//                        continue;
//                    }
//                    if ((int) $relation['property']['rooms'] === 0) {
//                        $relation['property']['rooms'] = 1;
//                    }
//                    if ((int) $relation['property']['rooms'] > 5) {
//                        $relation['property']['rooms'] = 5;
//                    }
//                    $title = $relation['property']['rooms'] . ' комнатная';
//                    $alias = Str::slug(Str::limit($title, 10) . ' ' . str_random(7), '-');
////                    $isModel = CatalogAd::find($relation['category']['id']);
//                    if (isset($relation['category'])) {
//                        $cats = CatalogAdCategory::find($relation['category']['id']);
//                    } else {
//                        $cats = null;
//                    }
////                    $isModel = CatalogAd::find($relation['id']);
//
//                    $model = new CatalogAd();
//                    if (isset($relation['location']) && isset($relation['location']['lat']) && $relation['location']['lat'] != 0) {
//                        $model->latitude = $relation['location']['lat'];
//                        $model->longitude = $relation['location']['lng'];
//                        $model->street = $relation['location']['street'];
//                        $model->house = $relation['location']['house'];
//                    }
////                    $model->id = $relation['id'];
//                    $model->profile_id = $userDB->profile->getKey();
//                    $model->name = $title;
//                    $model->description = trim($relation['property']['desc']);
//                    $model->category_id = isset($cats) ? $cats->id : null;
//                    $model->alias = $alias;
//                    $model->state = 'active';
//                    $model->price = (int) str_replace(' ', '', $relation['property']['price']);
//                    $model->sale_price = (int) str_replace(' ', '', $relation['property']['price']);
//                    $model->save();
//                    $model->moveToStart();
//                    $string = $title;
//
//                    $rooms = CatalogParameter::where('value', $string)->first();
//                    $livingArea = CatalogParameter::where('value', $relation['property']['living_area'])->whereHas('filter', function ($q) use ($slug) {
//                        $q->where('alias', 'zilaya-ploshhad'  . $slug);
//                    })->first();
//                    $kitArea = CatalogParameter::where('value', $relation['property']['kitchen_area'])->whereHas('filter', function ($q) use ($slug) {
//                        $q->where('alias', 'ploshhad-kuxni'  . $slug);
//                    })->first();
//                    $totalArea = CatalogParameter::where('value', $relation['property']['total_area'])->whereHas('filter', function ($q) use ($slug) {
//                        $q->where('alias', 'obshhaya-ploshhad'  . $slug);
//                    })->first();
//                    $floorsInHouse = CatalogParameter::where('value', $relation['property']['floors_in_house'])->whereHas('filter', function ($q) use ($slug) {
//                        $q->where('alias', 'vsego-etazei'  . $slug);
//                    })->first();
//                    $floor = CatalogParameter::where('value', $relation['property']['floor'])->whereHas('filter', function ($q) use ($slug) {
//                        $q->where('alias', 'etaz'  . $slug);
//                    })->first();
//                    if ($relation['property']['ad_flat_type_building_id'] === 1) {
//                        $dom = CatalogParameter::where('value', 'Панельный')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'dom'  . $slug);
//                        })->first();
//                    } elseif ($relation['property']['ad_flat_type_building_id'] === 2) {
//                        $dom = CatalogParameter::where('value', 'Кирпичный')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'dom'  . $slug);
//                        })->first();
//                    } elseif ($relation['property']['ad_flat_type_building_id'] === 3) {
//                        $dom = CatalogParameter::where('value', 'Деревянный')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'dom'  . $slug);
//                        })->first();
//                    } else {
//                        $dom = CatalogParameter::where('value', 'Шлакоблоки')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'dom'  . $slug);
//                        })->first();
//                    }
//
//                    if ($relation['property']['ad_flat_type_seller_id'] === 1) {
//                        $seller = CatalogParameter::where('value', 'Собственник')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'prodavec'  . $slug);
//                        })->first();
//                    } else {
//                        $seller = CatalogParameter::where('value', 'Посредник')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'prodavec'  . $slug);
//                        })->first();
//                    }
//
//                    if ($relation['property']['ad_flat_type_novelty_id'] === 1) {
//                        $novizna = CatalogParameter::where('value', 'Вторичка')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'novizna'  . $slug);
//                        })->first();
//                    } else {
//                        $novizna = CatalogParameter::where('value', 'Новостройка')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'novizna'  . $slug);
//                        })->first();
//                    }
//
//                    $phone = $contentsComfort->where('name', 'Телефон')->first();
//                    $phoneCollect = collect($phone['flats']);
//                    $myObj = $phoneCollect->where('id', $relation['property']['id'])->first();
//                    if (!empty($myObj)) {
//                        $comfortPhone = CatalogParameter::where('value', 'Телефон')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'udobstva'  . $slug);
//                        })->first();
//                    } else {
//                        $comfortPhone = null;
//                    }
//
//                    $net = $contentsComfort->where('name', 'Интернет')->first();
//                    $netCollect = collect($net['flats']);
//                    $myObj = $netCollect->where('id', $relation['property']['id'])->first();
//                    if (!empty($myObj)) {
//                        $comfortNet = CatalogParameter::where('value', 'Интернет')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'udobstva'  . $slug);
//                        })->first();
//                    } else {
//                        $comfortNet = null;
//                    }
//
//                    $park = $contentsComfort->where('name', 'Парковка')->first();
//                    $parkCollect = collect($park['flats']);
//                    $myObj = $parkCollect->where('id', $relation['id'])->first();
//                    if (!empty($myObj)) {
//                        $comfortPark = CatalogParameter::where('value', 'Парковка')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'udobstva'  . $slug);
//                        })->first();
//                    } else {
//                        $comfortPark = null;
//                    }
//
//                    $twoLift = $contentsComfort->where('name', 'Два лифта')->first();
//                    $twoLiftCollect = collect($twoLift['flats']);
//                    $myObj = $twoLiftCollect->where('id', $relation['id'])->first();
//                    if (!empty($myObj)) {
//                        $comfortTwoLift = CatalogParameter::where('value', 'Два лифта')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'udobstva'  . $slug);
//                        })->first();
//                    } else {
//                        $comfortTwoLift = null;
//                    }
//
//                    $conseg = $contentsComfort->where('name', 'Консьерж')->first();
//                    $consegCollect = collect($conseg['flats']);
//                    $myObj = $consegCollect->where('id', $relation['id'])->first();
//                    if (!empty($myObj)) {
//                        $comfortCons = CatalogParameter::where('value', 'Консьерж')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'udobstva'  . $slug);
//                        })->first();
//                    } else {
//                        $comfortCons = null;
//                    }
//
//                    $balkon = $contentsComfort->where('name', 'Балкон')->first();
//                    $balkonCollect = collect($balkon['flats']);
//                    $myObj = $balkonCollect->where('id', $relation['id'])->first();
//                    if (!empty($myObj)) {
//                        $comfortBal = CatalogParameter::where('value', 'Балкон')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'udobstva'  . $slug);
//                        })->first();
//                    } else {
//                        $comfortBal = null;
//                    }
//
//
//
//                    $arr = collect();
//                    if (!empty($comfortBal)) {
//                        $arr->add($comfortBal->getKey());
//                    }
//                    if (!empty($comfortTwoLift)) {
//                        $arr->add($comfortTwoLift->getKey());
//                    }
//                    if (!empty($comfortCons)) {
//                        $arr->add($comfortCons->getKey());
//                    }
//                    if (!empty($comfortPhone)) {
//                        $arr->add($comfortPhone->getKey());
//                    }
//                    if (!empty($comfortPark)) {
//                        $arr->add($comfortPark->getKey());
//                    }
//                    if (!empty($comfortNet)) {
//                        $arr->add($comfortNet->getKey());
//                    }
//                    if (!empty($rooms)) {
//                        $arr->add($rooms->getKey());
//                    }
//                    if (!empty($livingArea)) {
//                        $arr->add($livingArea->getKey());
//                    }
//                    if (!empty($kitArea)) {
//                        $arr->add($kitArea->getKey());
//                    }
//                    if (!empty($totalArea)) {
//                        $arr->add($totalArea->getKey());
//                    }
//                    if (!empty($floorsInHouse)) {
//                        $arr->add($floorsInHouse->getKey());
//                    }
//                    if (!empty($floor)) {
//                        $arr->add($floor->getKey());
//                    }
//                    if (!empty($dom)) {
//                        $arr->add($dom->getKey());
//                    }
//                    if (!empty($seller)) {
//                        $arr->add($seller->getKey());
//                    }
//                    if (!empty($novizna)) {
//                        $arr->add($novizna->getKey());
//                    }
//                    $model->adParameters()->attach($arr);
//                    var_dump($i);
//                    $i += 1;
//
//                    if (!empty($relation['images'])) {
//                        foreach ($relation['images'] as $image) {
//                            $url = 'https://kto_tam:eto_tapigo@catalog.tapigo.tech/images/thumbnails/thumb_' . $image['image_path'];
//                            $files = resolve(Files::class);
//                            $files->saveParser($model, $url);
//                        }
//                    }
//                }
//            }
//        }

        //        $contents = json_decode($contents, true);
//        $i = 1;
//        foreach ($contents as $item) {
//            $userDB = User::find($item['id']);
//            if (isset($userDB)  || isset($userDB->profile)) {
//                foreach ($item['ads'] as $relation) {
//                    $next = true;
//                    if ($relation['property_type'] === "App\\Models\\Catalog\\Flat\\AdFlatProperty") {
//                        $next = false;
//                        $slug = '-bye';
//                    }
//                    if ($relation['property_type'] === "App\\Models\\Catalog\\Flat\\AdFlatRentProperty") {
//                        $next = false;
//                        $slug = '-rent';
//                    }
//                    if ($next === true) {
//                        continue;
//                    }
//                    if ((int) $relation['property']['rooms'] === 0) {
//                        $relation['property']['rooms'] = 1;
//                    }
//                    if ((int) $relation['property']['rooms'] > 5) {
//                        $relation['property']['rooms'] = 5;
//                    }
//                    $title = $relation['property']['rooms'] . ' комнатная';
//                    $alias = Str::slug(Str::limit($title, 10) . ' ' . str_random(7), '-');
////                    $isModel = CatalogAd::find($relation['category']['id']);
//                    if (isset($relation['category'])) {
//                        $cats = CatalogAdCategory::find($relation['category']['id']);
//                    } else {
//                        $cats = null;
//                    }
////                    $isModel = CatalogAd::find($relation['id']);
//
//                    $model = new CatalogAd();
//                    if (isset($relation['location']) && isset($relation['location']['lat']) && $relation['location']['lat'] != 0) {
//                        $model->latitude = $relation['location']['lat'];
//                        $model->longitude = $relation['location']['lng'];
//                        $model->street = $relation['location']['street'];
//                        $model->house = $relation['location']['house'];
//                    }
////                    $model->id = $relation['id'];
//                    $model->profile_id = $userDB->profile->getKey();
//                    $model->name = $title;
//                    $model->description = trim($relation['property']['desc']);
//                    $model->category_id = isset($cats) ? $cats->id : null;
//                    $model->alias = $alias;
//                    $model->state = 'active';
//                    $model->price = (int) str_replace(' ', '', $relation['property']['price']);
//                    $model->sale_price = (int) str_replace(' ', '', $relation['property']['price']);
//                    $model->save();
//                    $model->moveToStart();
//                    $string = $title;
//
//                    $rooms = CatalogParameter::where('value', $string)->first();
//                    $livingArea = CatalogParameter::where('value', $relation['property']['living_area'])->whereHas('filter', function ($q) use ($slug) {
//                        $q->where('alias', 'zilaya-ploshhad'  . $slug);
//                    })->first();
//                    $kitArea = CatalogParameter::where('value', $relation['property']['kitchen_area'])->whereHas('filter', function ($q) use ($slug) {
//                        $q->where('alias', 'ploshhad-kuxni'  . $slug);
//                    })->first();
//                    $totalArea = CatalogParameter::where('value', $relation['property']['total_area'])->whereHas('filter', function ($q) use ($slug) {
//                        $q->where('alias', 'obshhaya-ploshhad'  . $slug);
//                    })->first();
//                    $floorsInHouse = CatalogParameter::where('value', $relation['property']['floors_in_house'])->whereHas('filter', function ($q) use ($slug) {
//                        $q->where('alias', 'vsego-etazei'  . $slug);
//                    })->first();
//                    $floor = CatalogParameter::where('value', $relation['property']['floor'])->whereHas('filter', function ($q) use ($slug) {
//                        $q->where('alias', 'etaz'  . $slug);
//                    })->first();
//                    if ($relation['property']['ad_flat_type_building_id'] === 1) {
//                        $dom = CatalogParameter::where('value', 'Панельный')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'dom'  . $slug);
//                        })->first();
//                    } elseif ($relation['property']['ad_flat_type_building_id'] === 2) {
//                        $dom = CatalogParameter::where('value', 'Кирпичный')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'dom'  . $slug);
//                        })->first();
//                    } elseif ($relation['property']['ad_flat_type_building_id'] === 3) {
//                        $dom = CatalogParameter::where('value', 'Деревянный')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'dom'  . $slug);
//                        })->first();
//                    } else {
//                        $dom = CatalogParameter::where('value', 'Шлакоблоки')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'dom'  . $slug);
//                        })->first();
//                    }
//
//                    if ($relation['property']['ad_flat_type_seller_id'] === 1) {
//                        $seller = CatalogParameter::where('value', 'Собственник')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'prodavec'  . $slug);
//                        })->first();
//                    } else {
//                        $seller = CatalogParameter::where('value', 'Посредник')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'prodavec'  . $slug);
//                        })->first();
//                    }
//
//                    if ($relation['property']['ad_flat_type_novelty_id'] === 1) {
//                        $novizna = CatalogParameter::where('value', 'Вторичка')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'novizna'  . $slug);
//                        })->first();
//                    } else {
//                        $novizna = CatalogParameter::where('value', 'Новостройка')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'novizna'  . $slug);
//                        })->first();
//                    }
//
//                    $phone = $contentsComfort->where('name', 'Телефон')->first();
//                    $phoneCollect = collect($phone['flats']);
//                    $myObj = $phoneCollect->where('id', $relation['property']['id'])->first();
//                    if (!empty($myObj)) {
//                        $comfortPhone = CatalogParameter::where('value', 'Телефон')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'udobstva'  . $slug);
//                        })->first();
//                    } else {
//                        $comfortPhone = null;
//                    }
//
//                    $net = $contentsComfort->where('name', 'Интернет')->first();
//                    $netCollect = collect($net['flats']);
//                    $myObj = $netCollect->where('id', $relation['property']['id'])->first();
//                    if (!empty($myObj)) {
//                        $comfortNet = CatalogParameter::where('value', 'Интернет')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'udobstva'  . $slug);
//                        })->first();
//                    } else {
//                        $comfortNet = null;
//                    }
//
//                    $park = $contentsComfort->where('name', 'Парковка')->first();
//                    $parkCollect = collect($park['flats']);
//                    $myObj = $parkCollect->where('id', $relation['id'])->first();
//                    if (!empty($myObj)) {
//                        $comfortPark = CatalogParameter::where('value', 'Парковка')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'udobstva'  . $slug);
//                        })->first();
//                    } else {
//                        $comfortPark = null;
//                    }
//
//                    $twoLift = $contentsComfort->where('name', 'Два лифта')->first();
//                    $twoLiftCollect = collect($twoLift['flats']);
//                    $myObj = $twoLiftCollect->where('id', $relation['id'])->first();
//                    if (!empty($myObj)) {
//                        $comfortTwoLift = CatalogParameter::where('value', 'Два лифта')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'udobstva'  . $slug);
//                        })->first();
//                    } else {
//                        $comfortTwoLift = null;
//                    }
//
//                    $conseg = $contentsComfort->where('name', 'Консьерж')->first();
//                    $consegCollect = collect($conseg['flats']);
//                    $myObj = $consegCollect->where('id', $relation['id'])->first();
//                    if (!empty($myObj)) {
//                        $comfortCons = CatalogParameter::where('value', 'Консьерж')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'udobstva'  . $slug);
//                        })->first();
//                    } else {
//                        $comfortCons = null;
//                    }
//
//                    $balkon = $contentsComfort->where('name', 'Балкон')->first();
//                    $balkonCollect = collect($balkon['flats']);
//                    $myObj = $balkonCollect->where('id', $relation['id'])->first();
//                    if (!empty($myObj)) {
//                        $comfortBal = CatalogParameter::where('value', 'Балкон')->whereHas('filter', function ($q) use ($slug) {
//                            $q->where('alias', 'udobstva'  . $slug);
//                        })->first();
//                    } else {
//                        $comfortBal = null;
//                    }
//
//
//
//                    $arr = collect();
//                    if (!empty($comfortBal)) {
//                        $arr->add($comfortBal->getKey());
//                    }
//                    if (!empty($comfortTwoLift)) {
//                        $arr->add($comfortTwoLift->getKey());
//                    }
//                    if (!empty($comfortCons)) {
//                        $arr->add($comfortCons->getKey());
//                    }
//                    if (!empty($comfortPhone)) {
//                        $arr->add($comfortPhone->getKey());
//                    }
//                    if (!empty($comfortPark)) {
//                        $arr->add($comfortPark->getKey());
//                    }
//                    if (!empty($comfortNet)) {
//                        $arr->add($comfortNet->getKey());
//                    }
//                    if (!empty($rooms)) {
//                        $arr->add($rooms->getKey());
//                    }
//                    if (!empty($livingArea)) {
//                        $arr->add($livingArea->getKey());
//                    }
//                    if (!empty($kitArea)) {
//                        $arr->add($kitArea->getKey());
//                    }
//                    if (!empty($totalArea)) {
//                        $arr->add($totalArea->getKey());
//                    }
//                    if (!empty($floorsInHouse)) {
//                        $arr->add($floorsInHouse->getKey());
//                    }
//                    if (!empty($floor)) {
//                        $arr->add($floor->getKey());
//                    }
//                    if (!empty($dom)) {
//                        $arr->add($dom->getKey());
//                    }
//                    if (!empty($seller)) {
//                        $arr->add($seller->getKey());
//                    }
//                    if (!empty($novizna)) {
//                        $arr->add($novizna->getKey());
//                    }
//                    $model->adParameters()->attach($arr);
//                    var_dump($i);
//                    $i += 1;
//
//                    if (!empty($relation['images'])) {
//                        foreach ($relation['images'] as $image) {
//                            $url = 'https://kto_tam:eto_tapigo@catalog.tapigo.tech/images/thumbnails/thumb_' . $image['image_path'];
//                            $files = resolve(Files::class);
//                            $files->saveParser($model, $url);
//                        }
//                    }
//                }
//            }
//        }

//        foreach ($contents as $item) {
//            $userDB = User::find($item['id']);
//            if (isset($userDB)  || isset($userDB->profile)) {
//                foreach ($item['ads'] as $relation) {
//                    if ($relation['property'] === null || !isset($relation['property']['title'])) {
//                        continue;
//                    }
//                    $alias = Str::slug(Str::limit($relation['property']['title'], 10) . ' ' . str_random(5), '-');
//                    $isModel = CatalogAd::find($relation['property']['id']);
//                    if (isset($relation['category'])) {
//                        $cats = CatalogAdCategory::find($relation['category']['id']);
//                    } else {
//                        $cats = null;
//                    }
//                    $model = $isModel ?? new CatalogAd();
//                    if (isset($relation['location']) && isset($relation['location']['lat']) && $relation['location']['lat'] != 0) {
//                        $model->latitude = $relation['location']['lat'];
//                        $model->longitude = $relation['location']['lng'];
//                        $model->street = $relation['location']['street'];
//                        $model->house = $relation['location']['house'];
//                    }
//                    $isModel ? $model->update() : $model;
//
//
////                    if (!empty($relation['images'])) {
////                        foreach ($relation['images'] as $image) {
////                            $url = 'https://catalog.tapigo.ru/images/thumbnails/thumb_' . $image['image_path'];
////                            $files = resolve(Files::class);
////                            $files->saveParser($model, $url);
////                        }
////                    }
//                }
//            }
//        }
//
//        $client = new Client();
//        $response = $client->get('https://user.tapigo.ru/all-up', ['verify' => false]);
//        $contents = $response->getBody()->getContents();
//        Storage::disk('local')->put('all-up.txt', $contents);
//        $contents = json_decode($contents, true);
//        foreach ($contents as $item) {
//            if ($item['user'] === null) {
//                continue;
//            }
//            $profileDB = Profile::find($item['id']);
//            if (isset($profileDB)) {
//                $profile = $profileDB;
//                $profile->isPerson = true;
//                $profile->update();
//                $profile->person()->create([
//                    'inn' => $item['inn'],
//                    'name' => $item['name'],
//                ]);
//            }
//        }
//
//        $client = new Client();
//        $response = $client->get('https://catalog.tapigo.ru/all-resume-json', ['verify' => false]);
//        $contents = $response->getBody()->getContents();
//        Storage::disk('local')->put('all-resume-json.txt', $contents);
//        $contents = Storage::disk('local')->put('all-resume-json.txt');
//        $contents = json_decode($contents, true);
//
//        foreach ($contents as $item) {
//            $userDB = User::find($item['id']);
//            if (!isset($userDB) || !isset($userDB->profile)) {
//                continue;
//            }
//            $profileID = $userDB->profile->getKey();
//            if (isset($item['posts']) && !empty($item['posts'])) {
//                foreach ($item['posts'] as $post) {
//                    if (isset($post['category'])) {
//                        $cats = JobsResumeCategory::where('name', $post['category']['title'])->first();
//                    } else {
//                        $cats = null;
//                    }
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
//                            $model->name = $property['title'];
//                            $model->category_id = isset($cats) ? $cats->id : null;
//                            $model->experience = (new TimeArray($property['experience_years'], null))->parce();
//                            $model->education = (new Education($property['education'], null))->parce();
//                            $model->schedule = (new Schedule($property['schedule'], null))->parce();
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
//                        $model->category_id = isset($cats) ? $cats->id : null;
//                        $model->min_price = $property['price'];
//                        $model->max_price = $property['price_max'];
//                        $model->description = $property['desc'];
//                        $model->title = $property['title'];
//                        $model->name = $property['title'];
//                        $model->phone = $property['telefon'];
//                        $model->duties = $property['duties'];
//                        $model->demands = $property['demands'];
//                        $model->additionally = $property['additionally'];
//                        $model->experience = (new TimeArray($property['experience_years'], null))->parce();
//                        $model->education = (new Education($property['education'], null))->parce();
//                        $model->schedule = (new Schedule($property['schedule'], null))->parce();
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
//
//
//
//        $client = new Client();
////        $response = $client->get('https://catalog.tapigo.ru/all-uslugi-json2', ['verify' => false]);
////        $contents = $response->getBody()->getContents();
////        Storage::disk('local')->put('all-uslugi-json2.txt', $contents);
//        $contents = Storage::disk('local')->get('all-uslugi-json2.txt');
//        $contents = json_decode($contents, true);
////        dd($contents);
//        foreach ($contents as $item) {
//            $userDB = User::find($item['id']);
//            if (isset($userDB)) {
//                foreach ($item['uslugi'] as $relation) {
//                        $alias = Str::slug(Str::limit($relation['property']['desc'], 10) . ' ' . str_random(5), '-');
//                        $isModel = Service::find($relation['property']['id']);
//                    if (isset($relation['cats'])) {
//                        $cats = ServiceCategory::where('name', $relation['cats']['title'])->first();
//                    } else {
//                        $cats = null;
//                    }
//                        var_dump($relation['property']);
//                        $model = $isModel ?? new Service();
//                        $model->id = $relation['property']['id'];
//                        $model->profile_id = $userDB->profile->getKey();
//                        $model->title = $relation['property']['title'];
//                        $model->contract = $relation['property']['contract'];
//                        $model->guarantee = $relation['property']['guarantee'];
//                        $model->consultation = $relation['property']['consultation'];
//                        $model->hourly_payment = $relation['property']['hourly_payment'];
//                        $model->category_id = isset($cats) ? $cats->id : null;
//                        $model->alias = $alias;
//                        $model->description = $relation['property']['desc'];
//                        $model->price = (int) str_replace(' ', '', $relation['property']['price']);
//                        $isModel ? $model->update() : $model->save();
//                }
//            }
//        }

//        dd($contents);
        return 1;
    }
}
