@component('mail::message')
# Нажмите кнопку ниже, чтобы подтвердить свой адрес электронной почты.

@component('mail::button', ['url' => "https://tapigo.ru/auth/verify-email?hash=${hash}"])
    Подтвердите адрес электронной почты
@endcomponent

Если вы не создавали учетную запись, никаких дальнейших действий не требуется.

@component('mail::button', ['url' => 'https://hub.tapigo.ru/profile'])
В личный кабинет
@endcomponent

{{--Компания,<br>--}}
{{--{{ config('app.name') }}--}}
@endcomponent
