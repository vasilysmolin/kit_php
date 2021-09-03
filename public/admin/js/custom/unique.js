$(document).ready(function () {
    var alias = $('.js-unique-nickname');
    var phone = $('.js-unique-phone');
    // var userID = $('#userID').data('user');
    var addressID = $('#addressID').data('address');

    function isNumber(n) { return /^-?[\d.]+(?:e-?\d+)?$/.test(n); }
    function isEnglish(n) { return /-?[А-Яа-яйё]/.test(n); }

    $('.js-unique-nickname').on('keyup', function () {
        if (alias.length > 0) {
            if(isNumber(alias.val())){
                $('.js-unique-nickname').next().css('display', 'block');
                $('.js-unique-nickname').next().text('Никнейм должен содержать латинские буквы');
                $('.js-unique-nickname').css('border', '1px solid #c9302c');
                $('.jsUnique').attr('disabled', 'disabled');
            }else{
                if(isEnglish(alias.val())) {
                    console.log(1);
                    $('.js-unique-nickname').next().css('display', 'block');
                    $('.js-unique-nickname').next().text('Никнейм не должен содержать русские буквы');
                    $('.js-unique-nickname').css('border', '1px solid #c9302c');
                    $('.jsUnique').attr('disabled', 'disabled');

                }else{
                    $('.js-unique-nickname').next().css('display', 'none');
                    $('.js-unique-nickname').css('border', '1px solid #E3E3E3');
                    $('.jsUnique').removeAttr('disabled');

                    uniqueForm(alias, 'nickname');
                }
            }
        }
        return false;
    });

    $('.js-unique-phone').on('keyup', function () {
        if (phone.length > 0) {
            $('.bb-input__error').css('display', 'none');
            $('.js-unique-nickname').css('border', '1px solid #E3E3E3');
            $('.jsUnique').removeAttr('disabled');

            uniqueForm(phone, 'phone');
        }
        return false;
    });

    $('.jsAdminTagControl').on('keyup', function () {
        var $error = $(this).next();
        var $btn = $error.closest('form').find('button');

        $.ajax({
            method: 'POST',
            url: '/admin/ajaxUniqueTagSetting',
            data: {
                tag: $(this).val(),
                settingID: $('.jsAdminTagSettingID').val(),
                _token: token
            }
        }).done(function (response) {
            if (response.success == false) {
               $error.css({'display': 'block'});
               $btn.attr('disabled', 'disabled');
            } else {
                $error.css({'display': 'none'});
                $btn.removeAttr('disabled');
            }
        });
    });

    function uniqueForm(alias, type) {

        $.ajax({
            method: 'POST',
            url: '/admin/ajaxAdminValidate',
            data: {
                type: type,
                alias: alias.val(),
                addressID: addressID,
                _token: token
            }
        }).done(function (response) {
            if(response.phone === true ){

                if (response.success === true) {
                    $('.js-unique-phone').next().css('display', 'none');
                    $('.js-unique-phone').css('border', '1px solid #E3E3E3');
                    $('.jsUnique').removeAttr('disabled');
                }
                if (response.success === false) {
                    $('.js-unique-phone').next().css('display', 'block');
                    $('.js-unique-phone').next().text('телефон занят');
                    $('.js-unique-phone').css('border', '1px solid #c9302c');
                    $('.jsUnique').attr('disabled', 'disabled');
                }

            } else if(response.nickname === true){

                if (response.success === true) {
                    $('.js-unique-nickname').next().css('display', 'none');
                    $('.js-unique-nickname').css('border', '1px solid #E3E3E3');
                    $('.jsUnique').removeAttr('disabled');
                }
                if (response.success === false) {
                    if($('.js-unique-nickname').val().length>0){
                        $('.js-unique-nickname').next().css('display', 'block');
                        $('.js-unique-nickname').next().text('никнайм не уникальный');
                        $('.js-unique-nickname').css('border', '1px solid #c9302c');
                        $('.jsUnique').attr('disabled', 'disabled');
                    }
                }

            }

        });

    }
});
