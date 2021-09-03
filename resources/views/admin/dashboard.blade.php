@extends('admin.layouts.default')

{{--@section('header')--}}
{{--    {!! $header !!}--}}
{{--@endsection--}}

@section('content')
    <div class="wrapper">
        <div class="container">
            <!-- begin first row -->
            <div class="row">

                    <h4 class="page-title">Дашборд
                    </h4>

                <!-- begin col-lg-3 col-md-6 -->
                <div class="col-lg-3 col-md-6">
                    <div class="card-box">

                        <a href="/" class="header-title m-t-0 m-b-30">Студии</a>

                        <div class="widget-chart-1">
                            {{--                                <div class="widget-chart-box-1">--}}
                            {{--                                    <h2 class="text-warning  m-b-0" data-plugin="counterup">{{ $countStepsStudios ?? null }}</h2>--}}
                            {{--                                    <p class="text-muted">Н/а</p>--}}
                            {{--                                </div>--}}

                            <div class="widget-detail-1">
                                <h2 class="p-t-10 m-b-0"> {{ 1000 }} </h2>
                                <p class="text-muted">Всего пользователей</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end col-lg-3 col-md-6 -->


            </div>
            <!-- end first row -->
    </div>
@endsection
