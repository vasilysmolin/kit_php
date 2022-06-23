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

        return Command::SUCCESS;
    }

    protected function createIndexes(Client $client): void
    {
        try {
            $client->createIndex('catalog_ads');
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

        $this->info('Indexes create...');
    }

    protected function updateSortableAttributes(Client $client): void
    {
        $client->index('catalog_ads')->updateSortableAttributes([
            'name',
            'sort',
        ]);

        $client->index('catalog_ad_categories')->updateSortableAttributes([
            'name',
            'sort',
        ]);


        $client->index('cities')->updateSortableAttributes([
            'name',
            'sort',
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

        $client->index('catalog_ad_categories')->updateSearchableAttributes([
            'name',
        ]);


        $client->index('cities')->updateSearchableAttributes([
            'name',
        ]);

        $this->info('Updated searchable attributes...');
    }

    protected function updateFilterableAttributes(Client $client): void
    {
        $client->index('catalog_ads')->updateFilterableAttributes([
            'state',
            'name',
        ]);

        $client->index('catalog_ad_categories')->updateFilterableAttributes([
            'active',
            'name',
        ]);

        $client->index('cities')->updateFilterableAttributes([
            'active',
            'name',
        ]);

        $this->info('Updated filterable attributes...');
    }
}
