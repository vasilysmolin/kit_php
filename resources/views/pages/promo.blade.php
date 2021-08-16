@extends('welcome')

@section('content')
    <main class="container bg-gray-50">
        <h1>Доставка еды — как раз, два, три.</h1>
        <a href="" class="">Создать аккаунт</a>
        <section class="relative w-full flex flex-col justify-center items-center bg-red-50">
            <img src="/img/promo-1.webp" alt="Доставка еды" class="rellax" data-rellax-speed="7">
            <p class="absolute bottom-0 font-rb font-black text-480">Скоро</p>
        </section>
        <form action="">
            <input type="email" name="email" placeholder="Напишите вашу почту" class="font-os font-black">
            <button type="submit">Сообщите мне сразу</button>
        </form>
        <p>Тапиго — это платформа, которая позволяет вам из одного места решать разные задачи, используя все преимущества единого аккаунта. Для людей и для компаний.</p>
        <a href="" class="">Создать аккаунт</a>
    </main>
@endsection
