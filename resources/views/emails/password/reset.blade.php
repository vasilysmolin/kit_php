@component('mail::message')
# Восстановление пароля

Вы получили это письмо, потому что мы получили запрос на сброс пароля для вашей учетной записи.

@component('mail::button', ['url' => "https://tapigo.ru/auth/reset-password?token=${token}&email=${email}"])
    Сброс пароля
@endcomponent


Срок действия этой ссылки для сброса пароля истекает через 60 минут.

Если вы не запрашивали сброс пароля, никаких дальнейших действий не требуется.

@component('mail::button', ['url' => 'https://hub.tapigo.ru/profile'])
В личный кабинет
@endcomponent

{{--Компания,<br>--}}
{{--{{ config('app.name') }}--}}
@endcomponent
