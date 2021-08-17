@extends('welcome')

@section('content')
    <main class="container mt-[30px] flex flex-col justify-start items-center 2xl">
        <h1 class="relative w-10/12 font-rb font-black text-6 text-center leading-[1.15]">Доставка еды — как раз, два, три.</h1>
        <a href="https://user.tapigo.ru/user/register" class="btn-create-account">Создать аккаунт</a>
        <section class="relative flex flex-col justify-center items-center w-full">
            <img src="/img/promo-1.webp" alt="Доставка еды" class="relative z-10 rellax" data-rellax-speed="5" data-rellax-mobile-speed="2">
            <p class="relative -mt-20 xs:-mt-24 sm:-mt-32 md:-mt-48 xl:-mt-72 font-rb font-black text-28 text-gray-300 uppercase leading-none z-0">Скоро</p>
        </section>
        <form action="" class="relative my-[7%] w-10/12 lg:w-[768px]">
            <input type="email" name="email" placeholder="Напишите вашу почту" class="relative py-2 w-full border-b-4 border-black lg:text-[2.5rem] text-black text-center font-os font-black placeholder-gray-300">
            <button type="submit" class="btn-submit">Сообщите мне сразу</button>
        </form>
        <p class="relative px-6 lg:max-w-screen-lg lg:text-[1.875rem] text-center leading-normal">Тапиго — это платформа, которая позволяет вам из одного места решать разные задачи, используя все преимущества <a href="https://tapigo.ru/about" class="links">единого аккаунта</a>. Для людей и для компаний.</p>
        <a href="https://user.tapigo.ru/user/register" class="btn-create-account">Создать аккаунт</a>
    </main>
@endsection
