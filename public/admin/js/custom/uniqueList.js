$(document).ready(function () {

    $('.js-unique-nickname').on('blur', function () {
        var alias = $(this);
        var userID = $(this).data('user');
        var addressID = $(this).data('address');

        var type = $(this).data('type');
        if (alias.length > 0) {
            if(isNumber(alias.val())){
                $(this).next().css('display', 'block');
                $(this).next().text('Никнейм должен содержать латинские буквы');
                $(this).css('border', '1px solid #c9302c');

            }else{
                if(isEnglish(alias.val())) {
                    $(this).next().css('display', 'block');
                    $(this).next().text('Никнейм не должен содержать русские буквы');
                    $(this).css('border', '1px solid #c9302c');


                }else{
                    $('.bb-input__error').css('display', 'none');
                    $(this).css('border', '1px solid #E3E3E3');

                    uniqueForm(alias, type, addressID);
                }
            }
        }
        return false;
    });

    function uniqueForm(alias, type, addressID) {
        $.ajax({
            method: 'POST',
            url: '/admin/ajaxAdminValidate',
            data: {
                type: type,
                list: true,
                alias: alias.val(),
                addressID: addressID,
                _token: token
            }
        }).done(function (response) {
            if (response.success === true) {
                $(this).next().css('display', 'none');
                $(this).css('border', '1px solid #E3E3E3');
            }
            if (response.success === false) {
                console.log('Никнейм не уникальный');
                alias.next().css('display', 'block');
                alias.next().text('Никнейм не уникальный');
                alias.css('border', '1px solid #c9302c');

            }
        });

    }

    function isNumber(n) { return /^-?[\d.]+(?:e-?\d+)?$/.test(n); }
    function isEnglish(n) { return /-?[А-Яа-яйё]/.test(n); }

});
