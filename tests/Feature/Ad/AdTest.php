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

    public function testServiceShow404()
    {
        $categoryServices = CatalogAd::factory()->create();

        $response = $this->get(route('declarations.show', $categoryServices->id . $categoryServices->id));
        $response->assertStatus(404);
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

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyCatalogAd()
    {
        $catalogAd = CatalogAd::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('declarations.destroy', [$catalogAd->id]), []);

        $this->assertNull(CatalogAd::find($catalogAd->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
