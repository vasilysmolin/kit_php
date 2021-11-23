<?php

namespace Tests\Feature\FoodDishes;

use App\Models\FoodRestaurant;
use App\Models\FoodRestaurantDishes;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group dishes
 * @group ci
 * */
class DishesTest extends TestCase
{
    use DatabaseTransactions;

    public function testDishesIndex()
    {
        $restaurant = FoodRestaurant::factory()->create();
        $response = $this->get(route('restaurants.dishes.index', $restaurant->id));
        $response->assertStatus(200)->assertJsonStructure([
            'dishes'
        ]);
    }

    public function testDishesShow()
    {
        $user =         User::factory(1)
            ->has(Profile::factory(1)
                ->has(FoodRestaurant::factory(1)
                    ->has(FoodRestaurantDishes::factory(5))))
            ->create();
        $id = $user->first()->profile->restaurant->first()->dishes->first()->id;

        $response = $this->get(route('dishes.show', $id ));
        $response->assertStatus(200);
    }

    public function testDishesShow404()
    {
        $restaurant = FoodRestaurant::factory()->create();

        $response = $this->get(route('dishes.show', $restaurant->id . $restaurant->id ));
        $response->assertStatus(404);
    }


    public function testStoreDishes()
    {
//        $category = FoodCategoryRestaurant::factory()->create();
        $restaurant = FoodRestaurant::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('restaurants.dishes.store', $restaurant->id), [
                'name' => 'test',
                'price' => 1000,
//                'categoryRestaurantID' => [$category->id],
            ]);

        $id = explode('/dishes/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('food_dishes', [ 'id' => $id[1] ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }


    public function testDestroyDishes()
    {
        $user =         User::factory(1)
            ->has(Profile::factory(1)
                ->has(FoodRestaurant::factory(1)
                    ->has(FoodRestaurantDishes::factory(5))))
            ->create();

        $access_token = JWTAuth::fromUser($user->first());
        $id = $user->first()->profile->restaurant->first()->dishes->first()->id;

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('dishes.destroy', [$id]), []);

        $this->assertNull(FoodRestaurantDishes::find($id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
