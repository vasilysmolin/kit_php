<?php

namespace Tests\Feature\Ad;

use App\Models\CatalogAdCategory;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group category-declarations
 * @group ci
 * */
class AdCategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testCatalogAdCategoryIndex()
    {
        $response = $this->get(route('category-declarations.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'catalog_ad_categories',
        ]);
    }

    public function testServiceCategoryShow()
    {
        $categoryServices = CatalogAdCategory::factory()->create();

        $response = $this->get(route('category-declarations.show', [$categoryServices->id]));

        $response->assertStatus(200);
    }

    public function testCatalogAdCategoryShow404()
    {
        $categoryServices = CatalogAdCategory::factory()->create();

        $response = $this->get(route('category-declarations.show', $categoryServices->id . $categoryServices->id));
        $response->assertStatus(404);
    }

    public function testCatalogAdCategoryStore()
    {

        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('category-declarations.store'), [
                'name' => 'test',
            ]);

        $id = explode('/category-declarations/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('catalog_ad_categories', [ 'id' => $id[1] ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testCatalogAdCategoryDestroy()
    {
        $categoryResume = CatalogAdCategory::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('category-declarations.destroy', [$categoryResume->id]), []);

        $this->assertNull(CatalogAdCategory::find($categoryResume->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
