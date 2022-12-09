<?php

namespace App\Console\Commands;

use App\Models\CatalogAdCategory;
use App\Models\FilterParameter;
use App\Models\Realty;
use App\Models\RealtyCategory;
use App\Models\Filter;
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
         $filters = Filter::get();
         $filters->each(function($filter){
             $filter->categoryable_id = $filter->category_id;
             $filter->categoryable_type = RealtyCategory::class;
             $filter->update();
         });

        $filterParameters = FilterParameter::get();
        $filterParameters->each(function($filterParameter){
            $filterParameter->itemable_id = $filterParameter->realty_id;
            $filterParameter->itemable_type = Realty::class;
            $filterParameter->update();
        });



//        $categoryMain = CatalogAdCategory::where('name', 'Недвижимость')->first();
//        $categoriesID = $this->iter($categoryMain, []);
//        $categories =  CatalogAdCategory::whereIn('id', $categoriesID)->get();
//        CatalogAd::whereIn('category_id', $categories->pluck('id')->toArray())->delete();
//        $realtyCategories = RealtyCategory::all();
//        if ($realtyCategories->isEmpty()) {
//            RealtyCategory::insert($categories->toArray());
//        }
//        $ads = CatalogAd::whereIn('category_id', $categories->pluck('id')->toArray())->get();
//        $realty = Realty::all();
//        if ($realty->isEmpty()) {
//            Realty::insert($ads->toArray());
//        }
//        $ads->each(function ($ad) {
//            $images = $ad->images;
//            $images->each(function ($image) {
//                $image->imageable_type = Realty::class;
//                $image->update();
//            });
//        });
//        $adFilter = CatalogFilter::whereIn('category_id', $categories->pluck('id')->toArray())->get();
//        $realtyFilter = RealtyFilter::all();
//        if ($realtyFilter->isEmpty()) {
//            RealtyFilter::insert($adFilter->toArray());
//        }
//        $adParams = CatalogParameter::whereIn('filter_id', $adFilter->pluck('id')->toArray())->get();
//        $realtyParameters = RealtyParameter::all();
//        if ($realtyParameters->isEmpty()) {
//            RealtyParameter::insert($adParams->toArray());
//        }
//        $ads = CatalogAd::with('adParameters')->whereIn('category_id', $categories->pluck('id')->toArray())->get();
//
//        $ads->each(function ($ad) {
//            $params = $ad->adParameters->map(function ($ad) {
//                return $ad->pivot->parameter_id;
//            });
//            $realty = Realty::find($ad->getKey());
//            $realty->realtyParameters()->sync($params);
//        });
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
