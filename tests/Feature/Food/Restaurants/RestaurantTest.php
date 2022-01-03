<?php

namespace Tests\Feature\Food\Restaurants;

use App\Models\FoodCategoryRestaurant;
use App\Models\FoodRestaurant;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group restaurant
 * @group ci
 * */
class RestaurantTest extends TestCase
{
    use DatabaseTransactions;

    public function testRestaurantIndex()
    {
        $response = $this->get(route('restaurants.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'food_restaurants'
        ]);
    }

    public function testRestaurantShow()
    {
        $restaurant = FoodRestaurant::factory()->create();

        $response = $this->get(route('restaurants.show', $restaurant->id ));
        $response->assertStatus(200);
    }

    public function testRestaurantShow404()
    {
        $restaurant = FoodRestaurant::factory()->create();

        $response = $this->get(route('restaurants.show', $restaurant->id . $restaurant->id ));
        $response->assertStatus(404);
    }

    public function testStoreRestaurant()
    {
        $category = FoodCategoryRestaurant::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('restaurants.store'), [
                'name' => 'test',
                'categoryRestaurantID' => [$category->id],
            ]);

        $id = explode('/restaurants/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('food_restaurants', [ 'id' => $id[1] ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * @group restaurant1
     * @group ci
     * */
    public function testDestroyRestaurant()
    {
        $restaraunt = FoodRestaurant::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('restaurants.destroy',[$restaraunt->id]), []);

        $this->assertNull(FoodRestaurant::find($restaraunt->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
