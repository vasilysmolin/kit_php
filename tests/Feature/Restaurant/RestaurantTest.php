<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

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

//    /**
//     * @group restaurant1
//     * @group ci
//     * */
//    public function testStoreRestaurant()
//    {
//        $category = CategoryFood::factory()->create();
//        $user = User::factory()->create();
//        $this->actingAs($user)->withToken('123');
//
//        $response = $this
//            ->withToken('123')
//            ->json('POST', route('restaurants.store'), [
//                'name' => 'test',
//                'alias' => 'test',
//                'category_id' => $category->id,
//            ]);
//        dd($response->getContent());
//        $order = Order::latest()->first();
//        dd($order);
//
//
//        $response->assertStatus(Response::HTTP_CREATED);
//    }
}
