<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Region;
use App\Models\Timezone;
use Maatwebsite\Excel\Facades\Excel;
use morphos\Russian\GeographicalNamesInflection;
use Illuminate\Console\Command;

class CreateCitiesDadataCommand extends Command
{
    protected $signature = 'cities-parser-dadata';

    protected $description = 'cities-parser';

    public function handle(): void
    {


        $citiesDB = City::all();
        $regionsDB = Region::all();
        $timezoneDB = Timezone::all();

        $collect = Excel::toCollection(null, base_path() . '/region.csv', null, \Maatwebsite\Excel\Excel::CSV);
        $regions = $collect->first()->splice(1);
        $regions->each(function ($item) use ($regionsDB) {

//            $temp = strtolower($item[0]);
//            $cityName = ucfirst($temp);
            $alias = mb_strtolower($item[2]);
            $alias = preg_replace("/\s/", '-', $alias);
            $alias = preg_replace("/\//", '-', $alias);
            $alias = preg_replace("/\./", '-', $alias);
            $aliasPrepare = preg_replace(
                '/[^a-zA-Zа-яА-Я0-9]/ui',
                '',
                $alias
            );
            $alias = strtr(
                $aliasPrepare,
                array('а' => 'a','б' => 'b','в' => 'v',
                    'г' => 'g','д' => 'd','е' => 'e',
                    'ё' => 'e','ж' => 'j','з' => 'z','и' => 'i',
                    'й' => 'y','к' => 'k','л' => 'l','м' => 'm',
                    'н' => 'n','о' => 'o','п' => 'p','р' => 'r',
                    'с' => 's','т' => 't','у' => 'u','ф' => 'f',
                    'х' => 'h','ц' => 'c','ч' => 'ch','ш' => 'sh',
                    'щ' => 'shch','ы' => 'y','э' => 'e','ю' => 'yu',
                'я' => 'ya',
                'ъ' => '',
                'ь' => '')
            );
            $dublicate = $regionsDB->where('alias', $alias);
            $i = 1;
            while ($dublicate->count() > 0) {
                $alias .= '-' . $i;
                $dublicate = $regionsDB->where('alias', $alias);
                $i += 1;
            }

            $model = new Region();
            $model->country_id = 1;
            $model->name = $item[0];
            $model->active = 1;
            $model->alias = $alias;
            $model->full_name = $item[2];
            $model->kladr_id = $item[4];
            $model->fias_id = $item[5];
            $model->postal_code = $item[9];

            $model->save();
        });
        $collect = Excel::toCollection(null, base_path() . '/city.csv', null, \Maatwebsite\Excel\Excel::CSV);

        $bar = $this->output->createProgressBar($collect->first()->count());
        $bar->start();
        $cities = $collect->first()->splice(1);

        $cities->each(function ($item) use ($bar, $citiesDB, $timezoneDB, $regionsDB) {
            $bar->advance();
            if (empty($item[9])) {
                $item[9] = $item[5];
            }
            $temp = strtolower($item[9]);
            $cityName = ucfirst($temp);
            $alias = mb_strtolower($item[9]);
            $alias = preg_replace("/\s/", '-', $alias);
            $alias = preg_replace("/\//", '-', $alias);
            $alias = preg_replace("/\./", '-', $alias);
            $aliasPrepare = preg_replace(
                '/[^a-zA-Zа-яА-Я0-9]/ui',
                '',
                $alias
            );
            $alias = strtr(
                $aliasPrepare,
                array('а' => 'a','б' => 'b','в' => 'v',
                        'г' => 'g','д' => 'd','е' => 'e',
                        'ё' => 'e','ж' => 'j','з' => 'z','и' => 'i',
                        'й' => 'y','к' => 'k','л' => 'l','м' => 'm',
                        'н' => 'n','о' => 'o','п' => 'p','р' => 'r',
                        'с' => 's','т' => 't','у' => 'u','ф' => 'f',
                        'х' => 'h','ц' => 'c','ч' => 'ch','ш' => 'sh',
                        'щ' => 'shch','ы' => 'y','э' => 'e','ю' => 'yu',
                'я' => 'ya',
                'ъ' => '',
                'ь' => '')
            );
            $dublicate = $citiesDB->where('alias', $alias);
            $i = 1;
            while ($dublicate->count() > 0) {
                $alias .= '-' . $i;
                $dublicate = $citiesDB->where('alias', $alias);
                $i += 1;
            }
            $timeZones = substr($item[19], 3);
            $timeZone = $timezoneDB->where('regular', $timeZones)->first();
            if ($cityName === 'Орёл') {
                $cityDB = $citiesDB->where('name', 'Орел');
            } else {
                $cityDB = $citiesDB->where('name', $cityName);
            }

            $region = $regionsDB->where('name', $item[5])->first();
            $namePrepositional = GeographicalNamesInflection::getCase($cityName, 'предложный');

            if ($cityDB->count() > 0) {
                $model = City::where('id', $cityDB->first()->getKey())->first();
                $model->longitude = $item[21];
                $model->latitude = $item[20];
                $model->postal_code = $item[1];
                $model->kladr_id =  $item[12];
                $model->fias_id = $item[13];
                $model->population = $item[22];
                if (isset($region)) {
                    $model->region_id = $region->getKey();
                    $model->update();
                }
            } else {
                $model = new City();
                $model->country_id = 1;
                $model->timezone_id = $timeZone->getKey();
                $model->name = $cityName;
                $model->prepositionalName = $namePrepositional;
                $model->alias = $alias;
                $model->longitude = $item[21];
                $model->latitude = $item[20];
                $model->postal_code = $item[1];
                $model->kladr_id = $item[12];
                $model->fias_id = $item[13];
                $model->population = $item[22];
                $model->active = 1;
                if (isset($region)) {
                    $model->region_id = $region->getKey();
                    $model->save();
                }
                $model->save();
                $citiesDB->push($model);
            }
        });

        $bar->finish();
    }
}
