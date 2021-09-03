@extends('Common::admin.layouts.default')

@section('header')
{!! $header !!}
@endsection

@section('content')
<div class="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="btn-group pull-left m-t-15" style="margin-right: 20px">
                    <a href="{{ route('admin_cities_list') }}">
                        <button class="btn btn-accent btn-inverse btn-trans waves-effect m-b-5"> <i
                                class="fa fa-chevron-left m-r-5"></i> <span>Назад</span> </button>
                    </a>
                </div>
                <div class="btn-group pull-right m-t-15">
                    <a href="{{ route('admin_cities_post_list_edit') }}">
                        <button class="btn btn-success waves-effect waves-light m-b-5"> <i class="fa fa-plus m-r-5"></i>
                            <span>Новый город</span> </button>
                    </a>
                </div>
                <h4 class="page-title">{{ $oPost ? 'Редактировать город' : 'Добавить город' }}</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (Session::has('success'))
                            <div id="js-alert-block" class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <strong>Отлично!</strong> <?= Session::get('success'); ?> Вы можете вернуться к <a
                                    href="{{ route('admin_cities_list') }}" class="alert-link">списку</a> или <a
                                    href="#" class="alert-link" data-dismiss="alert" aria-hidden="true">продолжить
                                    работать</a>.
                            </div>
                            @endif
                            <form id="js-admin-edit-form" method="post" class="form-horizontal" role="form"
                                action="{{ route('admin_cities_post_list_edit') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                @if ($oPost)
                                <input type="hidden" name="cityID" value="{{ $oPost->cityID }}">
                                @endif
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Город <span
                                            style="color: red;">*</span></label>
                                    <div class="col-md-10">
                                        <input id="name" name="name" type="text" class="form-control jsGeoMap"
                                            value="{{ $oPost->name ?? null }}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Алиас <span
                                            style="color: red;">*</span></label>
                                    <div class="col-md-10">
                                        <input id="alias" name="alias" type="text" class="form-control"
                                            value="{{ $oPost->alias ?? null }}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Город в родительном<span
                                            style="color: red;">*</span></label>
                                    <div class="col-md-10">
                                        <input id="prepositionalName" name="prepositionalName" type="text"
                                            class="form-control" value="{{ $oPost->prepositionalName ?? null }}"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Карта <span
                                            style="color: red;">*</span></label>
                                    <div class="col-md-10">
                                        <div class="js-cabinet__map card-adress__map">
                                            <div id="card-map"></div>
                                        </div>
                                        <input id="latitude" name="latitude" type="hidden"
                                            value="{{ isset($oPost) && $oPost->latitude != null ? $oPost->latitude : '' }}" required>
                                        <input id="longitude" name="longitude" type="hidden"
                                            value="{{ isset($oPost) && $oPost->longitude != null ? $oPost->longitude : '' }}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"></label>
                                    <div class="col-md-10">
                                        Метро
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Да</label>
                                    <div class="col-md-10">
                                        <input name="isMetro" type="checkbox" data-plugin="switchery"
                                            data-color="#00b19d"
                                            {{ $oPost && $oPost->isMetro ? 'checked' : (!$oPost ? 'checked' : null) }}
                                            value="1" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"></label>
                                    <div class="col-md-10">
                                        Район
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Да</label>
                                    <div class="col-md-10">
                                        <input name="isDistrict" type="checkbox" data-plugin="switchery"
                                            data-color="#00b19d"
                                            {{ $oPost && $oPost->isDistrict ? 'checked' : (!$oPost ? 'checked' : null) }}
                                            value="1" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Категория</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="regionID" id="regionID">
                                            <option value="1">Нет</option>
                                            @foreach($regions as $region)
                                            @if ($oPost ? $oPost->regionID == $region->regionID : '')
                                            <option value="{{ $region->regionID }}" selected>{{ $region->name ?? null }}
                                            </option>
                                            @else
                                            <option value="{{ $region->regionID ?? null }}">{{ $region->name ?? null }}
                                            </option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">SEO Текст<span
                                            style="color: red;">*</span></label>
                                    <div class="col-md-10">
                                        <textarea id="editor" name="seoText" class="form-control"
                                            required>{{ $oPost->seoText ?? null }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Title</label>
                                    <div class="col-md-10">
                                        <textarea name="seoTitle"
                                            class="form-control">{{ $oPost->seoTitle ?? null }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Description</label>
                                    <div class="col-md-10">
                                        <textarea name="seoDescription"
                                            class="form-control">{{ $oPost->seoDescription ?? null }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Бесплатная доставка начиная с</label>
                                    <div class="col-md-10">
                                        <input type="text" name="delivery_threshold" class="form-control"
                                            value="{{ $oPost->delivery_threshold ?? null }}" required></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Часовой пояс</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="timezoneID" id="timezoneID">
                                            <option value="">Нет</option>
                                            @foreach($oTimezones as $timezone)
                                            @if ($oPost ? $oPost->timezoneID == $timezone->timezoneID : '')
                                            <option value="{{ $timezone->timezoneID }}" selected>
                                                {{ $timezone->name ?? null }}</option>
                                            @else
                                            <option value="{{ $timezone->timezoneID ?? null }}">
                                                {{ $timezone->name ?? null }}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"></label>
                                    <div class="col-md-10">
                                        Активный?
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Да</label>
                                    <div class="col-md-10">
                                        <input name="active" type="checkbox" data-plugin="switchery"
                                            data-color="#00b19d"
                                            {{ $oPost && $oPost->active ? 'checked' : (!$oPost ? 'checked' : null) }}
                                            value="1" />
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"></label>
                                    <div class="col-md-10">
                                        <button id="js-form-submit" type="submit"
                                            class="btn btn-purple waves-effect waves-light">{{ $oPost ? 'Редактировать' : 'Добавить' }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<link href="{{ '/admin/css/pages.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ '/admin/plugins/switchery/switchery.min.css' }}" rel="stylesheet" />
@endsection

@section('scripts_bottom_production')
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
@endsection

@section('scripts_bottom')
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
<script src="{{ '/admin/plugins/parsleyjs/dist/parsley.min.js' }}" type="text/javascript"></script>
<script src="{{ '/admin/plugins/parsleyjs/src/i18n/ru.js' }}" type="text/javascript"></script>
<script src="{{ '/admin/plugins/switchery/switchery.min.js' }}" type="text/javascript"></script>
<script src="{{ '/admin/js/jquery.liTranslit.js' }}" type="text/javascript"></script>
<script src="{{ '/admin/js/jquery.core.js' }}" type="text/javascript"></script>
<script src="{{ '/admin/custom/users/usersEdit.js' }}" type="text/javascript"></script>
<script src="{{ '/admin/js/ckeditor/ckeditor.js' }}" type="text/javascript"></script>
<script src="{{ '/admin/custom/blog/blogPostEdit.js' }}" type="text/javascript"></script>
@endsection
