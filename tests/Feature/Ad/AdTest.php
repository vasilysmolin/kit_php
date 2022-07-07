<?php

namespace Tests\Feature\Ad;

use App\Models\CatalogAd;
use App\Models\CatalogAdCategory;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;
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
        $ad = CatalogAd::factory()->create();

        $response = $this->get(route('declarations.show', [$ad->id]));

        $response->assertStatus(200);
    }

    public function testCatalogShow404()
    {
//        $ad = CatalogAd::factory()->create();
        $response = $this->get(route('declarations.show', "00"));
        $response->assertStatus(404);
    }

    public function testCatalogUpdate()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $ad = CatalogAd::factory(2)->create()->first();
        $ad->sort = 1;
        $ad->profile_id = $user->profile->getKey();
        $ad->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('declarations.update', $ad->getKey()), [
            'name' => 'newName',
        ]);
        $ad = CatalogAd::find($ad->id);
        $this->assertEquals('newName', $ad->name);
        $this->assertEquals($ad->sort, 1);
        $response->assertStatus(204);
    }

    public function testCatalogState()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $ad = CatalogAd::factory(2)->create()->first();
        $ad->sort = 1;
        $ad->profile_id = $user->profile->getKey();
        $ad->update();
        $sort = $ad->sort;
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('declarations.state', $ad->id), [
            'state' => 'pause',
        ]);
        $ad = CatalogAd::find($ad->id);
        $this->assertEquals('pause', $ad->state);
        $this->assertNotEquals($ad->sort, $sort);
        $response->assertStatus(204);
    }

    public function testStoreCatalogAd()
    {

        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $cat = CatalogAdCategory::factory()->create();

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('declarations.store'), [
                'name' => 'test',
                'price' => 3000,
                'category_id' => $cat->getKey(),
            ]);

        $id = explode('/declarations/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('catalog_ads', [ 'id' => $id[1] ]);
        $ad = CatalogAd::find($id[1]);
        $this->assertEquals(1, $ad->sort);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyCatalogAd()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $ad = CatalogAd::factory()->create();
        $ad->profile_id = $user->profile->getKey();

        $ad->update();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('declarations.destroy', [$ad->id]), []);

        $this->assertNull(CatalogAd::find($ad->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testRestoreCatalogAd()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $ad = CatalogAd::factory(4)->create()->first();
        $ad->profile_id = $user->profile->getKey();

        $ad->update();
        $access_token = JWTAuth::fromUser($user);
        $ad->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('declarations.restore', [$ad->id]), []);

        $this->assertDatabaseHas('catalog_ads', [ 'id' => $ad->id ]);
        $adSort = CatalogAd::orderBy('sort', 'ASC')->first();
        $this->assertEquals($adSort->getKey(), $ad->id);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testSortCatalogAd()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $ad = CatalogAd::factory()->create();
        $ad->profile_id = $user->profile->getKey();

        $ad->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('declarations.sort', [$ad->id]), []);
        $ad = CatalogAd::find($ad->id);
        $this->assertEquals(1, $ad->sort);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testStoreCatalogAdAdmin()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $cat = CatalogAdCategory::factory()->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $userTwo = User::factory()->has(Profile::factory())->create();
        $response = $this
            ->withToken($access_token)
            ->json('POST', route('declarations.store'), [
                'name' => 'test',
                'price' => 3000,
                'category_id' => $cat->getKey(),
                'profile_id' => $userTwo->profile->getKey(),
            ]);
        $id = explode('/declarations/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('catalog_ads', [ 'id' => $id[1] ]);
        $ad = CatalogAd::find($id[1]);
        $this->assertEquals(1, $ad->sort);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyCatalogAdAdmin()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $ad = CatalogAd::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('declarations.destroy', [$ad->id]), []);

        $this->assertNull(CatalogAd::find($ad->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testCatalogAdUpdateAdmin()
    {
        $ad = CatalogAd::factory(2)->create()->first();
        $ad->sort = 1;
        $ad->update();
        $sort = $ad->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('declarations.update', $ad->id), [
            'name' => 'newName',
        ]);
        $ad = CatalogAd::find($ad->id);
        $this->assertEquals('newName', $ad->name);
        $this->assertEquals($ad->sort, $sort);
        $response->assertStatus(204);
    }

    public function testCatalogAdStateAdmin()
    {
        $ad = CatalogAd::factory(2)->create()->first();
        $ad->sort = 1;
        $ad->update();
        $sort = $ad->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('declarations.state', $ad->id), [
            'state' => 'new',
        ]);
        $ad = CatalogAd::find($ad->id);
        $this->assertEquals('new', $ad->state);
        $this->assertNotEquals($ad->sort, $sort);
        $response->assertStatus(204);
    }

    public function testCatalogAdUpdate403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $ad = CatalogAd::factory(2)->create()->first();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('declarations.update', $ad->id), [
            'name' => 'newName',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDestroyCatalogAd403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $ad = CatalogAd::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('declarations.destroy', [$ad->id]), []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testRestoreCatalogAd403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $ad = CatalogAd::factory(2)->create()->first();
        $access_token = JWTAuth::fromUser($user);
        $ad->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('declarations.restore', [$ad->id]), []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testSortCatalogAd403()
    {
        $ad = CatalogAd::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('declarations.sort', [$ad->id]), []);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCatalogAdState403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $ad = CatalogAd::factory(2)->create()->first();

        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('declarations.state', $ad->id), [
            'state' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCatalogAdState403byState()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $ad = CatalogAd::factory(2)->create()->first();
        $ad->profile_id = $user->profile->getKey();
        $ad->update();

        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('declarations.state', $ad->id), [
            'state' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testStoreCatalogAd403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $userTwo = User::factory()->has(Profile::factory())->create();
        $response = $this
            ->withToken($access_token)
            ->json('POST', route('declarations.store'), [
                'name' => 'test',
                'max_price' => 1000,
                'min_price' => 100,
                'profile_id' => $userTwo->profile->getKey(),
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCatalogAdIndexCabinet()
    {
        $count = 1;
        $user = User::factory()->has(Profile::factory())->create();
        CatalogAd::factory(4)->create()->first();
        $ad = CatalogAd::factory($count)->create()->first();
        $ad->profile_id = $user->profile->getKey();
        $ad->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->get(route('declarations.index', ['from' => 'cabinet']));
        $response->assertStatus(200)->assertJsonStructure([
            'catalog_ads',
        ]);
        $this->assertEquals($count, count(json_decode($response->getContent(), true)['catalog_ads']));
    }

    public function testCatalogAdShowCabinet()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $ad = CatalogAd::factory()->create();
        $ad->profile_id = $user->profile->getKey();
        $ad->update();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->get(route('declarations.show', [$ad->id, 'from' => 'cabinet']));
        $response->assertStatus(200);
    }

    public function testCatalogAdShowCabinet404()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $ad = CatalogAd::factory(3)->create()->first();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->get(route('declarations.show', [$ad->id, 'from' => 'cabinet']));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
