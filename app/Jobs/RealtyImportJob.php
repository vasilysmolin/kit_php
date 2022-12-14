<?php

namespace App\Jobs;

use App\Models\Profile;
use App\Models\Realty;
use App\Models\Parameter;
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
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
        if ($this->type === 'cian') {
            foreach ($this->realty as $realtyString) {
                $realty = simplexml_load_string($realtyString);
                $checkFlat = false;
                if ((string) $realty->Category === 'flatSale') {
                    $checkFlat = true;
                    $typeParameters = '-bye';
                }
                if ((string) $realty->Category === 'flatRent') {
                    $checkFlat = true;
                    $typeParameters = '-rent';
                }
                if ($checkFlat) {
                    $this->flatCian($realty, $typeParameters);
                }

                $checkHouse = false;
                if ((string) $realty->Category === 'houseSale') {
                    $checkHouse = true;
                    $typeParameters = '-bye';
                }
                if ((string) $realty->Category === 'houseRent') {
                    $checkFlat = true;
                    $typeParameters = '-rent';
                }
                if ($checkHouse) {
                    $this->houseCian($realty, $typeParameters);
                }
            }
        }
        if ($this->type === 'yandex') {
            foreach ($this->realty as $realtyString) {
                $realty = simplexml_load_string($realtyString);

                $checkFlat = false;
                if ((string) $realty->category === 'квартира' && (string) $realty->type === 'продажа') {
                    $checkFlat = true;
                    $typeParameters = '-bye';
                }
                if ((string) $realty->category === 'квартира' && (string) $realty->type === 'аренда') {
                    $checkFlat = true;
                    $typeParameters = '-rent';
                }
                if ($checkFlat) {
                    $this->flatYandex($realty, $typeParameters);
                }

                $checkHouse = false;
                if ((string) $realty->category === 'дом' && (string) $realty->type === 'продажа') {
                    $checkHouse = true;
                    $typeParameters = '-bye';
                }
                if ((string) $realty->category === 'дом' && (string) $realty->type === 'аренда') {
                    $checkHouse = true;
                    $typeParameters = '-rent';
                }

                if ($checkHouse) {
                    $this->houseYandex($realty, $typeParameters);
                }
            }
        }
        if ($this->type === 'avito') {
            foreach ($this->realty as $realtyString) {
                $realty = simplexml_load_string($realtyString);
                $checkFlat = false;
                if ((string) $realty->Category === 'Квартиры' && (string) $realty->OperationType === 'Продам') {
                    $checkFlat = true;
                    $typeParameters = '-bye';
                }
                if ((string) $realty->Category === 'Квартиры' && (string) $realty->OperationType === 'Сдам') {
                    $checkFlat = true;
                    $typeParameters = '-rent';
                }

                if ($checkFlat) {
                    $this->flatAvito($realty, $typeParameters);
                }

                $checkHouse = false;
                if ((string) $realty->Category === 'Дома, дачи, коттеджи' && (string) $realty->OperationType === 'Продам') {
                    $checkHouse = true;
                    $typeParameters = '-bye';
                }
                if ((string) $realty->Category === 'Дома, дачи, коттеджи' && (string) $realty->OperationType === 'Сдам') {
                    $checkHouse = true;
                    $typeParameters = '-rent';
                }

                if ($checkHouse) {
                    $this->houseAvito($realty, $typeParameters);
                }
            }
        }
    }

    private function flatAvito($realty, string $typeParameters)
    {
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
        $data['video'] = (string)  $realty->VideoURL;
        $data['date_build'] = !empty($realty->BuiltYear) ? (string) $realty->BuiltYear : null;
        $data['ceiling_height'] = !empty($realty->CeilingHeight) ? (string) $realty->CeilingHeight : null;
        $data['sale_price'] = (string)  $realty->Price;
        $data['description'] = trim((string) $realty->Description);
        $data['profile_id'] = (int) $this->profile->getKey();
        $data['city_id'] = (int) $this->profile->user->city->getKey();
        $data['latitude'] = !empty($realty->Latitude) ? $realty->Latitude : null;
        $data['longitude'] = !empty($realty->Longitude) ? $realty->Longitude : null;
        if (empty($data['latitude'])) {
            try {
                $dadata = new Dadata();
                $result = $dadata->findAddress(trim($street) . ' ' . trim($house));
                if (!empty($result['suggestions']) && $result['suggestions'][0]) {
                    $dadata = $result['suggestions'][0]['data'];
                    $data['latitude'] = !empty($dadata['geo_lat']) ? $dadata['geo_lat'] : null;
                    $data['longitude'] = !empty($dadata['geo_lat']) ? $dadata['geo_lon'] : null;
                }
            } catch (\Exception $exception) {
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
            $i = 0;
            if (config('app.env') === 'production') {
                foreach ($realty->Images as $photos) {
                    foreach ($photos as $item) {
                        $files = resolve(Files::class);
                        $files->saveParser($model, (string) $item['url']);
                        $i++;
                    }
                }
            }
        }

        if (empty($model->agent)) {
            $nameAgent = (string) $realty->CompanyName;
            if (!empty($nameAgent)) {
                $phoneAgent = preg_replace("/[^0-9]/", '', (string) $realty->ContactPhone);
                $emailAgent = (string) $realty->EMail;
                $model->agent()->create([
                    'phone' => $phoneAgent,
                    'name' => $nameAgent,
                    'email' => $emailAgent,
                ]);
            }
        }



        $dataParameters = [];
        $dataParameters['floorsCount'] = (int) $realty->Floors;
        $dataParameters['propertyRights'] = (int) $realty->PropertyRights;
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
        $rooms = Parameter::where('value', $dataParameters['flatRoomsCount'] . ' комнатная')
            ->whereHas('filter', function ($q) use ($typeParameters) {
                $q->where('alias', 'kollicestvo-komnat'  . $typeParameters);
            })->first();

        $livingArea = Parameter::where('sort', $dataParameters['livingArea'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'zilaya-ploshhad'  . $typeParameters);
        })->first();

        $kitArea = Parameter::where('sort', $dataParameters['kitchenArea'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'ploshhad-kuxni'  . $typeParameters);
        })->first();
        $totalArea = Parameter::where('sort', $dataParameters['totalArea'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'obshhaya-ploshhad'  . $typeParameters);
        })->first();
        $floor = Parameter::where('sort', $dataParameters['floorNumber'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'etaz'  . $typeParameters);
        })->first();
        $floorsInHouse = Parameter::where('sort', $dataParameters['floorsCount'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'vsego-etazei'  . $typeParameters);
        })->first();
        $isNew = Parameter::where('value', $dataParameters['isNew'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'novizna'  . $typeParameters);
        })->first();
        $typeRooms = Parameter::where('value', $dataParameters['typeRooms'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'tip-komnat'  . $typeParameters);
        })->first();
        $house = Parameter::where('value', $dataParameters['house'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'dom'  . $typeParameters);
        })->first();
        $view = Parameter::where('value', $dataParameters['viewFromWindows'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'vid-iz-okon'  . $typeParameters);
        })->first();
        $renovation = Parameter::where('value', $dataParameters['renovation'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'remont'  . $typeParameters);
        })->first();
        $propertyRights = Parameter::where('value', $dataParameters['propertyRights'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'prodavec'  . $typeParameters);
        })->first();

        if ($dataParameters['balconyOrLoggia']) {
            $balkon = Parameter::where('value', 'Балкон')
                ->whereHas('filter', function ($q) use ($typeParameters) {
                    $q->where('alias', 'udobstva'  . $typeParameters);
                })->first();
        }

        $arr = collect();
        if (!empty($propertyRights)) {
            $arr->add($propertyRights->getKey());
        }
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
        $model->parameters()->sync($arr);
    }

    private function flatYandex($realty, string $typeParameters)
    {
        $arrayStreet = explode(',', trim((string) $realty->location->address));
        $house = array_pop($arrayStreet);
        $street = array_pop($arrayStreet);
        $externalID = $realty->ExternalId ?? (string)$realty->attributes()['internal-id'];
        $name = $realty->rooms . '-к квартира';
        $data = [];
        $data['title'] = $name;
        $data['name'] = $name;
        $data['state'] = (new States())->active();
        $data['price'] = (string)  $realty->price->value;
        $data['sale_price'] = (string)  $realty->price->value;
        $data['description'] = trim((string) $realty->description);
        $data['profile_id'] = (int) $this->profile->getKey();
        $data['date_build'] = !empty($realty->xpath('//built-year')) ? (int) $realty->xpath('//built-year')[0]->value : null;
        if ($data['date_build'] < 1) {
            $data['date_build'] = null;
        }
        $data['ceiling_height'] = !empty($realty->xpath('//ceiling-height')) ? (int) $realty->xpath('//ceiling-height')[0]->value : null;
        $data['city_id'] = (int) $this->profile->user->city->getKey();
        $data['latitude'] = !empty($realty->location->latitude) ? (string) $realty->location->latitude : null;
        $data['longitude'] = !empty($realty->location->longitude) ? (string) $realty->location->longitude : null;
        if (empty($data['latitude'])) {
            try {
                $dadata = new Dadata();
                $result = $dadata->findAddress(trim($street) . ' ' . trim($house));
                if (!empty($result['suggestions']) && $result['suggestions'][0]) {
                    $dadata = $result['suggestions'][0]['data'];
                    $data['latitude'] = !empty($dadata['geo_lat']) ? $dadata['geo_lat'] : null;
                    $data['longitude'] = !empty($dadata['geo_lat']) ? $dadata['geo_lon'] : null;
                }
            } catch (\Exception $exception) {
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
            $i = 0;
            if (config('app.env') === 'production') {
                foreach ($realty->image as $photos) {
                    $files = resolve(Files::class);
                    $files->saveParser($model, (string) $photos);
                    $i++;
                }
            }
        }
        $dataParameters = [];
        $dataParameters['floorsCount'] = (int) $realty['floors-total'];
        $dataParameters['typeRooms'] = !empty($realty->xpath('//rooms-type')) ? (string) $realty->xpath('//living-space')[0]->value : null;
        $dataParameters['house'] = !empty($realty->xpath('//building-type')) ? (string) $realty->xpath('//living-space')[0]->value : null;
        if (!empty($dataParameters['typeRooms'])) {
            if ($dataParameters['typeRooms'] === 'Изолированная') {
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
        $dataParameters['totalArea'] = !empty($realty->xpath('//area')) ? (int) $realty->xpath('//area')[0]->value : 0;
        $dataParameters['kitchenArea'] = !empty($realty->xpath('//kitchen-space')) ? (int) $realty->xpath('//kitchen-space')[0]->value : 0;
        $dataParameters['materialType'] = !empty($realty->xpath('//building-type')) ?  (string) $realty->xpath('//building-type')[0] : 0;
        $rooms = Parameter::where('value', $dataParameters['flatRoomsCount'] . ' комнатная')->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'kollicestvo-komnat'  . $typeParameters);
        })->first();
        $materialType = Parameter::where('value', $dataParameters['materialType'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'material-sten'  . $typeParameters);
        })->first();
        $livingArea = Parameter::where('sort', $dataParameters['livingArea'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'zilaya-ploshhad'  . $typeParameters);
        })->first();

        $kitArea = Parameter::where('sort', $dataParameters['kitchenArea'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'ploshhad-kuxni'  . $typeParameters);
        })->first();
        $totalArea = Parameter::where('sort', $dataParameters['totalArea'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'obshhaya-ploshhad'  . $typeParameters);
        })->first();
        $floor = Parameter::where('sort', $dataParameters['floorNumber'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'etaz'  . $typeParameters);
        })->first();
        $floorsInHouse = Parameter::where('sort', $dataParameters['floorsCount'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'vsego-etazei'  . $typeParameters);
        })->first();
        $typeRooms = Parameter::where('value', $dataParameters['typeRooms'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'tip-komnat'  . $typeParameters);
        })->first();
//                $isNew = RealtyParameter::where('value', $dataParameters['isNew'])->whereHas('filter', function ($q) use ($typeParameters) {
//                    $q->where('alias', 'novizna'  . $typeParameters);
//                })->first();
        $house = Parameter::where('value', $dataParameters['house'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'dom'  . $typeParameters);
        })->first();
        if ($dataParameters['balconyOrLoggia']) {
            $balkon = Parameter::where('value', 'Балкон')
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
        if (!empty($materialType)) {
            $arr->add($materialType->getKey());
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
        $model->parameters()->sync($arr);
    }

    private function flatCian($realty, string $typeParameters)
    {
        $arrayStreet = explode(',', trim((string) $realty->Address));
        $house = array_pop($arrayStreet);
        $street = array_pop($arrayStreet);
        $externalID = $realty->ExternalId ?? (string) $realty->JKSchema->Id;
        $title = (int) $realty->FlatRoomsCount . '-к квартира';
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
            try {
                $dadata = new Dadata();
                $result = $dadata->findAddress(trim($street) . ' ' . trim($house));
                if (!empty($result['suggestions']) && $result['suggestions'][0]) {
                    $dadata = $result['suggestions'][0]['data'];
                    $data['latitude'] = !empty($dadata['geo_lat']) ? $dadata['geo_lat'] : null;
                    $data['longitude'] = !empty($dadata['geo_lat']) ? $dadata['geo_lon'] : null;
                }
            } catch (\Exception $exception) {
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
            $i = 0;
            if (config('app.env') === 'production') {
                foreach ($realty->Photos as $photos) {
                    foreach ($photos->PhotoSchema as $item) {
                        $files = resolve(Files::class);
                        $files->saveParser($model, (string) $item->FullUrl);
                        $i++;
                    }
                }
            }
        }

        $dataParameters = [];
//        dd((int) $realty->Building->FloorsCount, $realty);
        $dataParameters['floorsCount'] = (int) $realty->Building->FloorsCount;
        $dataParameters['livingArea'] = (int) $realty->LivingArea;
        $dataParameters['floorNumber'] = (int) $realty->FloorNumber;
        $dataParameters['flatRoomsCount'] = (int) $realty->FlatRoomsCount;
        $dataParameters['totalArea'] = (int) $realty->TotalArea;
        $dataParameters['kitchenArea'] = (int) $realty->KitchenArea;
        $dataParameters['materialType'] = (int) $realty->MaterialType;
        $rooms = Parameter::where('value', $dataParameters['flatRoomsCount'] . ' комнатная')->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'kollicestvo-komnat'  . $typeParameters);
        })->first();
        $livingArea = Parameter::where('sort', $dataParameters['livingArea'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'zilaya-ploshhad'  . $typeParameters);
        })->first();

        $kitArea = Parameter::where('sort', $dataParameters['kitchenArea'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'ploshhad-kuxni'  . $typeParameters);
        })->first();
        $totalArea = Parameter::where('sort', $dataParameters['totalArea'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'obshhaya-ploshhad'  . $typeParameters);
        })->first();
        $floor = Parameter::where('sort', $dataParameters['floorNumber'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'etaz'  . $typeParameters);
        })->first();
        $floorsInHouse = Parameter::where('sort', $dataParameters['floorsCount'])->whereHas('filter', function ($q) use ($typeParameters) {
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
        $model->parameters()->sync($arr);
    }

    private function houseAvito($realty, string $typeParameters)
    {
        //LandArea Площадь земельного участка
        //Square Площадь
        //Floors этажей
        //HouseServices Электричество Отопление
        //ObjectType Дача
        //DistanceToCity 25 км
        //WallsType бревно
        //Rooms комнат
        //Renovation требуется
        $arrayStreet = explode(',', trim((string) $realty->Address));
        $house = array_pop($arrayStreet);
        $street = array_pop($arrayStreet);
        $externalID = $realty->Id;
        $name = !empty($realty->Title) ? $realty->Title : 'Дом';
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
            try {
                $dadata = new Dadata();
                $result = $dadata->findAddress(trim($street) . ' ' . trim($house));
                if (!empty($result['suggestions']) && $result['suggestions'][0]) {
                    $dadata = $result['suggestions'][0]['data'];
                    $data['latitude'] = !empty($dadata['geo_lat']) ? $dadata['geo_lat'] : null;
                    $data['longitude'] = !empty($dadata['geo_lat']) ? $dadata['geo_lon'] : null;
                }
            } catch (\Exception $exception) {
            }
        }
        $data['street'] = trim($street);
        $data['house'] = trim($house);
        $data['category_id'] = $typeParameters === '-bye' ? 389 : 385;
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
            $i = 0;
//            if (config('app.env') === 'production') {
            foreach ($realty->Images as $photos) {
                foreach ($photos as $item) {
                    $files = resolve(Files::class);
                    $files->saveParser($model, (string) $item['url']);
                    $i++;
                }
            }
//            }
        }

        $dataParameters = [];
        $dataParameters['floorsCount'] = (int) $realty->Floors;
        $dataParameters['wallsType'] = !empty($realty->WallsType) ? (string) $realty->WallsType : null;
        $dataParameters['distanceToCity'] = !empty($realty->DistanceToCity) ? (string) $realty->DistanceToCity : null;
        $dataParameters['landArea'] = !empty($realty->LandArea) ? (int) $realty->LandArea : null;
        $dataParameters['square'] = !empty($realty->Square) ? (int) $realty->Square : null;
        $dataParameters['rooms'] = !empty($realty->Rooms) ? (int) $realty->Rooms : null;
        $dataParameters['renovation'] = (string) $realty->Renovation;
        $rooms = Parameter::where('value', $dataParameters['rooms'] . ' комнат')->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'kolicestvo-komnat-doma'  . $typeParameters);
        })->first();
        $landArea = Parameter::where('sort', $dataParameters['landArea'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'ploshhad-zemelnogo-ucastka'  . $typeParameters);
        })->first();

        $square = Parameter::where('sort', $dataParameters['square'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'ploshhad-doma'  . $typeParameters);
        })->first();

        $floorsCount = Parameter::where('value', $dataParameters['floorsCount'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'etazei-doma'  . $typeParameters);
        })->first();

        $distanceToCity = Parameter::where('value', $dataParameters['distanceToCity'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'rasstoianie-do-goroda'  . $typeParameters);
        })->first();

        $renovation = Parameter::where('value', $dataParameters['renovation'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'remont-doma'  . $typeParameters);
        })->first();

        $wallsType = Parameter::where('value', $dataParameters['wallsType'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'material-sten-doma'  . $typeParameters);
        })->first();

        $arr = collect();
        if (!empty($landArea)) {
            $arr->add($landArea->getKey());
        }
        if (!empty($wallsType)) {
            $arr->add($wallsType->getKey());
        }
        if (!empty($square)) {
            $arr->add($square->getKey());
        }
        if (!empty($renovation)) {
            $arr->add($renovation->getKey());
        }
        if (!empty($floorsCount)) {
            $arr->add($floorsCount->getKey());
        }
        if (!empty($distanceToCity)) {
            $arr->add($distanceToCity->getKey());
        }
        if (!empty($rooms)) {
            $arr->add($rooms->getKey());
        }

        $model->parameters()->sync($arr);
    }

    private function houseYandex($realty, string $typeParameters)
    {
        $arrayStreet = explode(',', trim((string) $realty->location->address));
        $house = array_pop($arrayStreet);
        $street = array_pop($arrayStreet);
        $externalID = $realty->ExternalId ?? (string)$realty->attributes()['internal-id'];
        $name = $realty->category;
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
            try {
                $dadata = new Dadata();
                $result = $dadata->findAddress(trim($street) . ' ' . trim($house));
                if (!empty($result['suggestions']) && $result['suggestions'][0]) {
                    $dadata = $result['suggestions'][0]['data'];
                    $data['latitude'] = !empty($dadata['geo_lat']) ? $dadata['geo_lat'] : null;
                    $data['longitude'] = !empty($dadata['geo_lat']) ? $dadata['geo_lon'] : null;
                }
            } catch (\Exception $exception) {
            }
        }
        $data['street'] = trim($street);
        $data['house'] = trim($house);
        $data['category_id'] = $typeParameters === '-bye' ? 389 : 385;
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
            $i = 0;
            if (config('app.env') === 'production') {
                foreach ($realty->image as $photos) {
                    $files = resolve(Files::class);
                    $files->saveParser($model, (string) $photos);
                    $i++;
                }
            }
        }
        $dataParameters = [];

        $dataParameters['floorsCount'] = (int) $realty['floors-total'];
        $dataParameters['wallsType'] = !empty($realty->xpath('//building-type')) ? (string) $realty->xpath('//building-type')[0]->value : null;
        $dataParameters['landArea'] = !empty($realty->xpath('//area')) ? (int) $realty->xpath('//area')[0]['value'] : 0;
        $dataParameters['square'] = !empty($realty->xpath('//lot-area')) ? (int) $realty->xpath('//lot-area')[0]['value'] : 0;

//        $rooms = RealtyParameter::where('value', $dataParameters['rooms'] . ' комнат')->whereHas('filter', function ($q) use ($typeParameters) {
//            $q->where('alias', 'kolicestvo-komnat-doma'  . $typeParameters);
//        })->first();
        $landArea = Parameter::where('sort', $dataParameters['landArea'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'ploshhad-zemelnogo-ucastka'  . $typeParameters);
        })->first();

        $square = Parameter::where('sort', $dataParameters['square'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'ploshhad-doma'  . $typeParameters);
        })->first();

        $floorsCount = Parameter::where('value', $dataParameters['floorsCount'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'etazei-doma'  . $typeParameters);
        })->first();

//        $distanceToCity = RealtyParameter::where('value', $dataParameters['distanceToCity'])->whereHas('filter', function ($q) use ($typeParameters) {
//            $q->where('alias', 'rasstoianie-do-goroda'  . $typeParameters);
//        })->first();
//
//        $renovation = RealtyParameter::where('value', $dataParameters['renovation'])->whereHas('filter', function ($q) use ($typeParameters) {
//            $q->where('alias', 'remont-doma'  . $typeParameters);
//        })->first();

        $wallsType = Parameter::where('value', $dataParameters['wallsType'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'material-sten-doma'  . $typeParameters);
        })->first();

        $arr = collect();
        if (!empty($landArea)) {
            $arr->add($landArea->getKey());
        }
        if (!empty($wallsType)) {
            $arr->add($wallsType->getKey());
        }
        if (!empty($square)) {
            $arr->add($square->getKey());
        }
        if (!empty($renovation)) {
            $arr->add($renovation->getKey());
        }
        if (!empty($floorsCount)) {
            $arr->add($floorsCount->getKey());
        }
        if (!empty($distanceToCity)) {
            $arr->add($distanceToCity->getKey());
        }
        if (!empty($rooms)) {
            $arr->add($rooms->getKey());
        }

        $model->parameters()->sync($arr);
    }

    private function houseCian($realty, string $typeParameters)
    {
        $arrayStreet = explode(',', trim((string) $realty->Address));
        $house = array_pop($arrayStreet);
        $street = array_pop($arrayStreet);
        $externalID = $realty->ExternalId ?? (string) $realty->JKSchema->Id;
        Log::info($externalID);
        $title = $name = (string) $realty->Title;
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
            try {
                $dadata = new Dadata();
                $result = $dadata->findAddress(trim($street) . ' ' . trim($house));
                if (!empty($result['suggestions']) && $result['suggestions'][0]) {
                    $dadata = $result['suggestions'][0]['data'];
                    $data['latitude'] = !empty($dadata['geo_lat']) ? $dadata['geo_lat'] : null;
                    $data['longitude'] = !empty($dadata['geo_lat']) ? $dadata['geo_lon'] : null;
                }
            } catch (\Exception $exception) {
            }
        }
        $data['street'] = trim($street);
        $data['house'] = isset($realty->JKSchema) ? (string) $realty->JKSchema->House->Name : trim($house);
        $data['category_id'] = $typeParameters === '-bye' ? 389 : 385;
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
            $i = 0;
            if (config('app.env') === 'production') {
                foreach ($realty->Photos as $photos) {
                    foreach ($photos->PhotoSchema as $item) {
                        $files = resolve(Files::class);
                        $files->saveParser($model, (string) $item->FullUrl);
                        $i++;
                    }
                }
            }
        }

        $dataParameters = [];
        // общая площадь
        $dataParameters['landArea'] = (int) $realty->TotalArea;
        // всего комнат
        $dataParameters['rooms'] = (int) $realty->BedroomsCount;
        // материал стен трансформирвоать
        $dataParameters['wallsType'] = (int) $realty->Building->MaterialType;
        if ($dataParameters['wallsType'] === 'wood') {
            $dataParameters['wallsType'] = 'Деревянный';
        }
        if ($dataParameters['wallsType'] === 'panel') {
            $dataParameters['wallsType'] = 'Панельный';
        }
        if ($dataParameters['wallsType'] === 'monolith') {
            $dataParameters['wallsType'] = 'Монолитный';
        }
        if ($dataParameters['wallsType'] === 'brick') {
            $dataParameters['wallsType'] = 'Кирпичный';
        }
        if ($dataParameters['wallsType'] === 'block') {
            $dataParameters['wallsType'] = 'Блочный';
        }
        // всего этажей
        $dataParameters['floorsCount'] = (int) $realty->Building->FloorsCount;
        // ремонт косметичекий
        $dataParameters['renovation'] = (int) $realty->RepairType;
        if ($dataParameters['renovation'] === 'cosmetic') {
            $dataParameters['renovation'] = 'Косметический';
        }
        if ($dataParameters['renovation'] === 'design') {
            $dataParameters['renovation'] = 'Дизайнерский';
        }
        if ($dataParameters['renovation'] === 'euro') {
            $dataParameters['renovation'] = 'Евро';
        }
        $rooms = Parameter::where('value', $dataParameters['rooms'] . ' комнат')->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'kolicestvo-komnat-doma'  . $typeParameters);
        })->first();
        $landArea = Parameter::where('sort', $dataParameters['landArea'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'ploshhad-zemelnogo-ucastka'  . $typeParameters);
        })->first();

        $floorsCount = Parameter::where('value', $dataParameters['floorsCount'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'etazei-doma'  . $typeParameters);
        })->first();

        $wallsType = Parameter::where('value', $dataParameters['wallsType'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'material-sten-doma'  . $typeParameters);
        })->first();

        $renovation = Parameter::where('value', $dataParameters['renovation'])->whereHas('filter', function ($q) use ($typeParameters) {
            $q->where('alias', 'remont-doma'  . $typeParameters);
        })->first();

        $arr = collect();
        if (!empty($landArea)) {
            $arr->add($landArea->getKey());
        }
        if (!empty($square)) {
            $arr->add($square->getKey());
        }
        if (!empty($renovation)) {
            $arr->add($renovation->getKey());
        }
        if (!empty($floorsCount)) {
            $arr->add($floorsCount->getKey());
        }
        if (!empty($distanceToCity)) {
            $arr->add($distanceToCity->getKey());
        }
        if (!empty($rooms)) {
            $arr->add($rooms->getKey());
        }

        $model->parameters()->sync($arr);
    }
}
