<?php

namespace Tests\Feature\Color;

use App\Models\Color;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group colors
 * @group ci
 * */
class ColorTest extends TestCase
{
    use DatabaseTransactions;

    public function testColorIndex()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->get(route('colors.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'colors',
        ]);
    }

    public function testColorShow()
    {
        $color = Color::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->get(route('colors.show', [$color->id]));

        $response->assertStatus(200);
    }

    public function testCatalogShow404()
    {
        $color = Color::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->get(route('colors.show', "00"));
        $response->assertStatus(404);
    }

    public function testStoreColorAdmin()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('POST', route('colors.store'), [
                'name' => 'test',
                'hash' => '#fffff',
            ]);
        $id = explode('/colors/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('colors', [ 'id' => $id[1] ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyColorAdmin()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $color = Color::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('colors.destroy', [$color->id]), []);

        $this->assertNull(Color::find($color->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testColorUpdateAdmin()
    {
        $color = Color::factory(2)->create()->first();
        $color->update();
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('colors.update', $color->id), [
            'name' => 'newName',
            'hash' => '#000000',
        ]);
        $color = Color::find($color->id);
        $this->assertEquals('newName', $color->name);
        $this->assertEquals('#000000', $color->hash);
        $response->assertStatus(204);
    }
//
//    public function testColorUpdate403()
//    {
//        $user = User::factory()->has(Profile::factory())->create();
//        $color = Color::factory(2)->create()->first();
//        $access_token = JWTAuth::fromUser($user);
//        $response = $this->withToken($access_token)->put(route('colors.update', $color->id), [
//            'name' => 'newName',
//        ]);
//        $response->assertStatus(Response::HTTP_FORBIDDEN);
//    }
//
//    public function testDestroyColor403()
//    {
//        $user = User::factory()->has(Profile::factory())->create();
//        $color = Color::factory()->create();
//        $access_token = JWTAuth::fromUser($user);
//
//        $response = $this
//            ->withToken($access_token)
//            ->json('DELETE', route('colors.destroy', [$color->id]), []);
//
//        $response->assertStatus(Response::HTTP_FORBIDDEN);
//    }
//
//    public function testStoreColor403()
//    {
//        $user = User::factory()->has(Profile::factory())->create();
//        $access_token = JWTAuth::fromUser($user);
//        $response = $this
//            ->withToken($access_token)
//            ->json('POST', route('colors.store'), [
//                'name' => 'test',
//                'hash' => 1000,
//            ]);
//
//        $response->assertStatus(Response::HTTP_FORBIDDEN);
//    }

}
