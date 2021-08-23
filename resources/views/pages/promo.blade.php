@extends('welcome')

@section('title', 'Еда - заказ и доставка еды из кафе и ресторанов в Перми за один час')
@section('description', 'Доставка еды в Перми за один час из кафе и ресторанов')

@section('content')
    <main class="container mt-[15px] lg:mt-[30px] flex flex-col justify-start items-center 2xl">
        @if($errors->any())
            <ul class="error">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        <h1 class="relative w-full px-5 xl:w-10/12 xl:p-0 font-mr font-black text-6 text-center leading-[1.15]">Доставка еды — как раз, два, три.</h1>
        <a href="https://user.tapigo.ru/user/register" class="btn-create-account">Создать аккаунт</a>
        <section class="relative flex flex-col justify-center items-center w-full box-border">
            <img src="/img/promo-1.webp" alt="Доставка еды" class="relative z-10 rellax" data-rellax-speed="5" data-rellax-mobile-speed="2">
            <p class="relative -mt-20 xs:-mt-24 sm:-mt-32 md:-mt-48 xl:-mt-72 font-mr font-black text-hybrid1 xs:text-[7.15rem] sm:text-[10.15rem] md:text-[12.2rem] lg:text-[16.3rem] xl:text-[20.5rem] 2xl:text-[24.65rem] text-gray-300 uppercase leading-none z-0">Скоро</p>
        </section>
        <form action="{{ route('email') }}" method="post" class="relative my-[7%] w-10/12 lg:w-[768px]">
            @csrf
            <input type="email" name="email" id="email" placeholder="Напишите вашу почту" class="relative py-5 w-full border-b-4 border-black text-[1.1rem] xs:text-[1.5rem] lg:text-[2.5rem] text-black text-center font-os font-black placeholder-gray-300">
            <button type="submit" class="btn-submit">Сообщите мне сразу</button>
        </form>
        <p class="relative px-4 lg:max-w-screen-lg xs:text-[1.2rem] lg:text-[1.875rem] xs:text-center leading-normal">Тапиго — это платформа, которая позволяет вам из одного места решать разные задачи, используя все преимущества <a href="https://tapigo.ru/about" class="links">единого аккаунта</a>. Для людей и для компаний.</p>
        <a href="https://user.tapigo.ru/user/register" class="btn-create-account">Создать аккаунт</a>
    </main>
@endsection
