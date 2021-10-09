<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderFood;
use App\Models\OrderRestaurant;
use App\Models\RestaurantFood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
//        dd($request->getContent());
        $content = json_decode($request->getContent(), true);

        DB::transaction(function () use ($content) {
            $order = new Order();
            $order->name = $content['name'];
            $order->user_id = auth('api')->user()->getAuthIdentifier();
            $order->surname = $content['surname'];
            $order->patronymic = $content['patronymic'];
            $order->patronymic = $content['patronymic'];
            $order->email = $content['email'];
            $order->phone = $content['phone'];
            $order->city_id = $content['city_id'];
            $order->address = $content['address'];
            $order->save();

            foreach ($content['order'] as $key => $val) {
                $orderRestaurant = new OrderRestaurant();
                $orderRestaurant->restaurant_id = $key;
                $orderRestaurant->order_id = $order->getKey();
                $orderRestaurant->save();
//                $orderRestaurant->createMany([
//                    ['message' => 'A new comment.'],
//                    ['message' => 'Another new comment.'],
//                ])
                foreach ($val as $k => $v) {
                    $food = RestaurantFood::find($k);
                    $orderFood = new OrderFood();
                    $orderFood->order_restaurant_id = $orderRestaurant->getKey();
                    $orderFood->restaurant_food_id = $k;
                    $orderFood->quantity = $v;
                    $orderFood->price = $food->price;
                    $orderFood->salePrice = $food->salePrice;
                    $orderFood->save();
                }
            }
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
