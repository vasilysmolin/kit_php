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

}
