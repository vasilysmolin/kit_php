<?php

namespace Tests\Feature\Service;

use App\Models\ServiceCategory;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group category-services
 * @group ci
 * */
class ServiceCategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testServiceCategoryIndex()
    {
        $response = $this->get(route('category-services.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'category_services',
        ]);
    }

    public function testServiceCategoryShow()
    {
        $categoryServices = ServiceCategory::factory()->create();

        $response = $this->get(route('category-services.show', [$categoryServices->id]));

        $response->assertStatus(200);
    }

    public function testServiceCategoryShow404()
    {
        $categoryServices = ServiceCategory::factory()->create();

        $response = $this->get(route('category-services.show', $categoryServices->id . $categoryServices->id));
        $response->assertStatus(404);
    }

    public function testStoreServiceCategory()
    {

        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('category-services.store'), [
                'name' => 'test',
            ]);

        $id = explode('/category-services/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('service_categories', [ 'id' => $id[1] ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyServiceCategory()
    {
        $categoryResume = ServiceCategory::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('category-services.destroy', [$categoryResume->id]), []);

        $this->assertNull(ServiceCategory::find($categoryResume->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
