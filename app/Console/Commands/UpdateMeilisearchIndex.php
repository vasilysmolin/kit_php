<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MeiliSearch\Client;
use MeiliSearch\Exceptions\ApiException;

class UpdateMeilisearchIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Meilisearch index and filterable attributes';

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
        $client = new Client(config('scout.meilisearch.host'));

        $this->createIndexes($client);

        $this->updateSortableAttributes($client);

        $this->updateFilterableAttributes($client);

        $this->updateSearchableAttributes($client);

        $this->updateRankingRules($client);

        return Command::SUCCESS;
    }

    protected function createIndexes(Client $client): void
    {
        try {
            $client->createIndex('catalog_ads');
        } catch (ApiException $exception) {
        }
        try {
            $client->createIndex('vacancies');
        } catch (ApiException $exception) {
        }
        try {
            $client->createIndex('resumes');
        } catch (ApiException $exception) {
        }
        try {
            $client->createIndex('services');
        } catch (ApiException $exception) {
        }
        try {
            $client->createIndex('cities');
        } catch (ApiException $exception) {
        }
        try {
            $client->createIndex('catalog_ad_categories');
        } catch (ApiException $exception) {
        }
        try {
            $client->createIndex('realty_categories');
        } catch (ApiException $exception) {
        }
        try {
            $client->createIndex('realties');
        } catch (ApiException $exception) {
        }
        try {
            $client->createIndex('journal');
        } catch (ApiException $exception) {
        }

        $this->info('Indexes create...');
    }

    protected function updateSortableAttributes(Client $client): void
    {
        $client->index('catalog_ads')->updateSortableAttributes([
            'name',
            'sort',
        ]);

        $client->index('realties')->updateSortableAttributes([
            'name',
            'sort',
        ]);

        $client->index('services')->updateSortableAttributes([
            'name',
            'sort',
        ]);

        $client->index('vacancies')->updateSortableAttributes([
            'name',
            'sort',
        ]);

        $client->index('resumes')->updateSortableAttributes([
            'name',
            'sort',
        ]);

        $client->index('catalog_ad_categories')->updateSortableAttributes([
            'name',
            'sort',
        ]);

        $client->index('realty_categories')->updateSortableAttributes([
            'name',
            'sort',
        ]);


        $client->index('cities')->updateSortableAttributes([
            'name',
            'sort',
        ]);

        $client->index('journal')->updateSortableAttributes([
            'name',
            'sort',
            'id',
        ]);

        $this->info('Updated sortable attributes...');
    }

    protected function updateSearchableAttributes(Client $client): void
    {
        $client->index('catalog_ads')->updateSearchableAttributes([
            'name',
            'description',
            'filter',
            'street',
        ]);

        $client->index('realties')->updateSearchableAttributes([
            'name',
            'description',
            'filter',
            'street',
        ]);

        $client->index('services')->updateSearchableAttributes([
            'name',
            'description',
        ]);

        $client->index('resumes')->updateSearchableAttributes([
            'name',
            'description',
            'education',
            'duties',
            'demands',
            'additionally',
        ]);

        $client->index('vacancies')->updateSearchableAttributes([
            'name',
            'description',
            'education',
            'duties',
            'demands',
            'additionally',
        ]);

        $client->index('journal')->updateSearchableAttributes([
            'name',
            'description',
            'category.name',
        ]);

        $client->index('catalog_ad_categories')->updateSearchableAttributes([
            'name',
        ]);

        $client->index('realty_categories')->updateSearchableAttributes([
            'name',
        ]);

        $client->index('cities')->updateSearchableAttributes([
            'name',
        ]);

        $this->info('Updated searchable attributes...');
    }

    protected function updateRankingRules(Client $client): void
    {
        $client->index('catalog_ads')->updateRankingRules([
            'name',
            'description',
            'filter',
            'street',
        ]);

        $client->index('realties')->updateRankingRules([
            'name',
            'description',
            'filter',
            'street',
        ]);

        $client->index('services')->updateRankingRules([
            'name',
            'description',
        ]);

        $client->index('resumes')->updateRankingRules([
            'name',
            'description',
            'education',
            'duties',
            'demands',
            'additionally',
        ]);

        $client->index('vacancies')->updateRankingRules([
            'name',
            'description',
            'education',
            'duties',
            'demands',
            'additionally',
        ]);

        $client->index('journal')->updateRankingRules([
            'name',
            'description',
            'category.name',
        ]);

        $client->index('catalog_ad_categories')->updateRankingRules([
            'name',
        ]);


        $client->index('realty_categories')->updateRankingRules([
            'name',
        ]);

        $client->index('cities')->updateRankingRules([
            'name',
        ]);

        $this->info('Updated ranking rules...');
    }

    protected function updateFilterableAttributes(Client $client): void
    {
        $client->index('catalog_ads')->updateFilterableAttributes([
            'state',
            'name',
        ]);

        $client->index('realties')->updateFilterableAttributes([
            'state',
            'name',
        ]);

        $client->index('catalog_ad_categories')->updateFilterableAttributes([
            'active',
            'name',
        ]);

        $client->index('realty_categories')->updateFilterableAttributes([
            'active',
            'name',
        ]);

        $client->index('services')->updateFilterableAttributes([
            'name',
            'state',
            'description',
        ]);

        $client->index('journal')->updateFilterableAttributes([
            'category.id',
            'profile_id',
        ]);

        $client->index('resumes')->updateFilterableAttributes([
            'name',
            'state',
            'description',
        ]);

        $client->index('vacancies')->updateFilterableAttributes([
            'name',
            'state',
            'description',
        ]);

        $client->index('cities')->updateFilterableAttributes([
            'active',
            'name',
        ]);

        $this->info('Updated filterable attributes...');
    }
}
