@component('mail::message')
# Спасибо, что выбрали наш сайт

@php
    $states = new App\Objects\States\States();
    $reasons = new App\Objects\Reasons\Reasons();
@endphp
@switch($model->state)
    @case($states->block())
    Ваше объявление заблокированно: {{ $reasons->getById($model->reason) }}
    @break

    @case($states->reBlock())
    Ваше объявление повторно заблокированно: {{ $reasons->getById($model->reason) }}
    @break

    @case($states->pause())
    Ваше обьявление поставлено на паузу
    @break

    @case($states->inProgress())
    Ваше обьявление проверяется модератором
    @break

    @case($states->active())
    Ваше объявление прошло успешно модерацию
    @break

    @default
@endswitch


@component('mail::button', ['url' => 'https://hub.tapigo.ru/profile'])
В личный кабинет
@endcomponent

{{--Компания,<br>--}}
{{--{{ config('app.name') }}--}}
@endcomponent
