@extends('Common::admin.layouts.default')

@section('header')
    {!! $header !!}
@endsection

@section('content')
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="btn-group pull-right m-t-15">
                        <a href="{{ route('admin_cities_post_list_edit') }}">
                            <button class="btn btn-success waves-effect waves-light m-b-5"> <i class="fa fa-plus m-r-5"></i> <span>Новый город</span> </button>
                        </a>
                    </div>
                    <h4 class="page-title">Список городов!!</h4>
                </div>
            </div>
            <div class="row pull-right btn-group">
                <form id="js-search-form" method="get" class="form-horizontal" role="form" action="">
                    <div class="form-group">
                        <div class="col-sm-5">
                            <select name="sort" class="js-select-differents citySelect"
                                    data-placeholder="Стоимость">
                                <option value="">Стоимость</option>

                                @foreach(Config::get('settings.arrSortCost') as $key => $provider)
                                    @if($request->threshold == $key)
                                        <option value="{{ $key }}" selected>{{ $provider }}</option>
                                    @else
                                        <option value="{{ $key }}">{{ $provider }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-purple waves-effect waves-light">Искать
                            </button>
                        </div>
                        <div class="col-sm-6 form-horizontal" style="line-height: 35px;">
                            <a href="{{route('admin_users_all')}}">Сбросить фильтр</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th width="10%" style="text-align: center">Город</th>
                                <th width="10%" style="text-align: center">Страна</th>
                                <th width="10%" style="text-align: center">Seo категории</th>
                                <th width="10%" style="text-align: center">Seo услуги</th>
                                <th width="4%" style="text-align: center"><a href="{{route('admin_all_cities_deliveries')}}">Доставки</a></th>
                                <th width="4%" style="text-align: center"><a href="{{route('admin_all_cities_deliveries')}}">Стоимость</a></th>
                                <th width="4%" style="text-align: center">Метро</th>
                                <th width="4%" style="text-align: center">Активный</th>
                                <th width="5%" style="text-align: center">Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (isset($oCitiesCityList) && count($oCitiesCityList) > 0)
                                @foreach ($oCitiesCityList as $oCities)
                                    <tr class="js-item-tr-{{ $oCities->cityID }}">

                                        <td style="text-align: center; font-size: 13px;">
                                            <a href="{{ route('index_catalog', [$oCities->alias]) }}">{{ $oCities->name }}</a>
                                        </td>
                                        <td style="text-align: center; font-size: 13px;">{{ $oCities->country->name }}</td>
                                        <td style="text-align: center; font-size: 13px;">
                                            <a href="{{ route('admin_seo_services_list', [$oCities->cityID]) }}">Seo категории</a>
                                        </td>
                                        <td style="text-align: center; font-size: 13px;">
                                            <a href="{{ route('admin_seo_filters_list', [$oCities->cityID]) }}">Seo услуги</a>
                                        </td>
                                        <td style="text-align: center; font-size: 13px;">
                                            <a href="{{ route('admin_city_deliveries', [$oCities->cityID]) }}">{{$oCities->delivery_cdek + $oCities->delivery_dally + $oCities->delivery_pochta + count($oCities->delivery)}}</a>
                                        </td>
                                        <td style="text-align: center; font-size: 13px;">
                                            <input id="{{$oCities->cityID}}" name="threshold" data-type="number"
                                                   data-id="{{$oCities->cityID}}"
                                                   class="form-control js-delivery-threshold"
                                                   value="{{$oCities->delivery_threshold}}"
                                                   required=""/>
                                        </td>
                                        <td style="text-align: center; font-size: 13px;">
                                                <input data-id="{{ $oCities->cityID }}" data-table="{{ $oCities->getTable() }}" data-field="isMetro" class="js-change-parameter" data-size="small" type="checkbox" data-plugin="switchery" data-color="#00b19d" {{ $oCities->isMetro == 1 ? 'checked' : null }} value="1" />
                                        </td>
                                        <td style="text-align: center">
                                            <input data-id="{{ $oCities->cityID }}" data-table="{{ $oCities->getTable() }}" data-field="active" class="js-change-parameter" data-size="small" type="checkbox" data-plugin="switchery" data-color="#00b19d" {{ $oCities->active == 1 ? 'checked' : null }} value="1" />
                                        </td>
                                        <td style="text-align: center">
                                            <a href="{{ route('admin_cities_post_list_edit', [$oCities->cityID]) }}" class="on-default edit-row" title="Редактировать" style="margin-right: 20px"><i class="fa fa-cog fa-lg"></i></a>
                                            <a href="#" class="js-delete-item on-default remove-row" data-id="{{ $oCities->cityID }}" data-table="{{ $oCities->getTable() }}" title="Удалить"><i class="fa fa-trash fa-lg"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="12" style="text-align: center">Список элементов пуст</td></tr>
                            @endif
                            </tbody>
                        </table>
                        <!-- сюда поместим вывод пагинации-->
                        {{ $oCitiesCityList->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link href="{{ '/admin/plugins/switchery/switchery.min.css' }}" rel="stylesheet" />
@endsection

@section('scripts_bottom')
    <script src="{{ '/admin/plugins/switchery/switchery.min.js' }}"></script>
    <script src="{{ '/admin/js/jquery.core.js' }}"></script>
    <script src="{{ '/admin/plugins/select2/dist/js/select2.min.js' }}"></script>
    <script src="{{ '/admin/custom/pages/typesList.js' }}" type="text/javascript"></script>
    <script src="{{ '/admin/js/custom/custom.js' }}"></script>


@endsection
