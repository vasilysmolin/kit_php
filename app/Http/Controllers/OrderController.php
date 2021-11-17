<?php

namespace App\Http\Controllers;

use App\Mail\OrderShipped;
use App\Models\Order;
use App\Models\OrderFood;
use App\Models\OrderRestaurant;
use App\Models\Restaurant;
use App\Models\RestaurantFood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }


    public function store(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $user = auth('api')->user();
        DB::transaction(function () use ($content, $user) {
            $order = new Order();
            $order->name = $content['name'];
            $order->user_id = isset($user) ? $user->getAuthIdentifier() : null;
            $order->surname = $content['surname'] ?? null;
            $order->patronymic = $content['patronymic'] ?? null;
            $order->email = $content['email'] ?? null;
            $order->phone = $content['phone'] ?? null;
            $order->city_id = $content['city_id'] ?? null;

            if (isset($content['address']) && isset($content['address']['coords']) && is_array($content['address']['coords'])) {
                $order['latitude'] = $content['address']['coords'][0] ?? 0;
                $order['longitude'] = $content['address']['coords'][1] ?? 0;
            }

            if (isset($formData['address']) && isset($formData['address']['text'])) {
                $order['address'] = $formData['address']['text'];
            } else {
                $order['address'] = '';
            }
            $order->save();

            foreach ($content['order'] as $key => $val) {
                $orderRestaurant = new OrderRestaurant();
                $orderRestaurant->restaurant_id = $key;
                $orderRestaurant->order_id = $order->getKey();
                $orderRestaurant->save();
                $sum = 0;
                foreach ($val as $k => $v) {
                    $food = RestaurantFood::find($k);
                    $orderFood = new OrderFood();
                    $orderFood->order_restaurant_id = $orderRestaurant->getKey();
                    $orderFood->restaurant_food_id = $k;
                    $orderFood->quantity = $v;
                    $orderFood->price = $food->price;
                    $orderFood->salePrice = $food->salePrice;
                    $orderFood->save();
                    $sum = $sum + $orderFood->salePrice * $orderFood->quantity;
                }
                $order->sum = $sum;
                $restaurant = Restaurant::find($key);
                if (isset($restaurant) && isset($restaurant->user)) {
                    Mail::to($restaurant->user->email)->queue(new OrderShipped($order));
                }
            }

            Mail::to($order->email)->queue(new OrderShipped($order));
        }, 3);



        return response()->json([], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
