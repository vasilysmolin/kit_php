<?php

namespace App\Jobs;

use App\Models\Profile;
use App\Models\Realty;
use App\Models\RealtyParameter;
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

        /**
         * Продажа квартир 12 id
         * - cian flatSale, yandex продажа-квартира, avito <Category>Квартиры</Category><OperationType>Продам</OperationType>
         * */
        //        set_time_limit(180);
        if($this->type === 'cian') {
            foreach ($this->realty as $realtyString) {
                $realty = simplexml_load_string($realtyString);
                if ((string) $realty->Category !== 'flatSale') {
                    continue;
                }
                $arrayStreet = explode(',', trim((string) $realty->Address));
                $house = array_pop($arrayStreet);
                $street = array_pop($arrayStreet);
                $externalID = $realty->ExternalId ?? (string) $realty->JKSchema->Id;
                $name = $realty->Title ?? $realty->JKSchema->Name ?? 'Квартира';
//            Log::info($externalID);
//            Log::info(' ');

                $data = [];
                $data['title'] = $name;
                $data['name'] = $name;
                $data['state'] = (new States())->active();
                $data['price'] = (string)  $realty->BargainTerms->Price;
                $data['sale_price'] = (string)  $realty->BargainTerms->Price;
                $data['description'] = trim((string) $realty->Description);
                $data['profile_id'] = (int) $this->profile->getKey();
                $data['city_id'] = (int) $this->profile->user->city->getKey();
                $data['latitude'] = $realty->Coordinates->Lat;
                $data['longitude'] = $realty->Coordinates->Lng;
                $data['street'] = trim($street);
                $data['house'] = isset($realty->JKSchema) ? (string) $realty->JKSchema->House->Name : trim($house);
                $data['category_id'] = 12;
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
                foreach ($realty->Photos->PhotoSchema as $photo) {
                    if ($i < 1) {
                        $files = resolve(Files::class);
                        $files->saveParser($model, (string) $photo->FullUrl);
                    }
                    $i++;
                }

                $dataParameters = [];
                $dataParameters['floorsCount'] = (int) $realty->Building->FloorsCount;
                $dataParameters['livingArea'] = (int) $realty->LivingArea;
                $dataParameters['floorNumber'] = (int) $realty->FloorNumber;
                $dataParameters['flatRoomsCount'] = (int) $realty->FlatRoomsCount;
                $dataParameters['totalArea'] = (int) $realty->TotalArea;
                $dataParameters['kitchenArea'] = (int) $realty->KitchenArea;
                $dataParameters['materialType'] = (int) $realty->MaterialType;
                $rooms = RealtyParameter::where('value', $dataParameters['flatRoomsCount'] . ' комнатная')->first();
                $livingArea = RealtyParameter::where('sort', $dataParameters['livingArea'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'zilaya-ploshhad'  . '-bye');
                })->first();

                $kitArea = RealtyParameter::where('sort', $dataParameters['kitchenArea'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'ploshhad-kuxni'  . '-bye');
                })->first();
                $totalArea = RealtyParameter::where('sort', $dataParameters['totalArea'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'obshhaya-ploshhad'  . '-bye');
                })->first();
                $floor = RealtyParameter::where('sort', $dataParameters['floorNumber'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'etaz'  . '-bye');
                })->first();
                $floorsInHouse = RealtyParameter::where('sort', $dataParameters['floorsCount'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'vsego-etazei'  . '-bye');
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
                if ((string) $realty->category !== 'квартира' && (string) $realty->type !== 'продажа') {
                    continue;
                }
                $arrayStreet = explode(',', trim((string) $realty->location->address));
                $house = array_pop($arrayStreet);
                $street = array_pop($arrayStreet);
                $externalID = $realty->ExternalId ?? (string )$realty->attributes()['internal-id'];
                $name = $realty->rooms . ' квартира';
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
                $data['city_id'] = (int) $this->profile->user->city->getKey();
                $data['latitude'] = !empty($realty->location->latitude) ? $realty->location->latitude : null;
                $data['longitude'] = !empty($realty->location->longitude) ? $realty->location->longitude : null;
                $data['street'] = trim($street);
                $data['house'] = trim($house);
                $data['category_id'] = 12;
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
                foreach ($realty->image as $photo) {
                    if ($i < 1) {
                        $files = resolve(Files::class);
                        $files->saveParser($model, (string) $photo);
                    }
                    $i++;
                }
                $dataParameters = [];
                $dataParameters['floorsCount'] = (int) $realty['floors-total'];
                $dataParameters['livingArea'] = !empty($realty->xpath('//living-space')) ? (int) $realty->xpath('//living-space')[0]->value : 0;
                $dataParameters['floorNumber'] = (int) $realty->floor;
                $dataParameters['flatRoomsCount'] = (int) $realty->rooms;
                $dataParameters['totalArea'] = !empty($realty->xpath('//area')) ? (int) $realty->xpath('//area')[0]['value'] : 0;
                $dataParameters['kitchenArea'] = !empty($realty->xpath('//kitchen-space')) ? (int) $realty->xpath('//kitchen-space')[0]['value'] : 0;
                $dataParameters['materialType'] = !empty($realty->xpath('//building-type')) ?  (int) $realty->xpath('//building-type')[0]['value'] : 0;
                $rooms = RealtyParameter::where('value', $dataParameters['flatRoomsCount'] . ' комнатная')->first();
                $livingArea = RealtyParameter::where('sort', $dataParameters['livingArea'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'zilaya-ploshhad'  . '-bye');
                })->first();

                $kitArea = RealtyParameter::where('sort', $dataParameters['kitchenArea'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'ploshhad-kuxni'  . '-bye');
                })->first();
                $totalArea = RealtyParameter::where('sort', $dataParameters['totalArea'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'obshhaya-ploshhad'  . '-bye');
                })->first();
                $floor = RealtyParameter::where('sort', $dataParameters['floorNumber'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'etaz'  . '-bye');
                })->first();
                $floorsInHouse = RealtyParameter::where('sort', $dataParameters['floorsCount'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'vsego-etazei'  . '-bye');
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

        if($this->type === 'avito') {
            foreach ($this->realty as $realtyString) {
                $realty = simplexml_load_string($realtyString);
                if ((string) $realty->category !== 'квартира' && (string) $realty->type !== 'продажа') {
                    continue;
                }
                $arrayStreet = explode(',', trim((string) $realty->location->address));
                $house = array_pop($arrayStreet);
                $street = array_pop($arrayStreet);
                $externalID = $realty->ExternalId ?? (string )$realty->attributes()['internal-id'];
                $name = $realty->rooms . ' квартира';
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
                $data['city_id'] = (int) $this->profile->user->city->getKey();
                $data['latitude'] = !empty($realty->location->latitude) ? $realty->location->latitude : null;
                $data['longitude'] = !empty($realty->location->longitude) ? $realty->location->longitude : null;
                $data['street'] = trim($street);
                $data['house'] = trim($house);
                $data['category_id'] = 12;
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
                foreach ($realty->image as $photo) {
                    if ($i < 1) {
                        $files = resolve(Files::class);
                        $files->saveParser($model, (string) $photo);
                    }
                    $i++;
                }
                $dataParameters = [];
                $dataParameters['floorsCount'] = (int) $realty['floors-total'];
                $dataParameters['livingArea'] = !empty($realty->xpath('//living-space')) ? (int) $realty->xpath('//living-space')[0]->value : 0;
                $dataParameters['floorNumber'] = (int) $realty->floor;
                $dataParameters['flatRoomsCount'] = (int) $realty->rooms;
                $dataParameters['totalArea'] = !empty($realty->xpath('//area')) ? (int) $realty->xpath('//area')[0]['value'] : 0;
                $dataParameters['kitchenArea'] = !empty($realty->xpath('//kitchen-space')) ? (int) $realty->xpath('//kitchen-space')[0]['value'] : 0;
                $dataParameters['materialType'] = !empty($realty->xpath('//building-type')) ?  (int) $realty->xpath('//building-type')[0]['value'] : 0;
                $rooms = RealtyParameter::where('value', $dataParameters['flatRoomsCount'] . ' комнатная')->first();
                $livingArea = RealtyParameter::where('sort', $dataParameters['livingArea'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'zilaya-ploshhad'  . '-bye');
                })->first();

                $kitArea = RealtyParameter::where('sort', $dataParameters['kitchenArea'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'ploshhad-kuxni'  . '-bye');
                })->first();
                $totalArea = RealtyParameter::where('sort', $dataParameters['totalArea'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'obshhaya-ploshhad'  . '-bye');
                })->first();
                $floor = RealtyParameter::where('sort', $dataParameters['floorNumber'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'etaz'  . '-bye');
                })->first();
                $floorsInHouse = RealtyParameter::where('sort', $dataParameters['floorsCount'])->whereHas('filter', function ($q) {
                    $q->where('alias', 'vsego-etazei'  . '-bye');
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









        //        Realty::insert($resultNew->toArray());
//        Realty::upsert($resultUpdate->toArray(), 'id', [
//            'id',
//            'title',
//            'name',
//            'state',
//            'price',
//            'sale_price',
//            'description',
//            'profile_id',
//            'city_id',
//            'latitude',
//            'longitude',
//            'street',
//            'house',
//            'category_id',
//        ]);
//
//        dd($resultUpdate, $resultNew);
    }
}
