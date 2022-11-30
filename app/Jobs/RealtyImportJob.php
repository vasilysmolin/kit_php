<?php

namespace App\Jobs;

use App\Models\Profile;
use App\Models\Realty;
use App\Models\RealtyParameter;
use App\Objects\Dadata\Dadata;
use App\Objects\Files;
use App\Objects\States\States;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RealtyImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected Collection $realty;
    protected Collection $externalRealty;
    protected Profile $profile;
    protected string $type;

    public function __construct(Collection $realty, Collection $externalRealty, Profile $profile, string $type)
    {
        $this->realty = $realty;
        $this->externalRealty = $externalRealty;
        $this->profile = $profile;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->type === 'cian') {
            foreach ($this->realty as $realtyString) {
                $realty = simplexml_load_string($realtyString);
                $check = false;
                if((string) $realty->Category === 'flatSale') {
                    $typeParameters = '-bye';
                    $check = true;
                }
                if((string) $realty->Category === 'flatRent') {
                    $typeParameters = '-rent';
                    $check = true;
                }
                if (!$check) {
                    continue;
                }
                $arrayStreet = explode(',', trim((string) $realty->Address));
                $house = array_pop($arrayStreet);
                $street = array_pop($arrayStreet);
                $externalID = $realty->ExternalId ?? (string) $realty->JKSchema->Id;
                $title = $realty->Title ?? $realty->JKSchema->Name ?? 'Квартира';
                $name = (int) $realty->FlatRoomsCount . '-к квартира';
//            Log::info($externalID);
//            Log::info(' ');

                $data = [];
                $data['title'] = $title;
                $data['name'] = $name;
                $data['state'] = (new States())->active();
                $data['price'] = (string)  $realty->BargainTerms->Price;
                $data['sale_price'] = (string)  $realty->BargainTerms->Price;
                $data['description'] = trim((string) $realty->Description);
                $data['profile_id'] = (int) $this->profile->getKey();
                $data['date_build'] = !empty($realty->Building) && !empty($realty->Building->BuildYear) ? (string) $realty->Building->BuildYear : null;
                $data['ceiling_height'] = !empty($realty->Building) && !empty($realty->Building->CeilingHeight) ? (string) $realty->Building->CeilingHeight : null;
                $data['city_id'] = (int) $this->profile->user->city->getKey();
                $data['latitude'] = $realty->Coordinates->Lat;
                $data['longitude'] = $realty->Coordinates->Lng;
                if (empty($data['latitude'])) {
                    try{
                        $dadata = new Dadata();
                        $result = $dadata->findAddress(trim($street) . ' ' . trim($house));
                        if(!empty($result['suggestions']) && $result['suggestions'][0]) {
                            $data = $result['suggestions'][0]['data'];
                            $data['latitude'] = !empty($data['geo_lat']) ? $data['geo_lat'] : null;
                            $data['longitude'] = !empty($data['geo_lat']) ? $data['geo_lon'] : null;
                        }
                    } catch(\Exception $exception) {

                    }

                }
                $data['street'] = trim($street);
                $data['house'] = isset($realty->JKSchema) ? (string) $realty->JKSchema->House->Name : trim($house);
                $data['category_id'] = $typeParameters === '-bye' ? 12 : 383;
                $data['updated_at'] = now();
                $realtyDB = $this->externalRealty->where('external_id', $externalID)->first();
                if (!empty($realtyDB)) {
                    $realtyDB->fill($data);
                    $realtyDB->update();
                    $model = $realtyDB;
                } else {
                    $data['created_at'] = now();
                    $data['external_id'] = $externalID;
                    $data['alias'] = Str::slug($name) . '-' . Str::random(5);
                    $model = new Realty();
                    $model->fill($data);
                    $model->save();
                    $model->moveToStart();
                }
                $i = 0;
                if (config('app.env') === 'production') {
                    foreach ($realty->Images as $photos) {
                        foreach($photos as $item) {
                            if ($i < 15) {
                                $files = resolve(Files::class);
                                $files->saveParser($model, (string) $item['url']);
                            }
                            $i++;
                        }
                    }
                }

                $dataParameters = [];
                $dataParameters['floorsCount'] = (int) $realty->Building->FloorsCount;
                $dataParameters['livingArea'] = (int) $realty->LivingArea;
                $dataParameters['floorNumber'] = (int) $realty->FloorNumber;
                $dataParameters['flatRoomsCount'] = (int) $realty->FlatRoomsCount;
                $dataParameters['totalArea'] = (int) $realty->TotalArea;
                $dataParameters['kitchenArea'] = (int) $realty->KitchenArea;
                $dataParameters['materialType'] = (int) $realty->MaterialType;
                $rooms = RealtyParameter::where('value', $dataParameters['flatRoomsCount'] . ' комнатная')->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'kollicestvo-komnat'  . $typeParameters);
                })->first();
                $livingArea = RealtyParameter::where('sort', $dataParameters['livingArea'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'zilaya-ploshhad'  . $typeParameters);
                })->first();

                $kitArea = RealtyParameter::where('sort', $dataParameters['kitchenArea'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'ploshhad-kuxni'  . $typeParameters);
                })->first();
                $totalArea = RealtyParameter::where('sort', $dataParameters['totalArea'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'obshhaya-ploshhad'  . $typeParameters);
                })->first();
                $floor = RealtyParameter::where('sort', $dataParameters['floorNumber'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'etaz'  . $typeParameters);
                })->first();
                $floorsInHouse = RealtyParameter::where('sort', $dataParameters['floorsCount'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'vsego-etazei'  . $typeParameters);
                })->first();

                $arr = collect();
                if (!empty($comfortBal)) {
                    $arr->add($comfortBal->getKey());
                }
                if (!empty($comfortTwoLift)) {
                    $arr->add($comfortTwoLift->getKey());
                }
                if (!empty($comfortCons)) {
                    $arr->add($comfortCons->getKey());
                }
                if (!empty($comfortPhone)) {
                    $arr->add($comfortPhone->getKey());
                }
                if (!empty($comfortPark)) {
                    $arr->add($comfortPark->getKey());
                }
                if (!empty($comfortNet)) {
                    $arr->add($comfortNet->getKey());
                }
                if (!empty($rooms)) {
                    $arr->add($rooms->getKey());
                }
                if (!empty($livingArea)) {
                    $arr->add($livingArea->getKey());
                }
                if (!empty($kitArea)) {
                    $arr->add($kitArea->getKey());
                }
                if (!empty($totalArea)) {
                    $arr->add($totalArea->getKey());
                }
                if (!empty($floorsInHouse)) {
                    $arr->add($floorsInHouse->getKey());
                }
                if (!empty($floor)) {
                    $arr->add($floor->getKey());
                }
                if (!empty($dom)) {
                    $arr->add($dom->getKey());
                }
                if (!empty($seller)) {
                    $arr->add($seller->getKey());
                }
                if (!empty($novizna)) {
                    $arr->add($novizna->getKey());
                }
                $model->realtyParameters()->sync($arr);
            }
        }
        if($this->type === 'yandex') {
            foreach ($this->realty as $realtyString) {
                $realty = simplexml_load_string($realtyString);
                $check = false;
                if ((string) $realty->category === 'квартира' && (string) $realty->type === 'продажа') {
                    $check = true;
                    $typeParameters = '-bye';
                }
                if ((string) $realty->category === 'квартира' && (string) $realty->type === 'аренда') {
                    $check = true;
                    $typeParameters = '-rent';
                }
                if (!$check) {
                    continue;
                }
                $arrayStreet = explode(',', trim((string) $realty->location->address));
                $house = array_pop($arrayStreet);
                $street = array_pop($arrayStreet);
                $externalID = $realty->ExternalId ?? (string )$realty->attributes()['internal-id'];
                $name = $realty->rooms . '-к квартира';
//            Log::info($externalID);
//            Log::info(' ');

                $data = [];
                $data['title'] = $name;
                $data['name'] = $name;
                $data['state'] = (new States())->active();
                $data['price'] = (string)  $realty->price->value;
                $data['sale_price'] = (string)  $realty->price->value;
                $data['description'] = trim((string) $realty->description);
                $data['profile_id'] = (int) $this->profile->getKey();
                $data['date_build'] = !empty($realty->xpath('//built-year')) ? (int) $realty->xpath('//built-year')[0]->value : null;
                $data['ceiling_height'] = !empty($realty->xpath('//ceiling-height')) ? (int) $realty->xpath('//ceiling-height')[0]->value : null;
                $data['city_id'] = (int) $this->profile->user->city->getKey();
                $data['latitude'] = !empty($realty->location->latitude) ? $realty->location->latitude : null;
                $data['longitude'] = !empty($realty->location->longitude) ? $realty->location->longitude : null;
                if (empty($data['latitude'])) {
                    try{
                        $dadata = new Dadata();
                        $result = $dadata->findAddress(trim($street) . ' ' . trim($house));
                        if(!empty($result['suggestions']) && $result['suggestions'][0]) {
                            $data = $result['suggestions'][0]['data'];
                            $data['latitude'] = !empty($data['geo_lat']) ? $data['geo_lat'] : null;
                            $data['longitude'] = !empty($data['geo_lat']) ? $data['geo_lon'] : null;
                        }
                    } catch(\Exception $exception) {

                    }

                }
                $data['street'] = trim($street);
                $data['house'] = trim($house);
                $data['category_id'] = $typeParameters === '-bye' ? 12 : 383;
                $data['updated_at'] = now();
                $realtyDB = $this->externalRealty->where('external_id', $externalID)->first();

                if (!empty($realtyDB)) {
                    $realtyDB->fill($data);
                    $realtyDB->update();
                    $model = $realtyDB;
                } else {
                    $data['created_at'] = now();
                    $data['external_id'] = $externalID;
                    $data['alias'] = Str::slug($name) . '-' . Str::random(5);
                    $model = new Realty();
                    $model->fill($data);
                    $model->save();
                    $model->moveToStart();
                }
                $i = 0;
                if (config('app.env') === 'production') {
                    foreach ($realty->Images as $photos) {
                        foreach($photos as $item) {
                            if ($i < 15) {
                                $files = resolve(Files::class);
                                $files->saveParser($model, (string) $item['url']);
                            }
                            $i++;
                        }
                    }
                }
                $dataParameters = [];
                $dataParameters['floorsCount'] = (int) $realty['floors-total'];
                $dataParameters['typeRooms'] = !empty($realty->xpath('//rooms-type')) ? (string) $realty->xpath('//living-space')[0]->value : null;
                $dataParameters['house'] = !empty($realty->xpath('//building-type')) ? (string) $realty->xpath('//living-space')[0]->value : null;
                if(!empty($dataParameters['typeRooms'])) {
                    if($dataParameters['typeRooms'] == 'Изолированная') {
                        $dataParameters['typeRooms'] = 'Изолированные';
                    } else {
                        $dataParameters['typeRooms'] = 'Смежные';
                    }
                }
//                $dataParameters['isNew'] = (string) $realty->MarketType;
                $dataParameters['balconyOrLoggia'] = (string) $realty->balcony;
                $dataParameters['livingArea'] = !empty($realty->xpath('//living-space')) ? (int) $realty->xpath('//living-space')[0]->value : 0;
                $dataParameters['floorNumber'] = (int) $realty->floor;
                $dataParameters['flatRoomsCount'] = (int) $realty->rooms;
                $dataParameters['totalArea'] = !empty($realty->xpath('//area')) ? (int) $realty->xpath('//area')[0]['value'] : 0;
                $dataParameters['kitchenArea'] = !empty($realty->xpath('//kitchen-space')) ? (int) $realty->xpath('//kitchen-space')[0]['value'] : 0;
                $dataParameters['materialType'] = !empty($realty->xpath('//building-type')) ?  (int) $realty->xpath('//building-type')[0]['value'] : 0;
                $rooms = RealtyParameter::where('value', $dataParameters['flatRoomsCount'] . ' комнатная')->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'kollicestvo-komnat'  . $typeParameters);
                })->first();
                $livingArea = RealtyParameter::where('sort', $dataParameters['livingArea'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'zilaya-ploshhad'  . $typeParameters);
                })->first();

                $kitArea = RealtyParameter::where('sort', $dataParameters['kitchenArea'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'ploshhad-kuxni'  . $typeParameters);
                })->first();
                $totalArea = RealtyParameter::where('sort', $dataParameters['totalArea'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'obshhaya-ploshhad'  . $typeParameters);
                })->first();
                $floor = RealtyParameter::where('sort', $dataParameters['floorNumber'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'etaz'  . $typeParameters);
                })->first();
                $floorsInHouse = RealtyParameter::where('sort', $dataParameters['floorsCount'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'vsego-etazei'  . $typeParameters);
                })->first();
                $typeRooms = RealtyParameter::where('value', $dataParameters['typeRooms'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'tip-komnat'  . $typeParameters);
                })->first();
//                $isNew = RealtyParameter::where('value', $dataParameters['isNew'])->whereHas('filter', function ($q) use ($typeParameters) {
//                    $q->where('alias', 'novizna'  . $typeParameters);
//                })->first();
                $house = RealtyParameter::where('value', $dataParameters['house'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'dom'  . $typeParameters);
                })->first();
                if($dataParameters['balconyOrLoggia']) {
                    $balkon = RealtyParameter::where('value', 'Балкон')
                        ->whereHas('filter', function ($q) use ($typeParameters) {
                            $q->where('alias', 'udobstva'  . $typeParameters);
                        })->first();
                }

                $arr = collect();
                if (!empty($comfortBal)) {
                    $arr->add($comfortBal->getKey());
                }
                if (!empty($comfortTwoLift)) {
                    $arr->add($comfortTwoLift->getKey());
                }
                if (!empty($comfortCons)) {
                    $arr->add($comfortCons->getKey());
                }
                if (!empty($comfortPhone)) {
                    $arr->add($comfortPhone->getKey());
                }
                if (!empty($comfortPark)) {
                    $arr->add($comfortPark->getKey());
                }
                if (!empty($comfortNet)) {
                    $arr->add($comfortNet->getKey());
                }
                if (!empty($rooms)) {
                    $arr->add($rooms->getKey());
                }
                if (!empty($livingArea)) {
                    $arr->add($livingArea->getKey());
                }
                if (!empty($kitArea)) {
                    $arr->add($kitArea->getKey());
                }
                if (!empty($totalArea)) {
                    $arr->add($totalArea->getKey());
                }
                if (!empty($floorsInHouse)) {
                    $arr->add($floorsInHouse->getKey());
                }
                if (!empty($floor)) {
                    $arr->add($floor->getKey());
                }
                if (!empty($dom)) {
                    $arr->add($dom->getKey());
                }
                if (!empty($seller)) {
                    $arr->add($seller->getKey());
                }
                if (!empty($isNew)) {
                    $arr->add($isNew->getKey());
                }
                if (!empty($typeRooms)) {
                    $arr->add($typeRooms->getKey());
                }
                if (!empty($balkon)) {
                    $arr->add($balkon->getKey());
                }
                if (!empty($house)) {
                    $arr->add($house->getKey());
                }
                $model->realtyParameters()->sync($arr);
            }
        }
        if($this->type === 'avito') {
            foreach ($this->realty as $realtyString) {
                $realty = simplexml_load_string($realtyString);

                $check = false;
                if ((string) $realty->Category === 'Квартиры' && (string) $realty->OperationType === 'Продам') {
                    $check = true;
                    $typeParameters = '-bye';
                }
                if ((string) $realty->Category === 'Квартиры' && (string) $realty->OperationType === 'Сдам') {
                    $check = true;
                    $typeParameters = '-rent';
                }
                if (!$check) {
                    continue;
                }

                $arrayStreet = explode(',', trim((string) $realty->Address));
                $house = array_pop($arrayStreet);
                $street = array_pop($arrayStreet);
                $externalID = $realty->Id;
                $name = $realty->Rooms > 0 ? $realty->Rooms . '-к квартира' : '1-к квартира';
//            Log::info($externalID);
//            Log::info(' ');

                $data = [];
                $data['title'] = $name;
                $data['name'] = $name;
                $data['state'] = (new States())->active();
                $data['price'] = (string)  $realty->Price;
                $data['date_build'] = !empty($realty->BuiltYear) ? (string) $realty->BuiltYear : null;
                $data['ceiling_height'] = !empty($realty->CeilingHeight) ? (string) $realty->CeilingHeight : null;
                $data['sale_price'] = (string)  $realty->Price;
                $data['description'] = trim((string) $realty->Description);
                $data['profile_id'] = (int) $this->profile->getKey();
                $data['city_id'] = (int) $this->profile->user->city->getKey();
                $data['latitude'] = !empty($realty->Latitude) ? $realty->Latitude : null;
                $data['longitude'] = !empty($realty->Longitude) ? $realty->Longitude : null;
                if (empty($data['latitude'])) {
                    try{
                        $dadata = new Dadata();
                        $result = $dadata->findAddress(trim($street) . ' ' . trim($house));
                        if(!empty($result['suggestions']) && $result['suggestions'][0]) {
                            $data = $result['suggestions'][0]['data'];
                            $data['latitude'] = !empty($data['geo_lat']) ? $data['geo_lat'] : null;
                            $data['longitude'] = !empty($data['geo_lat']) ? $data['geo_lon'] : null;
                        }
                    } catch(\Exception $exception) {

                    }

                }
                $data['street'] = trim($street);
                $data['house'] = trim($house);
                $data['category_id'] = $typeParameters === '-bye' ? 12 : 383;
                $data['updated_at'] = now();
                $realtyDB = $this->externalRealty->where('external_id', $externalID)->first();

                if (!empty($realtyDB)) {
                    $realtyDB->fill($data);
                    $realtyDB->update();
                    $model = $realtyDB;
                } else {
                    $data['created_at'] = now();
                    $data['external_id'] = $externalID;
                    $data['alias'] = Str::slug($name) . '-' . Str::random(5);
                    $model = new Realty();
                    $model->fill($data);
                    $model->save();
                    $model->moveToStart();
                }
                $i = 0;
                if (config('app.env') === 'production') {
                    foreach ($realty->Images as $photos) {
                        foreach($photos as $item) {
                            if ($i < 15) {
                                $files = resolve(Files::class);
                                $files->saveParser($model, (string) $item['url']);
                            }
                            $i++;
                        }
                    }
                }

                $dataParameters = [];
                $dataParameters['floorsCount'] = (int) $realty->Floors;
                $dataParameters['isNew'] = (string) $realty->MarketType;
                $dataParameters['balconyOrLoggia'] = (string) $realty->BalconyOrLoggia === 'Балкон' || (string) $realty->BalconyOrLoggia === 'Лоджия';
//                $dataParameters['garbage'] = (string) $realty->Garbage === 'Мусоропровод';
                $dataParameters['house'] = (string) $realty->HouseType;
                $dataParameters['typeRooms'] = !empty($realty->RoomType) ? (string) $realty->RoomType->Option : null;
                $dataParameters['viewFromWindows'] = !empty($realty->ViewFromWindows) ? (string) $realty->ViewFromWindows->Option : null;
                $dataParameters['livingArea'] = (int) $realty->LivingSpace;
                $dataParameters['renovation'] = (string) $realty->Renovation;
                $dataParameters['floorNumber'] = (int) $realty->Floor;
                $dataParameters['flatRoomsCount'] = (int) $realty->Rooms;
                $dataParameters['totalArea'] = (int) $realty->Square;
                $dataParameters['kitchenArea'] = (int) $realty->KitchenSpace;
                $rooms = RealtyParameter::where('value', $dataParameters['flatRoomsCount'] . ' комнатная')->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'kollicestvo-komnat'  . $typeParameters);
                })->first();
                $livingArea = RealtyParameter::where('sort', $dataParameters['livingArea'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'zilaya-ploshhad'  . $typeParameters);
                })->first();

                $kitArea = RealtyParameter::where('sort', $dataParameters['kitchenArea'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'ploshhad-kuxni'  . $typeParameters);
                })->first();
                $totalArea = RealtyParameter::where('sort', $dataParameters['totalArea'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'obshhaya-ploshhad'  . $typeParameters);
                })->first();
                $floor = RealtyParameter::where('sort', $dataParameters['floorNumber'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'etaz'  . $typeParameters);
                })->first();
                $floorsInHouse = RealtyParameter::where('sort', $dataParameters['floorsCount'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'vsego-etazei'  . $typeParameters);
                })->first();
                $isNew = RealtyParameter::where('value', $dataParameters['isNew'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'novizna'  . $typeParameters);
                })->first();
                $typeRooms = RealtyParameter::where('value', $dataParameters['typeRooms'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'tip-komnat'  . $typeParameters);
                })->first();
                $house = RealtyParameter::where('value', $dataParameters['house'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'dom'  . $typeParameters);
                })->first();
                $view = RealtyParameter::where('value', $dataParameters['viewFromWindows'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'vid-iz-okon'  . $typeParameters);
                })->first();
                $renovation = RealtyParameter::where('value', $dataParameters['renovation'])->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'remont'  . $typeParameters);
                })->first();

                if($dataParameters['balconyOrLoggia']) {
                    $balkon = RealtyParameter::where('value', 'Балкон')
                        ->whereHas('filter', function ($q) use ($typeParameters) {
                            $q->where('alias', 'udobstva'  . $typeParameters);
                        })->first();
                }

                $arr = collect();
                if (!empty($comfortBal)) {
                    $arr->add($comfortBal->getKey());
                }
                if (!empty($view)) {
                    $arr->add($view->getKey());
                }
                if (!empty($renovation)) {
                    $arr->add($renovation->getKey());
                }
                if (!empty($comfortTwoLift)) {
                    $arr->add($comfortTwoLift->getKey());
                }
                if (!empty($comfortCons)) {
                    $arr->add($comfortCons->getKey());
                }
                if (!empty($comfortPhone)) {
                    $arr->add($comfortPhone->getKey());
                }
                if (!empty($comfortPark)) {
                    $arr->add($comfortPark->getKey());
                }
                if (!empty($comfortNet)) {
                    $arr->add($comfortNet->getKey());
                }
                if (!empty($rooms)) {
                    $arr->add($rooms->getKey());
                }
                if (!empty($livingArea)) {
                    $arr->add($livingArea->getKey());
                }
                if (!empty($kitArea)) {
                    $arr->add($kitArea->getKey());
                }
                if (!empty($totalArea)) {
                    $arr->add($totalArea->getKey());
                }
                if (!empty($floorsInHouse)) {
                    $arr->add($floorsInHouse->getKey());
                }
                if (!empty($floor)) {
                    $arr->add($floor->getKey());
                }
                if (!empty($dom)) {
                    $arr->add($dom->getKey());
                }
                if (!empty($seller)) {
                    $arr->add($seller->getKey());
                }
                if (!empty($isNew)) {
                    $arr->add($isNew->getKey());
                }
                if (!empty($typeRooms)) {
                    $arr->add($typeRooms->getKey());
                }
                if (!empty($house)) {
                    $arr->add($house->getKey());
                }
                if (!empty($balkon)) {
                    $arr->add($balkon->getKey());
                }
                $model->realtyParameters()->sync($arr);
            }
        }
    }
}
