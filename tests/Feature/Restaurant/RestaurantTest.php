<?php

namespace Tests\Feature\Restaurant;

use App\Models\CategoryRestaurant;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group restaurant
 * @group ci
 * */
class RestaurantTestTest extends TestCase
{
    use DatabaseTransactions;

    public function testRestaurantIndex()
    {
        $response = $this->get(route('restaurants.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'restaurants'
        ]);
    }

    public function testRestaurantShow()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.show', $restaurant->id ));
        $response->assertStatus(200);
    }

    public function testRestaurantShow404()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.show', $restaurant->id . $restaurant->id ));
        $response->assertStatus(404);
    }


    public function testStoreRestaurant()
    {
        $category = CategoryRestaurant::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('restaurants.store'), [
                'name' => 'test',
                'alias' => 'test',
                'category_restaurant_id' => [$category->id],
            ]);

        $id = explode('/restaurants/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('restaurants', [ 'id' => $id[1] ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * @group restaurant1
     * @group ci
     * */
    public function testDestroyRestaurant()
    {
        $restaraunt = Restaurant::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('restaurants.destroy',[$restaraunt->id]), []);

        $this->assertNull(Restaurant::find($restaraunt->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
