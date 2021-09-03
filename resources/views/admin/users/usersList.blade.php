<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>


    <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="btn-group pull-right m-t-15">
                        <a href="{{ route('admin.users.create') }}">
                            <button class="btn btn-success waves-effect waves-light m-b-5"> <i class="fa fa-plus m-r-5"></i> <span>Новый пользователь</span> </button>
                        </a>
                    </div>
                    <h4 class="page-title">Список пользователей</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th width="10%" style="text-align: center">id</th>
                                <th width="10%" style="text-align: center">Имя</th>
                                <th width="10%" style="text-align: center">E-mail</th>
                                <th width="5%" style="text-align: center">Действия</th>
                            </tr>
                            </thead>
                            <tbody>

                            @if (isset($users) && count($users) > 0)
                                @foreach ($users as $user)
                                    <tr class="js-item-tr-{{ $user->id }}">
                                        <td style="text-align: center; font-size: 13px;">
                                            {{ $user->id }}
                                        </td>
                                        <td style="text-align: center; font-size: 13px;">
                                            {{ $user->name }}
                                        </td>
                                        <td style="text-align: center; font-size: 13px;">
                                            {{ $user->email }}
                                        </td>

                                        <td style="text-align: center">
                                            <a href="{{ route('admin.users.update', [$user->id]) }}" class="on-default edit-row" title="Редактировать" style="margin-right: 20px"><i class="fa fa-cog fa-lg"></i></a>
                                            <a href="{{ route('admin.users.destroy', [$user->id]) }}" class="on-default edit-row"  title="Удалить"><i class="fa fa-trash fa-lg"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="12" style="text-align: center">Список элементов пуст</td></tr>
                            @endif
                            </tbody>
                        </table>
                        <!-- сюда поместим вывод пагинации-->
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
