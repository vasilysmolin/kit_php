<?php

namespace Tests\Feature\Service;

use App\Models\Service;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group services
 * @group ci
 * */
class ServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function testServiceIndex()
    {
        $response = $this->get(route('services.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'services',
        ]);
    }

    public function testServiceShow()
    {
        $categoryServices = Service::factory()->create();

        $response = $this->get(route('services.show', [$categoryServices->id]));

        $response->assertStatus(200);
    }

    public function testServiceShow404()
    {
        $categoryServices = Service::factory()->create();

        $response = $this->get(route('services.show', $categoryServices->id . $categoryServices->id));
        $response->assertStatus(404);
    }

    public function testStoreService()
    {

        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('services.store'), [
                'name' => 'test',
            ]);

        $id = explode('/services/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('services', [ 'id' => $id[1] ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyService()
    {
        $categoryResume = Service::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('services.destroy', [$categoryResume->id]), []);

        $this->assertNull(Service::find($categoryResume->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
