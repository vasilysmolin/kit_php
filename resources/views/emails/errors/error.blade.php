@component('mail::message')
# Данные по ошибке

- url: {{ $errors['url'] }}
- method: {{ $errors['method'] }}
- прошлый url: {{ $errors['urlPrevious'] }}
- пользователь: {{ $user->id ?? 'не авторизован' }}

## Параметры:
@component('mail::table')

    | Название      | Значение  |
    | :------------- |:----------- |
    @foreach($errors['params'] as $key => $val)
    | {{ $key }} | {{ $val}} |
    @endforeach
@endcomponent

## Заголовки:
@component('mail::table')
    | Название      | Значение      |
    | ------------- |:-------------:|
@foreach($errors['headers'] as $key => $val)
    @php($str = mb_strimwidth($val[0], 0, 20, "..."))
    | {{ $key }}      |    {{ $str}}    |
@endforeach

@endcomponent

## Трасировка
- код ошибки {{ $errors['code'] }}
- трасировка {{$errors['getTraceAsString'] }}
- сообщение ошибки {{ $errors['getMessage'] }}


Компания,<br>
{{ config('app.name') }}
@endcomponent
