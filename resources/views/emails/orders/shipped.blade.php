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
    @php($sum = 0)
    @foreach($order->orderRestaurant as $rest)

     Название ресторана: **{{$rest->restaurant->name}}**

 | Товар         | Кол-во        | Цена     |
 | ------------- |:-------------:| --------:|
        @foreach($rest->orderFood as $orFood)
 | {{$orFood->food->name}} - {{$orFood->salePrice}} руб/шт  | {{$orFood->quantity}} | {{$orFood->salePrice * $orFood->quantity}} |
        @endforeach
{{-- | Итого | {{$rest->orderFood->pluck('quantity')->sum() }} | {{ $sum }} |--}}
    @endforeach

@endcomponent


@component('mail::button', ['url' => config('app.url')])
В личный кабинет
@endcomponent

{{--Компания,<br>--}}
{{--{{ config('app.name') }}--}}
@endcomponent
