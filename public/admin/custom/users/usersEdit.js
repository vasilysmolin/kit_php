$(document).ready(function () {
    $('#js-users-roles-type-select').on('change', function () {
        $.ajax({
            method: 'POST',
            url: '/admin/ajaxUsersTakeRolesByType',
            data: {
                typeID: $(this).val(),
                _token: token
            }
        }).done(function (response) {
            if (response.success === true) {
                $('#js-users-roles-select').html(response.html)
            }
        });
    });
    $('#js-users-roles-city-select').on('change', function () {
        $.ajax({
            method: 'POST',
            url: '/admin/ajaxUsersTakeRolesByCity',
            data: {
                cityID: $(this).val(),
                _token: token
            }
        }).done(function (response) {
            if (response.success === true) {
                $('#js-users-roles-districts-select').html(response.htmlDis)
            }
            if (response.success === true) {
                $('#js-users-roles-metro-select').html(response.htmlMetro)
            }
        });
    });

    // $('.js-time-mask').mask("+7 (999) 999-99-99");

    //Accordeon (admin)
    $('.js-bb-accordeon').find('.bb-accordeon__item').find('.bb-accordeon__title').on('click', function () {
        if ($(this).parent().hasClass('is-open')) {
            $(this).parent().removeClass('is-open').find('.bb-accordeon__content').css('display', 'none');
        } else {
            $(this).parent().addClass('is-open').find('.bb-accordeon__content').removeAttr('style');
        }
    });


    $('.js-service-check__div').on('click', function () {
        var _this = $(this);

        if (!_this.hasClass('is-checked')) {
            _this.addClass('is-checked');
            _this.children('input.active').attr( "value", 1 );

        } else {
            _this.removeClass('is-checked');
            _this.children('input.active').attr( "value", 0 );
        }
    });

    $('.js-label-active').on('click', function () {
        var _this = $(this);
            if(!_this.parent('.js-service-check__div').hasClass('is-checked')){
                _this.parent('.js-service-check__div').addClass('is-checked');
                _this.prev('.active').attr( "value", 1 );
            } else {
                _this.parents('.js-service-check__div').removeClass('is-checked');
                _this.prev('.active').attr( "value", 0 );
            }
    });

    var inputElement = $('.js-service-input');
    inputElement.on('input', function () {
        $(this).parent().removeClass('is-invalid');
        if ($(this).val() === '') {
            $(this).parent().addClass('is-invalid');
        }
    });
    inputElement.on('change', function () {
        var _this = $(this);
        var price;
        var time;
        if (_this.hasClass('js-i-time')) {
            if (_this.val() !== '' && _this.parent().prev().children('.js-i-price').val() !== '') {
                price = _this.parent().prev().children('.js-i-price').val();
                time = _this.val();
            }
        }

        if (_this.hasClass('js-i-price')) {
            if (_this.val() !== '' && _this.parent().next().children('.js-i-time').val() !== '') {
                price = _this.val();
                time = _this.parent().next().children('.js-i-time').val();
            }
        }

    });

    $('.service-item__field').on('click', function () {
        var _this = $(this);
        if (_this.find('.js-service-input').attr('readonly')) {
            _this.parent('.service-item').children('.js-service-check__div').addClass('is-checked');
            _this.find('.js-service-input').removeAttr('readonly', '');
            _this.parent('.service-item').find('#active').attr( "value", 1 );
        }
    });

    // Map
    if ($('.js-cabinet__map').length > 0) {
        ymaps.ready(initCabinetMap);
    }

    if ($('.jsGeoMap').length > 0) {
        $(document).on('keyup', '.jsGeoMap', function () {
            geoMap($(this).val(), null, null);
        });
    }

    if ($('#jsCategoriesSelect').length > 0) {
        $(document).on('change', '#jsCategoriesSelect', function () {

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

            $('#jsCategoriesInput').val(value);
        });
    }

    $('.select2').select2();

    if ($("#select-profile").val() != '') {
        const URIString = document.documentURI;
        const dividedString = URIString.split('/');
        $.ajax({
            url: '/admin/ajaxGetUserDepartments',
            type: 'POST',
            data: {
                _token: token,
                userID: dividedString[dividedString.length - 1] // ID пользователя, полученное из адресной строки
            }
            }).done(function(response) {
                $(".select-department-holder").html(response.html);
                $("#select-department").select2();
        });
    }

    $("#select-profile").on("change", function(e) {
        $.ajax({
            url: '/admin/ajaxGetUserDepartmentsByDepartmentId',
            type: 'POST',
            data: {
                _token: token,
                departmentID: $("#select-profile").val()
            }
        }).done(function(response) {
            $(".select-department-holder").html(response.html);
            $("#select-department").select2();
        })
    })
});


var balloon = {
    iconLayout: 'default#image',
    iconImageHref: '/client/files/icons/svg/map-pin.svg',
    iconImageSize: [30, 42],
    iconImageOffset: [-3, -42]
};


function initCabinetMap() {
    var myMap;
    var myPlaceMark;
    var coordXInput = $('#latitude');
    var coordYInput = $('#longitude');
    var coordX;
    var coordY;


    if (coordXInput.val() === '' && coordYInput.val() === '') {
        coordX = 55.742909;
        coordY = 37.6275;
    } else {
        coordX = coordXInput.val();
        coordY = coordYInput.val();
    }

    myMap = new ymaps.Map('card-map', {
        center: [coordX, coordY],
        zoom: 17
    });

    myPlaceMark = new ymaps.Placemark([coordX, coordY], {}, balloon);

    myMap.events.add('click', function (e) {

        var coords = e.get('coords');

        coordXInput.val(coords[0]);
        coordYInput.val(coords[1]);

        myMap.geoObjects.remove(myPlaceMark);
        myPlaceMark = new ymaps.Placemark([coords[0], coords[1]], {}, balloon);
        myMap.geoObjects.add(myPlaceMark);
    });

    // myMap.behaviors.disable(['scrollZoom']);
    myMap.controls.remove('typeSelector');
    myMap.geoObjects.add(myPlaceMark);

    $('.js-profile-contact__city').on('change', function () {
        var cityID = $(this).val();

        $.ajax({
            method: 'POST',
            url: '/ajaxGetMetroOrDistrict',
            data: {
                cityID: cityID,
                _token: token
            }
        }).done(function (response) {
            if (response.success === true) {

                // myMap.setCenter([response.coordY, response.coordX], 13);

                var type = response.type;
                var metroBlock = $('.js-profile-contact__metro-block');
                var districtBlock = $('.js-profile-contact__district-block');
                var select;

                if (type === 'metro') {
                    metroBlock.removeClass('is-hidden');
                    districtBlock.addClass('is-hidden');

                    select = $('.js-profile-contact__metro');
                }

                if (type === 'district') {
                    districtBlock.removeClass('is-hidden');
                    metroBlock.addClass('is-hidden');

                    select = $('.js-profile-contact__district');
                }

                if (type === '') {
                    districtBlock.addClass('is-hidden');
                    metroBlock.addClass('is-hidden');
                } else {
                    var data = JSON.parse(response.html);
                    var arrData = [{'id': 0, 'text': 'Не выбран'}];

                    $.each(data, function (key, name) {
                        arrData.push({'id': key, 'text': name});
                    });
                    select.html('').select2({data: arrData});
                }

                var street = $('.js-profile-contact__home').val();
                var number = $('.js-profile-contact__number').val();

                if(street === ""){
                    street =  null;
                }
                if(number === ""){
                    number = null;
                }
                geoMap(response.cityName, street, number);

            }
        });

        return false;
    });

    $(document).on('keyup', '.js-profile-contact__home', function () {
        var city = $('.js-profile-contact__city').find("option:selected").text();
        var number = $('.js-profile-contact__number').val();
        if(number === ""){
            number = null;
        }
        geoMap(city, $(this).val(), number);
    });

    $(document).on('keyup', '.js-profile-contact__number', function () {
        var city = $('.js-profile-contact__city').find("option:selected").text();
        var street = $('.js-profile-contact__home').val();
        if(street === ""){
            street = null;
        }

        geoMap(city, street, $(this).val());
    });
}

function geoMap(city, street, number) {
    var myGeocoder = "";
    if (city !== null && street !== null && number !== null) {
        myGeocoder = ymaps.geocode(city+', '+street+', '+number);
    } else if (city !== null && street !== null) {
        myGeocoder = ymaps.geocode(city+', '+street);
    } else if (city !== null) {
        myGeocoder = ymaps.geocode(city);
    } else {
        myGeocoder = ymaps.geocode('Москва');
    }

    var myMap;
    var myPlaceMark;
    var coordX;
    var coordY;
    myGeocoder.then(
        function (res) {
            var coords = res.geoObjects.get(0).geometry.getCoordinates();
            myGeocoder.then(
                function (res) {
                    $('#card-map').html('');
                    coordX = coords[0];
                    coordY = coords[1];
                    myMap = new ymaps.Map('card-map', {
                        center: [coordX, coordY],
                        zoom: 17
                    });

                    $('#coordX').attr('value', coordX);
                    $('#coordY').attr('value', coordY);
                    myPlaceMark = new ymaps.Placemark([coordX, coordY], {}, balloon);
                    myMap.geoObjects.add(myPlaceMark);

                    myMap.events.add('click', function (e) {

                        var coords = e.get('coords');

                        $('#coordX').attr('value', coords[0]);
                        $('#coordY').attr('value', coords[1]);
                        myMap.geoObjects.remove(myPlaceMark);
                        myPlaceMark = new ymaps.Placemark([coords[0], coords[1]], {}, balloon);
                        myMap.geoObjects.add(myPlaceMark);
                    });
                }
            );
        }
    );
}
