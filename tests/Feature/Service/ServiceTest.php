<?php

namespace Tests\Feature\Service;

use App\Models\Service;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;
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
        $user = User::factory()->has(Profile::factory())->create();
        $service = Service::factory(2)->create()->first();
        $service->profile_id = $user->profile->getKey();
        $service->sort = 1;
        $service->update();
        $sort = $service->sort;
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
        $user = User::factory()->has(Profile::factory())->create();
        $service = Service::factory(5)->create()->first();
        $service->sort = 1;
        $service->profile_id = $user->profile->getKey();
        $service->update();
        $sort = $service->sort;
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('services.state', $service->id), [
            'state' => 'pause',
        ]);
        $service = Service::find($service->id);
        $this->assertEquals('pause', $service->state);
        $this->assertNotEquals($service->sort, $sort);
        $response->assertStatus(204);
    }

    public function testDestroyService()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $service = Service::factory()->create();
        $service->profile_id = $user->profile->getKey();
        $service->update();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('services.destroy', [$service->id]), []);

        $this->assertNull(Service::find($service->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testRestoreService()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $service = Service::factory(4)->create()->first();
        $service->profile_id = $user->profile->getKey();
        $service->update();
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
        $user = User::factory()->has(Profile::factory())->create();
        $service = Service::factory()->create();
        $service->profile_id = $user->profile->getKey();
        $service->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('services.sort', [$service->id]), []);
        $service = Service::find($service->id);
        $this->assertEquals(1, $service->sort);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testStoreServiceAdmin()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $userTwo = User::factory()->has(Profile::factory())->create();
        $response = $this
            ->withToken($access_token)
            ->json('POST', route('services.store'), [
                'name' => 'test',
                'max_price' => 1000,
                'min_price' => 100,
                'profile_id' => $userTwo->profile->getKey(),
            ]);

        $id = explode('/services/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('services', [ 'id' => $id[1] ]);
        $service = Service::find($id[1]);
        $this->assertEquals(1, $service->sort);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyServiceAdmin()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $service = Service::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('services.destroy', [$service->id]), []);

        $this->assertNull(Service::find($service->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testServiceUpdateAdmin()
    {
        $service = Service::factory(2)->create()->first();
        $service->sort = 1;
        $service->update();
        $sort = $service->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('services.update', $service->id), [
            'name' => 'newName',
        ]);
        $service = Service::find($service->id);
        $this->assertEquals('newName', $service->name);
        $this->assertEquals($service->sort, $sort);
        $response->assertStatus(204);
    }

    public function testServiceStateAdmin()
    {
        $service = Service::factory(2)->create()->first();
        $service->sort = 1;
        $service->update();
        $sort = $service->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('services.state', $service->id), [
            'state' => 'new',
        ]);
        $service = Service::find($service->id);
        $this->assertEquals('new', $service->state);
        $this->assertNotEquals($service->sort, $sort);
        $response->assertStatus(204);
    }

    public function testServiceUpdate403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $service = Service::factory(2)->create()->first();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('services.update', $service->id), [
            'name' => 'newName',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDestroyService403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $service = Service::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('services.destroy', [$service->id]), []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testRestoreService403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $service = Service::factory(2)->create()->first();
        $access_token = JWTAuth::fromUser($user);
        $service->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('services.restore', [$service->id]), []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testSortService403()
    {
        $service = Service::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('services.sort', [$service->id]), []);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testServiceState403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $service = Service::factory(2)->create()->first();

        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('services.state', $service->id), [
            'state' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testServiceState403byState()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $service = Service::factory(2)->create()->first();
        $service->profile_id = $user->profile->getKey();
        $service->update();

        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('services.state', $service->id), [
            'state' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testStoreService403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $userTwo = User::factory()->has(Profile::factory())->create();
        $response = $this
            ->withToken($access_token)
            ->json('POST', route('services.store'), [
                'name' => 'test',
                'max_price' => 1000,
                'min_price' => 100,
                'profile_id' => $userTwo->profile->getKey(),
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testServiceIndexCabinet()
    {
        $count = 1;
        $user = User::factory()->has(Profile::factory())->create();
        Service::factory(4)->create()->first();
        $service = Service::factory($count)->create()->first();
        $service->profile_id = $user->profile->getKey();
        $service->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->get(route('services.index',['from'=> 'cabinet']));
        $response->assertStatus(200)->assertJsonStructure([
            'services',
        ]);
        $this->assertEquals($count, count(json_decode($response->getContent(), true)['services']));
    }

    public function testServiceShowCabinet()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $service = Service::factory()->create();
        $service->profile_id = $user->profile->getKey();
        $service->update();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->get(route('services.show', [$service->id, 'from' => 'cabinet']));
        $response->assertStatus(200);
    }

    public function testServiceShowCabinet404()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $service = Service::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->get(route('services.show', [$service->id, 'from' => 'cabinet']));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

}
