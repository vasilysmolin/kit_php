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
        $service = Service::factory()->create();

        $response = $this->get(route('services.show', [$service->id]));

        $response->assertStatus(200);
    }

    public function testServiceShow404()
    {
        $service = Service::factory()->create();

        $response = $this->get(route('services.show', $service->id . $service->id));
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
        $service = Service::find($id[1]);
        $this->assertEquals(1, $service->sort);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testVacancyUpdate()
    {
        $service = Service::factory(2)->create()->first();
        $service->sort = 1;
        $service->update();
        $sort = $service->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('services.update', $service->id), [
            'name' => 'newName',
        ]);
        $service = Service::find($service->id);
        $this->assertEquals('newName', $service->name);
        $this->assertNotEquals($service->sort, $sort);
        $response->assertStatus(204);
    }

    public function testVacancyState()
    {
        $service = Service::factory(2)->create()->first();
        $service->sort = 1;
        $service->update();
        $sort = $service->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('services.state', $service->id), [
            'state' => 'new',
        ]);
        $service = Service::find($service->id);
        $this->assertEquals('new', $service->state);
        $this->assertNotEquals($service->sort, $sort);
        $response->assertStatus(204);
    }

    public function testDestroyService()
    {
        $service = Service::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('services.destroy', [$service->id]), []);

        $this->assertNull(Service::find($service->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testRestoreService()
    {
        $service = Service::factory(4)->create()->first();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);
        $service->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('services.restore', [$service->id]), []);

        $this->assertDatabaseHas('services', [ 'id' => $service->id ]);
        $serviceSort = Service::orderBy('sort', 'ASC')->first();
        $this->assertEquals($serviceSort->getKey(), $service->id);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testSortVacancy()
    {
        $service = Service::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('services.sort', [$service->id]), []);
        $service = Service::find($service->id);
        $this->assertEquals(1, $service->sort);
        $response->assertStatus(Response::HTTP_OK);
    }

}
