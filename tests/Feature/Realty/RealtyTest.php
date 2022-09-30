<?php

namespace Tests\Feature\Realty;

use App\Models\Realty;
use App\Models\RealtyCategory;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group realty
 * @group ci
 * */
class RealtyTest extends TestCase
{
    use DatabaseTransactions;

    public function testRealtyIndex()
    {
        $response = $this->get(route('realties.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'realties',
        ]);
    }

    public function testRealtyShow()
    {
        $realty = Realty::factory()->create();

        $response = $this->get(route('realties.show', [$realty->id]));

        $response->assertStatus(200);
    }

    public function testCatalogShow404()
    {
//        $realty = Realty::factory()->create();
        $response = $this->get(route('realties.show', "00"));
        $response->assertStatus(404);
    }

    public function testCatalogUpdate()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $realty = Realty::factory(2)->create()->first();
        $realty->sort = 1;
        $realty->profile_id = $user->profile->getKey();
        $realty->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('realties.update', $realty->getKey()), [
            'name' => 'newName',
        ]);
        $realty = Realty::find($realty->id);
        $this->assertEquals('newName', $realty->name);
        $this->assertEquals($realty->sort, 1);
        $response->assertStatus(204);
    }

    public function testCatalogState()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $realty = Realty::factory(2)->create()->first();
        $realty->sort = 1;
        $realty->profile_id = $user->profile->getKey();
        $realty->update();
        $sort = $realty->sort;
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('realties.state', $realty->id), [
            'state' => 'pause',
        ]);
        $realty = Realty::find($realty->id);
        $this->assertEquals('pause', $realty->state);
        $this->assertNotEquals($realty->sort, $sort);
        $response->assertStatus(204);
    }

    public function testStoreRealty()
    {

        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $cat = RealtyCategory::factory()->create()->first();

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('realties.store'), [
                'name' => 'test',
                'price' => 3000,
                'category_id' => $cat->getKey(),
            ]);
        $id = explode('/realties/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('realties', [ 'id' => $id[1] ]);
        $realty = Realty::find($id[1]);
        $this->assertEquals(1, $realty->sort);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyRealty()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $realty = Realty::factory()->create();
        $realty->profile_id = $user->profile->getKey();

        $realty->update();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('realties.destroy', [$realty->id]), []);

        $this->assertNull(Realty::find($realty->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testRestoreRealty()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $realty = Realty::factory(4)->create()->first();
        $realty->profile_id = $user->profile->getKey();

        $realty->update();
        $access_token = JWTAuth::fromUser($user);
        $realty->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('realties.restore', [$realty->id]), []);

        $this->assertDatabaseHas('realties', [ 'id' => $realty->id ]);
        $realtySort = Realty::orderBy('sort', 'ASC')->first();
        $this->assertEquals($realtySort->getKey(), $realty->id);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testSortRealty()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $realty = Realty::factory()->create();
        $realty->profile_id = $user->profile->getKey();

        $realty->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('realties.sort', [$realty->id]), []);
        $realty = Realty::find($realty->id);
        $this->assertEquals(1, $realty->sort);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testStoreRealtyAdmin()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $cat = RealtyCategory::factory()->create()->first();;
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $userTwo = User::factory()->has(Profile::factory())->create();
        $response = $this
            ->withToken($access_token)
            ->json('POST', route('realties.store'), [
                'name' => 'test',
                'price' => 3000,
                'category_id' => $cat->getKey(),
                'profile_id' => $userTwo->profile->getKey(),
            ]);
        $id = explode('/realties/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('realties', [ 'id' => $id[1] ]);
        $realty = Realty::find($id[1]);
        $this->assertEquals(1, $realty->sort);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyRealtyAdmin()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $realty = Realty::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('realties.destroy', [$realty->id]), []);

        $this->assertNull(Realty::find($realty->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testRealtyUpdateAdmin()
    {
        $realty = Realty::factory(2)->create()->first();
        $realty->sort = 1;
        $realty->update();
        $sort = $realty->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('realties.update', $realty->id), [
            'name' => 'newName',
        ]);
        $realty = Realty::find($realty->id);
        $this->assertEquals('newName', $realty->name);
        $this->assertEquals($realty->sort, $sort);
        $response->assertStatus(204);
    }

    public function testRealtyStateAdmin()
    {
        $realty = Realty::factory(2)->create()->first();
        $realty->sort = 1;
        $realty->update();
        $sort = $realty->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('realties.state', $realty->id), [
            'state' => 'new',
        ]);
        $realty = Realty::find($realty->id);
        $this->assertEquals('new', $realty->state);
        $this->assertNotEquals($realty->sort, $sort);
        $response->assertStatus(204);
    }

    public function testRealtyUpdate403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $realty = Realty::factory(2)->create()->first();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('realties.update', $realty->id), [
            'name' => 'newName',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDestroyRealty403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $realty = Realty::factory()->create()->first();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('realties.destroy', [$realty->id]), []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testRestoreRealty403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $realty = Realty::factory(2)->create()->first();
        $access_token = JWTAuth::fromUser($user);
        $realty->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('realties.restore', [$realty->id]), []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testSortRealty403()
    {
        $realty = Realty::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('realties.sort', [$realty->id]), []);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testRealtyState403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $realty = Realty::factory(2)->create()->first();

        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('realties.state', $realty->id), [
            'state' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testRealtyState403byState()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $realty = Realty::factory(2)->create()->first();
        $realty->profile_id = $user->profile->getKey();
        $realty->update();

        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('realties.state', $realty->id), [
            'state' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testRealtyIndexCabinet()
    {
        $count = 1;
        $user = User::factory()->has(Profile::factory())->create();
        Realty::factory(4)->create()->first();
        $realty = Realty::factory($count)->create()->first();
        $realty->profile_id = $user->profile->getKey();
        $realty->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->get(route('realties.index', ['from' => 'cabinet']));
        $response->assertStatus(200)->assertJsonStructure([
            'realties',
        ]);
        $this->assertEquals($count, count(json_decode($response->getContent(), true)['realties']));
    }

    public function testRealtyShowCabinet()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $realty = Realty::factory()->create();
        $realty->profile_id = $user->profile->getKey();
        $realty->update();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->get(route('realties.show', [$realty->id, 'from' => 'cabinet']));
        $response->assertStatus(200);
    }

    public function testRealtyShowCabinet404()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $realty = Realty::factory(3)->create()->first();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->get(route('realties.show', [$realty->id, 'from' => 'cabinet']));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
