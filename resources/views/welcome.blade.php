<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <title>Тапиго | @yield('title', 'Объявления, работа, услуги, доставка еды — это один аккаунт!')</title>
        <meta name="description" content="@yield('description', 'Тапиго — это платформа, которая позволяет вам из одного места решать разные задачи, используя все преимущества единого аккаунта. Для людей и для компаний.')">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@1,400;1,700;1,800&family=Roboto:wght@400;700;900&family=Montserrat:wght@900&display=swap" rel="stylesheet">
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        @include('layouts.header')
        @yield('content')
        <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
