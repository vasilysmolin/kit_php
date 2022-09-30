<?php

namespace Tests\Feature\Realty;

use App\Models\RealtyCategory;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group category-realty
 * @group ci
 * */
class RealtyCategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testRealtyCategoryIndex()
    {
        $response = $this->get(route('category-realties.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'realty_categories',
        ]);
    }

    public function testServiceCategoryShow()
    {
        $categoryServices = RealtyCategory::factory()->create();

        $response = $this->get(route('category-realties.show', [$categoryServices->id]));

        $response->assertStatus(200);
    }

    public function testRealtyCategoryShow404()
    {
        $categoryServices = RealtyCategory::factory()->create();

        $response = $this->get(route('category-realties.show', $categoryServices->id . $categoryServices->id));
        $response->assertStatus(404);
    }

    public function testRealtyCategoryStore()
    {

        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('category-realties.store'), [
                'name' => 'test',
                'color_id' => null,
            ]);
        $id = explode('/category-realties/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('realty_categories', [ 'id' => $id[1] ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testRealtyCategoryDestroy()
    {
        $categoryResume = RealtyCategory::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('category-realties.destroy', [$categoryResume->id]), []);

        $this->assertNull(RealtyCategory::find($categoryResume->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
