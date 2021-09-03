$(document).ready(function () {

    // Ajax NProgress bar
    $(document).ajaxStart(function () {
        NProgress.start();
    });
    $(document).ajaxStop(function () {
        NProgress.done();
    });

    // Validation all pages
    if ($('#js-admin-edit-form').length > 0) {
        // $('#js-admin-edit-form').parsley();
    }

    // Multi select items
    if ($('.js-multi-select').length > 0) {
        $('.js-multi-select').select2();
    }

    // Translit names
    if ($('#alias').length > 0) {
        $('#js-admin-edit-form').liTranslit({
            elName: '#name',
            elAlias: '#alias'
        });
    }

    // Hide information block on pages
    if ($('#js-alert-block').length > 0) {
        setTimeout(hideAlertBlock, 5000);
    }

    // Delete items
    if ($('.js-delete-item').length > 0) {
        $('.js-delete-item').click(function () {

            var itemID = $(this).data('id');
            var table = $(this).data('table');
            var userName = $(this).data('username');
            var textTitle = "Удалить элемент?";

            if (userName !== undefined) {
                textTitle = "Удалить пользователя - " + userName + " ?";
            }

            swal({
                title: textTitle,
                text: "У вас потом не будет возможности восстановить его.",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Нет, не надо",
                confirmButtonClass: 'btn-danger',
                confirmButtonText: "Да, удалить",
                closeOnConfirm: false
            }, function () {
                $.ajax({
                    method: 'POST',
                    url: '/admin/ajaxDeleteItemFromList',
                    data: {
                        table: table,
                        itemID: itemID,
                        _token: token
                    }
                }).done(function (response) {
                    if (response.success == true) {
                        $('.js-item-tr-' + itemID).addClass('danger');
                        $('.js-hidden-' + itemID).addClass('hidden');
                        $('.js-hidden-delete-' + itemID).removeClass('hidden');
                        swal("Удалено!", "Элемент был удален из базы данных.", "success");
                    } else {
                        swal("Ошибка!", "Элемент не был удален из базы данных.", "error");
                    }
                });
            });

            return false;
        });
    }

    // Active moderation all services
    if ($('.js-active-all-services').length > 0) {
        $('.js-active-all-services').click(function () {
            swal({
                title: "Промодерировать все услуги",
                text: "У вас потом не будет возможности восстановить его.",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Нет, не надо",
                confirmButtonClass: 'btn-danger',
                confirmButtonText: "Да",
                closeOnConfirm: false
            }, function () {
                $.ajax({
                    method: 'POST',
                    url: '/admin/ajaxModerationServices',
                    data: {
                        _token: token
                    }
                }).done(function (response) {
                    if (response.success == true) {
                        swal("Промодерировано!", "Услуги промодерированы.", "success");
                    } else {
                        swal("Ошибка!", "Элемент не был удален из базы данных.", "error");
                    }
                });
            });

            return false;
        });
    }

    // Master to salon items
    if ($('.js-master-to-salon-item').length > 0) {
        $('.js-master-to-salon-item').click(function () {

            var itemID = $(this).data('id');
            var type = $(this).data('type');
            var textTitle = $(this).data('title');
            // $(this).disable();

            swal({
                title: textTitle,
                text: "У вас потом не будет возможности вернуть роль.",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Нет, не надо",
                confirmButtonClass: 'btn-danger',
                confirmButtonText: "Да, обновить",
                closeOnConfirm: true,
            }, function () {
                swal("В процессе!", "", "success");
                $.ajax({
                    method: 'POST',
                    url: '/admin/ajaxMasterToSalon',
                    data: {
                        itemID: itemID,
                        type: type,
                        _token: token
                    }
                }).done(function (response) {
                    if (response.success == true) {
                        $('.js-item-tr-' + response.itemID).remove();
                        console.log($('.js-master-to-salon-item'));
                        $('.js-master-to-salon-item').remove();
                        swal("Обновлено!", "", "success");
                    } else {
                        swal("Ошибка!", "", "error");
                    }
                });
            });

            return false;
        });
    }

    // Master to salon items
    if ($('.js-add-amocrm').length > 0) {
        $('.js-add-amocrm').click(function () {
            var itemID = $(this).data('id');
            var textTitle = $(this).data('title');

            swal({
                title: textTitle,
                text: "Добавить пользователя в amocrm.",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Нет, не надо",
                confirmButtonClass: 'btn-danger',
                confirmButtonText: "Да, добавить",
                closeOnConfirm: true,
            }, function () {
                swal("В процессе!", "", "success");
                $.ajax({
                    method: 'POST',
                    url: '/admin/ajaxAddAmocrm',
                    data: {
                        itemID: itemID,
                        _token: token
                    }
                });
            }).done(function (response) {
                if (response.success == true) {
                    $('.js-add-amocrm').remove();
                    swal("Обновлено!", "", "success");
                } else {
                    swal("Ошибка!", "", "error");
                }
            });
            ;

            return false;
        });
    }

    if ($('.js-spy_login').length > 0) {
        $('.js-spy_login').click(function () {
            var itemID = $(this).data('id');
            var textTitle = $(this).data('title');

            swal({
                title: textTitle,
                text: "Войти под пользователем",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Нет, не надо",
                confirmButtonClass: 'btn-success',
                confirmButtonText: "Вход",
                closeOnConfirm: true,
            }, function () {
                swal("В процессе!", "", "success");
                $.ajax({
                    method: 'POST',
                    url: '/admin/ajaxSpyLogin',
                    data: {
                        itemID: itemID,
                        _token: token
                    }
                }).done(function (response) {
                    console.log(response);
                    if (response.success == true) {
                        $('.js-spy_login').remove();
                        console.log(1);
                        window.localStorage.setItem('access_token', response.token);
                        swal("Успешно!", "", "success");
                    } else {
                        swal("Ошибка!", "", "error");
                    }
                });
            });

            return false;
        });
    }

    // Undo users
    if ($('.js-undo-user').length > 0) {
        $('.js-undo-user').click(function () {

            var itemID = $(this).data('id');
            var userName = $(this).data('username');
            var textTitle = "Вернуть пользователя?";

            if (userName !== undefined) {
                textTitle = "Удалить пользователя - " + userName + " ?";
            }

            swal({
                title: textTitle,
                text: "Восстановить пользователя.",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Нет, не надо",
                confirmButtonClass: 'btn-danger',
                confirmButtonText: "Да, восстановить",
                closeOnConfirm: false
            }, function () {
                $.ajax({
                    method: 'POST',
                    url: '/admin/ajaxUndoUser',
                    data: {
                        itemID: itemID,
                        _token: token
                    }
                }).done(function (response) {
                    if (response.success == true) {
                        console.log($('.js-item-tr-' + itemID));
                        $('.js-item-tr-' + itemID).removeClass('danger');
                        $('.js-hidden-' + itemID).removeClass('hidden');
                        $('.js-hidden-delete-' + itemID).addClass('hidden');
                        swal("Восстановили!", "Сотрудник был восстановлен в базе данных.", "success");
                    } else {
                        swal("Ошибка!", "Сотрудник не был восстановлен в базе данных.", "error");
                    }
                });
            });

            return false;
        });
    }

    // Undo users
    if ($('.js-undo-phone').length > 0) {
        $('.js-undo-phone').click(function () {

            var itemID = $(this).data('id');
            var userName = $(this).data('username');
            var textTitle = "Очитистить данные авторизации?";

            if (userName !== undefined) {
                textTitle = "Очитистить данные авторизации - " + userName + " ?";
            }

            swal({
                title: textTitle,
                text: "Очитистить телефон.",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Нет, не надо",
                confirmButtonClass: 'btn-danger',
                confirmButtonText: "Да, очистить",
                closeOnConfirm: false
            }, function () {
                $.ajax({
                    method: 'POST',
                    url: '/admin/ajaxUndoPhone',
                    data: {
                        itemID: itemID,
                        _token: token
                    }
                }).done(function (response) {
                    if (response.success == true) {
                        console.log($('.js-item-tr-' + itemID));
                        // $('.js-item-tr-' + itemID).adClass('danger');
                        // $('.js-hidden-' + itemID).removeClass('hidden');
                        // $('.js-hidden-delete-' + itemID).addClass('hidden');
                        swal("Очистили телефон!", "Очистили данные авторизации в базе данных.", "success");
                    } else {
                        swal("Ошибка!", "", "error");
                    }
                });
            });

            return false;
        });
    }

    // Change active status
    if ($('.js-change-parameter').length > 0) {
        $('.js-change-parameter').on('change', function () {

            var param;
            var field = $(this).data('field');
            var itemID = $(this).data('id');
            var table = $(this).data('table');

            $(this).is(':checked') ? param = 1 : null;
            if (table === 'reviews') {
                var userID = $(this).data('userid');
                $.ajax({
                    method: 'POST',
                    url: '/admin/ajaxChangeActiveStatus',
                    data: {
                        itemID: itemID,
                        param: param,
                        field: field,
                        table: table,
                        userID: userID,
                        _token: token
                    }
                });
            } else if (table === 'customs_services' || table === 'customs_categories') {
                $.ajax({
                    method: 'POST',
                    url: '/admin/ajaxCustomsServicesChangeActiveStatus',
                    data: {
                        itemID: itemID,
                        param: param,
                        field: field,
                        table: table,
                        _token: token
                    }
                });
            } else {
                $.ajax({
                    method: 'POST',
                    url: '/admin/ajaxChangeActiveStatus',
                    data: {
                        itemID: itemID,
                        param: param,
                        field: field,
                        table: table,
                        _token: token
                    }
                });
            }

        });
    }

    if ($('.js-change-pochta').length > 0) {
        $('.js-change-pochta').on('change', function () {

            var param;
            var field = $(this).data('field');
            var itemID = $(this).data('id');
            var table = $(this).data('table');

            $(this).is(':checked') ? param = 1 : null;
            var userID = $(this).data('userid');
            $.ajax({
                method: 'POST',
                url: '/admin/ajaxChangeDeliveryActive',
                data: {
                    itemID: itemID,
                    param: param,
                    field: field,
                    table: table,
                    userID: userID,
                    _token: token
                }
            });
        });
    }

    $('.js-delivery-threshold').on('blur', function () {
        var alias = $(this);
        var cityID = $(this).data('id');


        $.ajax({
            method: 'POST',
            url: '/admin/ajaxDeliveryThreshold',
            data: {
                cityID: cityID,
                alias: alias.val(),
                _token: token
            }
        });
    })

        $('.js-deliveries-threshold').on('blur', function () {
            var alias = $(this);

            $.ajax({
                method: 'POST',
                url: '/admin/ajaxDeliveriesThreshold',
                data: {
                    alias: alias.val(),
                    _token: token
                }
            });
        })

        // Change active status
        if ($('.js-change-parameter').length > 0) {
            $('.js-change-parameter').on('change', function () {

                var param;
                var field = $(this).data('field');
                var itemID = $(this).data('id');
                var table = $(this).data('table');

                $(this).is(':checked') ? param = 1 : null;
                if (table === 'reviews') {
                    var userID = $(this).data('userid');
                    $.ajax({
                        method: 'POST',
                        url: '/admin/ajaxChangeActiveStatus',
                        data: {
                            itemID: itemID,
                            param: param,
                            field: field,
                            table: table,
                            userID: userID,
                            _token: token
                        }
                    });
                } else if (table === 'customs_services' || table === 'customs_categories') {
                    $.ajax({
                        method: 'POST',
                        url: '/admin/ajaxCustomsServicesChangeActiveStatus',
                        data: {
                            itemID: itemID,
                            param: param,
                            field: field,
                            table: table,
                            _token: token
                        }
                    });
                } else {
                    $.ajax({
                        method: 'POST',
                        url: '/admin/ajaxChangeActiveStatus',
                        data: {
                            itemID: itemID,
                            param: param,
                            field: field,
                            table: table,
                            _token: token
                        }
                    });
                }

            });
        }

        if ($('#csvFile').length > 0) {
            $('#csvFile').on('change', function () {
                var files = this.files;
                event.stopPropagation();
                event.preventDefault();

                var data = new FormData();
                $.each(files, function (key, value) {
                    data.append(key, value);
                });
                data.append('_token', token);
                data.append('file', files[0]);

                console.log(data);
                $.ajax({
                    method: 'POST',
                    url: '/admin/ajaxUploadCsvFile',
                    data: data,
                    processData: false,
                    contentType: false,
                });
            });
        }
    });

    function hideAlertBlock() {
        $('#js-alert-block').hide(500);
    }

