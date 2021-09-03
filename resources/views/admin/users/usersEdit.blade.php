<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="btn-group pull-left m-t-15" style="margin-right: 20px">
                    <a href="{{ route('admin.users.index') }}">
                        <button class="btn btn-accent btn-inverse btn-trans waves-effect m-b-5"> <i
                                class="fa fa-chevron-left m-r-5"></i> <span>Назад</span> </button>
                    </a>
                </div>
                <div class="btn-group pull-right m-t-15">
                    <a href="{{ route('admin.users.create') }}">
                        <button class="btn btn-success waves-effect waves-light m-b-5"> <i class="fa fa-plus m-r-5"></i>
                            <span>Новый пользователь</span> </button>
                    </a>
                </div>
                <h4 class="page-title">Редактировать</h4>
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
                                    href="{{ route('admin.users.index') }}" class="alert-link">списку</a> или <a
                                    href="#" class="alert-link" data-dismiss="alert" aria-hidden="true">продолжить
                                    работать</a>.
                            </div>
                            @endif
                            <form method="post" class="form-horizontal" role="form"
                                action="{{ route('admin.users.update', [$user->id]) }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                @if ($user)
                                    <input type="hidden" name="_method" value="put" />
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                @endif
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Имя <span
                                            style="color: red;">*</span></label>
                                    <div class="col-md-10">
                                        <input id="name" name="name" type="text" class="form-control jsGeoMap"
                                            value="{{ $user->name ?? null }}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">E-mail <span
                                            style="color: red;">*</span></label>
                                    <div class="col-md-10">
                                        <input id="alias" name="email" type="text" class="form-control"
                                            value="{{ $user->email ?? null }}" required>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"></label>
                                    <div class="col-md-10">
                                        <button type="submit" class="btn btn-purple waves-effect waves-light">Редактировать</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

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

