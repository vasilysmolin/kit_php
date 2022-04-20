<?php

namespace Tests\Feature\Ad;

use App\Models\CatalogAd;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group ads
 * @group ci
 * */
class AdTest extends TestCase
{
    use DatabaseTransactions;

    public function testCatalogAdIndex()
    {
        $response = $this->get(route('declarations.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'catalog_ads',
        ]);
    }

    public function testCatalogAdShow()
    {
        $categoryServices = CatalogAd::factory()->create();

        $response = $this->get(route('declarations.show', [$categoryServices->id]));

        $response->assertStatus(200);
    }

    public function testCatalogShow404()
    {
        $categoryServices = CatalogAd::factory()->create();

        $response = $this->get(route('declarations.show', $categoryServices->id . $categoryServices->id));
        $response->assertStatus(404);
    }

    public function testCatalogUpdate()
    {
        $ad = CatalogAd::factory(2)->create()->first();
        $ad->sort = 1;
        $ad->update();
        $sort = $ad->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('declarations.update', $ad->id), [
            'name' => 'newName',
        ]);
        $ad = CatalogAd::find($ad->id);
        $this->assertEquals('newName', $ad->name);
        $this->assertNotEquals($ad->sort, $sort);
        $response->assertStatus(204);
    }

    public function testCatalogState()
    {
        $ad = CatalogAd::factory(2)->create()->first();
        $ad->sort = 1;
        $ad->update();
        $sort = $ad->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('declarations.state', $ad->id), [
            'state' => 'new',
        ]);
        $ad = CatalogAd::find($ad->id);
        $this->assertEquals('new', $ad->state);
        $this->assertNotEquals($ad->sort, $sort);
        $response->assertStatus(204);
    }

    public function testStoreCatalogAd()
    {

        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('declarations.store'), [
                'name' => 'test',
            ]);

        $id = explode('/ads/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('catalog_ads', [ 'id' => $id[1] ]);
        $catalogAd = CatalogAd::find($id[1]);
        $this->assertEquals(1, $catalogAd->sort);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyCatalogAd()
    {
        $catalogAd = CatalogAd::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('declarations.destroy', [$catalogAd->id]), []);

        $this->assertNull(CatalogAd::find($catalogAd->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testRestoreCatalogAd()
    {
        $catalogAd = CatalogAd::factory(4)->create()->first();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $catalogAd->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('declarations.restore', [$catalogAd->id]), []);

        $this->assertDatabaseHas('catalog_ads', [ 'id' => $catalogAd->id ]);
        $catalogAdSort = CatalogAd::orderBy('sort', 'ASC')->first();
        $this->assertEquals($catalogAdSort->getKey(), $catalogAd->id);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testSortCatalogAd()
    {
        $catalogAd = CatalogAd::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('declarations.sort', [$catalogAd->id]), []);
        $catalogAd = CatalogAd::find($catalogAd->id);
        $this->assertEquals(1, $catalogAd->sort);
        $response->assertStatus(Response::HTTP_OK);
    }
}
