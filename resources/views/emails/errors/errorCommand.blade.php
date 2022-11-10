@component('mail::message')
# Данные по ошибке
- пользователь профиль: {{ $profile ?? null }}

## Трасировка
- код ошибки {{ $errors['code'] }}
- трасировка {{$errors['getTraceAsString'] }}
- сообщение ошибки {{ $errors['getMessage'] }}


Компания,<br>
{{ config('app.name') }}
@endcomponent
