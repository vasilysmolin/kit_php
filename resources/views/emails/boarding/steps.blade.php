@component('mail::message')
# Спасибо, что выбрали наш сайт, осталось
заполнить профиль и можно пользоваться всеми функциями и размещать объявления!

@component('mail::button', ['url' => 'https://hub.tapigo.ru/profile'])
В личный кабинет
@endcomponent

@endcomponent
