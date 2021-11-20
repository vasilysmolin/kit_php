@component('mail::message')
# Спасибо, что выбрали наш интернет-магазин

В ближайшее время с
вами свяжется наш менеджер для уточнения
деталей заказа № {{$order->id}}

## Ваши данные

- Имя: {{ "$order->name $order->surname $order->patronymic" }}
- Телефон: {{$order->phone}}
- Email: {{$order->email}}

@component('mail::table')
    @php
        $sum = 0;
    @endphp
    @foreach($order->orderRestaurant as $rest)

        @foreach($rest->orderDishes as $orFood)
            @php
              $sum = $sum + $orFood->salePrice * $orFood->quantity;
            @endphp
        @endforeach

     Название ресторана: **{{$rest->restaurant->name}}**

 | Товар         | Кол-во        | Цена     |
 | ------------- |:-------------:| --------:|
        @foreach($rest->orderDishes as $orFood)
 | {{$orFood->dishes->name}} - {{$orFood->salePrice}} руб/шт  | {{$orFood->quantity}} | {{$orFood->salePrice * $orFood->quantity}} |
        @endforeach
 | **Итого** | **{{$rest->orderDishes->pluck('quantity')->sum() }}** | **{{ $sum }}** |
    @endforeach

@endcomponent


@component('mail::button', ['url' => 'https://user.tapigo.ru'])
В личный кабинет
@endcomponent

{{--Компания,<br>--}}
{{--{{ config('app.name') }}--}}
@endcomponent
