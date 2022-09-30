<?php

namespace App\Console\Commands;

use App\Models\CatalogAd;
use App\Models\CatalogAdCategory;
use App\Models\CatalogFilter;
use App\Models\CatalogParameter;
use App\Models\Realty;
use App\Models\RealtyCategory;
use App\Models\RealtyFilter;
use App\Models\RealtyParameter;
use Illuminate\Console\Command;

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
        $categoryMain = CatalogAdCategory::where('name', 'Недвижимость')->first();
        $categoriesID = $this->iter($categoryMain, []);
        $categories =  CatalogAdCategory::whereIn('id', $categoriesID)->get();
        $realtyCategories = RealtyCategory::all();
        if ($realtyCategories->isEmpty()) {
            RealtyCategory::insert($categories->toArray());
        }
        $ads = CatalogAd::whereIn('category_id', $categories->pluck('id')->toArray())->get();
        $realty = Realty::all();
        if ($realty->isEmpty()) {
            Realty::insert($ads->toArray());
        }
        $adFilter = CatalogFilter::whereIn('category_id', $categories->pluck('id')->toArray())->get();
        $realtyFilter = RealtyFilter::all();
        if ($realtyFilter->isEmpty()) {
            RealtyFilter::insert($adFilter->toArray());
        }
        $adParams = CatalogParameter::whereIn('filter_id', $adFilter->pluck('id')->toArray())->get();
        $realtyParameters = RealtyParameter::all();
        if ($realtyParameters->isEmpty()) {
            RealtyParameter::insert($adParams->toArray());
        }
//        $ads->each(function($ad){
//            dd($ad->adParameters->first()->pivot->parameter_id);
//        });
//        dd($ads);
    }

    private function iter(?CatalogAdCategory $item, array $acc): array
    {
        array_push($acc, $item->getKey());
        if (empty($item->categories)) {
            return array_values($acc);
        }
        return $item->categories->reduce(function ($carry, $category) {
            $carry[] = $category->getKey();
            $carry = array_unique($carry);
            return $this->iter($category, $carry);
        }, $acc);
    }
}
