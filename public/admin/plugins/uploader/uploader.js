$(document).ready(function () {

    var uploadFileEntity = $('.js-upload-file');

    // Searching for file upload widget is exist
    if (uploadFileEntity.length > 0) {

        var removedFiles = [];

        // Change the upload input field
        uploadFileEntity.on('change', function (event) {

            var files = this.files;
            var data = new FormData();
            var randValue = $(this).data('rand');
            var cropParams = $(this).data('crop');
            var htmlBlock = $(this).parent().parent('.bb-upload__item');

            event.stopPropagation();
            event.preventDefault();

            if (typeof files === 'undefined') {
                return false;
            }

            $.each(files, function (key, value) {
                data.append(key, value);
            });

            data.append('randValue', randValue);
            data.append('cropParams', cropParams);
            data.append('_token', token);

            $.ajax({
                method: 'POST',
                url: '/admin/ajaxAdminAddImage',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response){
                    if (response.success === true) {
                        var arrImagesBlocks = response.arrImagesBlocks;
                        $(arrImagesBlocks).insertBefore(htmlBlock);

                        reInitialize();
                    } else {
                        if (response.error) {
                            alert(response.error);
                        }
                    }
                }
            });

            return false;
        });

        // Submit the all form
        var formValid = $('#js-admin-edit-form').parsley();
        $('#js-form-submit').on('click', function (event) {
            event.stopPropagation();
            event.preventDefault();

            formValid.validate();
            if (formValid.isValid()) {
                var sort = 1;
                var data = [];
                var arrSort = [];
                var randValue = uploadFileEntity.data('rand');

                $('.js-image-preview').each(function () {
                    var params = {};
                    var sortFiles = {};

                    sortFiles['name'] = $(this).data('name');
                    sortFiles['count'] = sort;

                    params['name'] = $(this).data('name');
                    params['extension'] = $(this).data('extension');
                    params['mime'] = $(this).data('mime');
                    params['size'] = $(this).data('size');
                    params['randValue'] = randValue;

                    arrSort.push(sortFiles);
                    data.push(params);
                    sort ++;
                });

                $('.js-upload-file__sort').val(JSON.stringify(arrSort));

                $(this).attr('disabled', 'disabled');

                $.ajax({
                    method: 'POST',
                    url: '/admin/ajaxFileSeries',
                    data: {
                        removedFiles: removedFiles,
                        data: data,
                        randValue: randValue,
                        consultantID: $('#consultantID').val(),
                        galleryID: $('#galleryID').val(),
                        _token: token
                    }
                }).done(function (response) {
                    if (response.success == true) {
                        $('form').submit();
                    }
                });
            }

            return false;
        });

        // Reinitialize upload widget js scripts
        if ($('.bb-upload').length > 0) {
            jsBbUploadCount();

            $(".bb-upload__list" ).sortable({
                items: "li:not(.unsortable)",
                stop: function() {
                    jsBbUploadCount();
                }
            });
            // $(".bb-upload__list" ).disableSelection();
        }
    }

    // Download archive album from admin panel
    var uploadFileArchive = $('.js-upload-file-archive');

    // Searching for file upload widget is exist
    if (uploadFileEntity.length > 0) {

        // Change the upload input field
        uploadFileArchive.on('change', function (event) {

            var files = this.files;
            var data = new FormData();
            var randValue = $(document).find('#randValue').val();
            var htmlBlock = $(this).parent().parent('.bb-upload__item');

            event.stopPropagation();
            event.preventDefault();

            if (typeof files === 'undefined') {
                return false;
            }

            $.each(files, function (key, value) {
                data.append(key, value);
            });

            data.append('randValue', randValue);
            data.append('_token', token);

            $.ajax({
                method: 'POST',
                url: '/admin/ajaxAdminAddArchive',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success === true) {
                        var fileArchiveBlock = response.arrArchivesBlocks;
                        $(fileArchiveBlock).insertBefore(htmlBlock);
                        $(htmlBlock).hide();
                        reInitialize();
                    } else {
                        if (response.error) {
                            alert(response.error);
                        }
                    }
                }
            });

            return false;
        });
    }

    $(document).on('click', '.js-delete-file', function (event) {

        event.stopPropagation();
        event.preventDefault();

        var block = $(this);
        var fileName = block.parent().prev().prev().data('name');
        var randValue = uploadFileEntity.data('rand');
        var id = $(this).data('id');
        removedFiles.push(fileName);

        $.ajax({
            method: 'POST',
            url: '/ajaxRemoveImage',
            data: {
                fileName: fileName,
                id: id,
                randValue: randValue,
                _token: token
            }
        }).done(function (response) {
            if (response.success == true) {
                block.parent().parent().remove();
                reInitialize();
            }
        });
    });

    // Поворот фото вправо
    $(document).on('click', '.js-rotate-file-right', function (event) {

        event.stopPropagation();
        event.preventDefault();

        var block = $(this);
        var fileName = block.parent().prev().prev().data('name');
        var fileExtension = block.parent().prev().prev().data('extension');
        var cropParams = $('.js-upload-file').data('crop');
        var randValue = uploadFileEntity.data('rand');
        var imgBlock = block.parent().prev().prev();

        // Убираем иконку поворота, заглушка, чтобы не поломать фото при повороте, если быстро нажимать
        block.hide();

        $.ajax({
            method: 'POST',
            url: '/ajaxUpFile',
            data: {
                fileName: fileName,
                randValue: randValue,
                fileExtension: fileExtension,
                cropParams: cropParams,
                edit: ['rotate', ['right', 'albums']],
                _token: token
            }
        }).done(function (response) {
            if (response.success == true) {
                // Снова показываем иконку, поворота
                block.show();
                updateImage(imgBlock);
            }
        });
    });

    // Delete file
    $(document).on('click', '.js-delete-file-new', function(event) {
        event.stopPropagation();
        event.preventDefault();

        var i = 0;
        var block = $(this);
        var fileName = block
            .parent()
            .prev()
            .children()
            .data('name');
        var randValue = uploadFileEntity.data('rand');
        var id = $(this).data('id');

        // removedFiles.push(fileName);

        $.ajax({
            method: 'POST',
            url: '/ajaxRemoveImage',
            data: {
                fileName: fileName,
                id: id,
                randValue: randValue,
                _token: token,
            },
        }).done(function(response) {
            if (response.success == true) {
                block
                    .parent()
                    .parent()
                    .remove();
                $('.bb-upload__item').each(function() {
                    i++;
                });
                if (i == 0) {
                    $('.js-lk-empty').show();
                }
            }
        });
    });

    // Delete avatar
    if ($('.js-avatar-delete').length > 0) {
        $(document).on('click', '.js-avatar-delete', function(event) {
            event.stopPropagation();
            event.preventDefault();

            let itemID = $(event.currentTarget).attr('itemid');
            let table = $(event.currentTarget).attr('table');

            swal({
                title: "Удалить аватар?",
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
                    url: '/admin/ajaxDeleteAvatarUser',
                    data: {
                        itemID: itemID,
                        table: table,
                        _token: token
                    }
                }).done(function (response) {
                    if (response.success == true) {
                        $('.js-avatar-container').attr('src', '');
                        $('.bb-upload__overlay').addClass('hidden');
                        swal("Удалено!", "Аватар был удален из базы данных.", "success");
                    } else {
                        swal("Ошибка!", "Аватар не был удален из базы данных.", "error");
                    }
                });
            });

            return false;
        });
    }

    if ($('.js-avatar-edit').length > 0) {
        $(document).on('click', '.js-avatar-edit', function(event) {
            event.stopPropagation();
            event.preventDefault();

            $('#image').attr('src', $(this).data('src'));
            cropperInit();
        });
    }

    if ($('.js-avatar-upload').length > 0) {
        $(document).on('change', '.js-avatar-upload', function(event) {
            event.stopPropagation();
            event.preventDefault();

            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#image').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }


            $('.js-open-model').click();
            setTimeout(() => {
                cropperInit();
            }, 200);
        });
    }

    if ($('.js-avatar-save').length > 0) {
        $(document).on('click', '.js-avatar-save', function(event) {
            event.stopPropagation();
            event.preventDefault();

            var form_data = new FormData();
            let crop = $(event.currentTarget).attr('crop');
            let itemID = $(event.currentTarget).attr('itemid');
            let table = $(event.currentTarget).attr('table');
            let upload = $('.js-avatar-upload');

            var file = null;
            if (upload[0].files.length) {
                file = upload.prop('files')[0];
                form_data.append('file', file);
            }

            form_data.append('table', table);
            form_data.append('crop', crop);
            form_data.append('itemID', itemID);
            form_data.append('_token', token);

            $.ajax({
                method: 'POST',
                url: '/admin/ajaxUpdateAvatarUser',
                data: form_data,
                cache: false,
                dataType: 'json',
                processData: false,
                contentType: false,
            }).done(function (response) {
                if (response.success == true) {
                    $('.js-avatar-close').click();
                    swal("Обновлено!", "Аватар был обновлен.", "success");
                } else {
                    swal("Ошибка!", "Аватар не был обновлен.", "error");
                }
            });
        });
    }

    // Поворот фото вправо
    $(document).on('click', '.js-up-file', function (event) {

        event.stopPropagation();
        event.preventDefault();

        var block = $(this);
        var id = $(this).data('id');
        var addressID = $('#addressID').data('address');
        var imgBlock = block.parent().prev().prev();

        // Убираем иконку поворота, заглушка, чтобы не поломать фото при повороте, если быстро нажимать
        block.hide();

        $.ajax({
            method: 'POST',
            url: '/ajaxUpFile',
            data: {
                uploadFileID: id,
                addressID: addressID,
                edit: ['rotate', ['right', 'albums']],
                _token: token
            }
        }).done(function (response) {
            if (response.success == true) {
                // Снова показываем иконку, поворота
                block.show();
                updateImage(imgBlock);
                alert('новое главное фото установленно');
            }
        });
    });
    // Поворот фото влево
    $(document).on('click', '.js-rotate-file-left', function (event) {

        event.stopPropagation();
        event.preventDefault();

        var block = $(this);
        var fileName = block.parent().prev().prev().data('name');
        var fileExtension = block.parent().prev().prev().data('extension');
        var randValue = uploadFileEntity.data('rand');
        var cropParams = $('.js-upload-file').data('crop');
        var imgBlock = block.parent().prev().prev();

        // Убираем иконку поворота, заглушка, чтобы не поломать фото при повороте, если быстро нажимать
        block.hide();

        $.ajax({
            method: 'POST',
            url: '/ajaxEditingImage',
            data: {
                fileName: fileName,
                randValue: randValue,
                fileExtension: fileExtension,
                cropParams: cropParams,
                edit: ['rotate', ['left', 'albums']],
                _token: token
            }
        }).done(function (response) {
            if (response.success == true) {
                // Снова показываем иконку, поворота
                block.show();
                updateImage(imgBlock);
            }
        });
    });

    // Sortable item
    var sortTable = $('#js-list-table');
    console.log(sortTable);
    if (sortTable.length > 0) {
        sortTable.sortable({
            handle: '.js-sortable-pivot',
            update: function (event, ui) {
                var arrEntitiesIDs = [];
                $('.js-items').each(function () {
                    arrEntitiesIDs.push($(this).data('id'));
                });
                $.ajax({
                    method: 'POST',
                    url: '/admin/ajaxSortItems',
                    data: {
                        arrEntitiesIDs: arrEntitiesIDs,
                        table: sortTable.data('table'),
                        _token: token
                    },
                    success: function(response) { window.location.href = window.location.href; }
                });
            }
        });
    }
});

function cropperInit()
{
    const image = document.getElementById('image');

    var src = image.getAttribute('src');
    new Cropper(image, {
        aspectRatio: 1,
        background: false,
        highlight: false,
        movable: false,
        rotatable: false,
        scalable: false,
        zoomable: false,
        zoomOnTouch: false,
        zoomOnWheel: false,
        minContainerWidth: 500,
        minContainerHeight: 500,
        viewMode: 1,
        crop(event) {
            let button = document.getElementsByClassName('js-avatar-save')[0];

            let crop = {
                crop: {
                    x:  parseInt(event.detail.x),
                    y: parseInt(event.detail.y),
                    width: parseInt(event.detail.width),
                    height: parseInt(event.detail.height),
                }
            };
            button.setAttribute('crop', JSON.stringify(crop));
        },
    }).replace(src);
}

function reInitialize() {
    $("[data-toggle='tooltip']").tooltip();
    jsBbUploadCount();
}

function jsBbUploadCount () {
    $('.js-bb-upload--count').each(function(e){$(this).text(e + 1);});
}

function updateImage(image) {
    image.attr('src', image.attr('src').split("?")[0] + "?" + new Date().getTime());
}
