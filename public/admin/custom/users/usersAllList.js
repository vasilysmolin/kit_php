$(document).ready(function () {
    $('.jsAdminUsersMastersParserActive').on('click', function () {
        $.ajax({
            method: 'POST',
            url: '/admin/ajaxAdminUsersMastersParserActive',
            data: {
                _token: token
            }
        }).done(function (response) {
            if (response.success === true) {
               console.log(123);
            }
        });
    });
});
