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
        $services = Service::factory()->create();

        $response = $this->get(route('services.show', [$services->id]));

        $response->assertStatus(200);
    }

    public function testServiceShow404()
    {
        $services = Service::factory()->create();

        $response = $this->get(route('services.show', $services->id . $services->id));
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
        $services = Service::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('services.destroy', [$services->id]), []);

        $this->assertNull(Service::find($services->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
    public function testRestoreVacancy()
    {
        $services = Service::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);
        $services->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('services.restore', [$services->id]), []);

        $this->assertDatabaseHas('services', [ 'id' => $services->id ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testSortVacancy()
    {
        $services = Service::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('services.sort', [$services->id]), []);
        $services = Service::find($services->id);
        $this->assertEquals(1, $services->sort);
        $response->assertStatus(Response::HTTP_OK);
    }

}
