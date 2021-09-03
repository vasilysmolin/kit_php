$(document).ready(function () {

    if($(".js-select-differents").length > 0){
        $(".js-select-differents").select2();
    }

    if($('.select2').length > 0) {
        $('.select2').select2();
    }

    if($('.select2-multiple').length > 0) {
        $('.select2-multiple').select2();
    }


    if ($('.js-select-progress-change-event').length > 0) {
        $('.js-select-progress-change-event').on('change',function(){
            const $this = $(this);
            const value = $this.val();
            const $profileTR = $this.closest('tr').children()[0];
            const profileID = $($profileTR).html().trim();
            if(value.length){
                $.ajax({
                    method:'POST',
                    url:'/admin/ajaxChangeUserStatus',
                    data:{
                        status:value,
                        profileID:profileID,
                        _token: token
                    }
                }).done((response)=>{
                    $this.css('color',response.color);
                });
            }
        });
    }

    if($('.js-phone-activate').length > 0){
        $('.js-phone-activate').on('change',function(){
            const $this = $(this);
            const value = $this.val();
            const userTR = $this.closest('tr').children()[0];
            const userID = $(userTR).html().trim();
            $.ajax({
                method:'POST',
                url:'/admin/ajaxChangePhoneActive',
                data:{
                    userID: userID,
                    phoneActive: value,
                    _token:token
                }
            }).done((response)=>{
            })
        });
    }

    if($('.js-address-catalog').length > 0){
        $('.js-address-catalog').on('change',function(){
            const $this = $(this);
            const value = $this.val();
            const addressID = $(this).data('id');
            const userTR = $this.closest('tr').children()[0];
            // const addressID = $(userTR).html().trim();
            $.ajax({
                method:'POST',
                url:'/admin/ajaxChangeAddressCatalog',
                data:{
                    addressID: addressID,
                    catalog: value,
                    _token:token
                }
            }).done((response)=>{
            })
        });
    }

    if ($('.js-select-progress-change-event-user').length > 0) {
        $('.js-select-progress-change-event-user').on('change',function(){
            const value = $(this).val();
            console.log(value);
            const $this = $(this);
            const $userTR = $(this).closest('tr').children()[0];
            const profileID = $($userTR).html().trim();
            if(value.length){
                $.ajax({
                    method:'POST',
                    url:'/admin/ajaxChangeUserStatus',
                    data:{
                        status:value,
                        userID:profileID,
                        _token: token
                    }
                }).done((response)=>{
                    $this.css('color',response.color);
                    // console.log(response);
                });
            }
        });
    }

    // Change user consultant
    let consultantSelect = '.js-select-user-consultant';
    if ($(consultantSelect).length > 0) {
        $(consultantSelect).on('change', function() {
            let consultantID = $(this).val();
            let $userTR = $(this).closest('tr').children()[0];
            let userID = $($userTR).html().trim();
            if (consultantID.length){
                $.ajax({
                    method:'POST',
                    url:'/admin/ajaxUserChangeConsultant',
                    data:{
                        consultantID: consultantID,
                        userID: userID,
                        _token: token
                    }
                });
            }
        });
    }

    // Change active status
    if ($('.js-change-parameter-phone').length > 0) {
        $('.js-change-parameter-phone').on('change', function () {

            var param;
            var field = $(this).data('field');
            var itemID = $(this).data('id');
            var table = $(this).data('table');

            $(this).is(':checked') ? param = 1 : null;
            $.ajax({
                method: 'POST',
                url: '/admin/ajaxChangePhoneActiveStatus',
                data: {
                    itemID: itemID,
                    param: param,
                    field: field,
                    table: table,
                    _token: token
                }
            });
        });
    }

    $('.js-review-user__address').on('change', function () {
        var userID = $(this).val();

        $.ajax({
            method: 'POST',
            url: '/admin/ajaxGetAddress',
            data: {
                userID: userID,
                _token: token
            }
        }).done(function (response) {
            if (response.success === true) {

                var addressID = $('#addressID');

                addressID.empty();
                addressID.html(response.html);

            }
        });

        return false;
    });

    if ($('#jsServiceSelect').length > 0) {
        $(document).on('change', '#jsServiceSelect', function (e) {
            var _this = $(this);
            var _thisVal = _this.val();
            var value = [];


            if (_thisVal !== null) {
                if (_thisVal.length > 0) {
                    for (var i = 0; i < _thisVal.length; i++) {

                        if (i + 1 === _thisVal.length) {
                            value += _thisVal[i];
                        } else {
                            value += _thisVal[i] + ', ';
                        }
                    }
                }
            } else {
                value = " ";
            }

            $('#jsServiceInput').val(value);
        });
    }

    if ($('.jsCatServiceSelect').length > 0) {
        $(document).on('change', '.jsCatServiceSelect', function (e) {
            var _this = $(this);
            $.ajax({
                method: 'POST',
                url: '/admin/ajaxGetCatServiceSelect',
                data: {
                    catServicesID: _this.val(),
                    _token: token
                }
            }).done(function (response) {
                if (response.success === true) {

                    var addressID = $('#addressID');

                    $('#jsServiceInput').val('').trigger("change");
                    $('#jsServiceSelect').html(response.html);
                    $('#jsServiceSelect').select2();
                    $('#jsServiceSelect').val('').trigger("change");
                }
            });

        });
    }

    function colorSelect(e) {
        function build($select) {
            var html = '';
            var listItems = '';
            var span = '<span>выберите цвет</span>';

            $select.find('option').each(function() {
                listItems += ''+
                    '<li style="background:'+$(this).attr('data-color')+'" data-colorVal="'+$(this).attr('data-color')+'" data-color="'+$(this).val()+'">'+
                    '<span>'+$(this).text()+'</span>'+
                    '</li>'
                ;

                if ($(this).attr('selected')) {
                    span = '<span>'+$(this).attr('data-color')+'<span style="background:'+$(this).attr('data-color')+'"></span></span>';
                }
            });

            html = ''+
                '<div class="color-select">'+
                span +
                '<ul>'+listItems+'</ul>'+
                '</div>'
            ;

            return html;
        }

        $(e).each(function() {
            var $this = $(this);

            $this.hide();

            $this.after(build($this));
        });
    };

    $(document).on('click', '.color-select > span', function() {
            $(this).siblings('ul').toggle();
    });

    $(document).on('click', '.color-select li', function() {
            var $this = $(this);
            var color = $this.attr('data-colorVal');
            var colorID = $this.attr('data-color');
            var colorText = $this.find('span').text();
            var $value = $this.parents('.color-select').find('span:first');
            var $select = $this.parents('.color-select').prev('select');

            $value.text(colorText);
            $value.append('<span style="background:'+color+'"></span>');
            $this.parents('ul').hide();
            $select.val(colorID);
        })
    ;

    if($('.jsColorSelect').length) {
        colorSelect($('.jsColorSelect'));
    }

    var settingsAddField = '.jsAdminSettingsAddField';
    var settingsDeleteField = '.jsAdminSettingsDeleteField';

    if ($(settingsAddField).length) {
        $(document).on('click', settingsAddField, function () {
            var $this = $(this);

            $.ajax({
                method: 'POST',
                url: '/admin/ajaxSettingsAddNewField',
                data: {
                    countAddField: $('.jsAdminSettingsAddFieldBlocks').length,
                    _token: token
                }
            }).done(function (response) {
                if (response.success === true) {
                    $this.closest('.form-group').before(response.html);
                }
            });
        });

        $(document).on('click', settingsDeleteField, function () {
            var $this = $(this);
            $this.closest('.form-group').remove();
        });
    }
});
