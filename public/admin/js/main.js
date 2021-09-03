'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 *
 * Copyright (c) 2019 Beautybox Inc. All Rights Reserved.
 *
 * @author Anton Ustinoff <a.a.ustinoff@gmail.com>
 */

//Global Vars
var $(window) = $(window);
var $document = $(document);
var $body = $('body');
var $html = $('html');
var $wrapper = $('.wrapper');
var $overlay = $('.overlay');
var $header = $('.header');
var $main = $('.main');
var $cabinet = $('.cabinet');

/**
 * Base.js
 *
 * @author Anton Ustinoff <a.a.ustinoff@gmail.com>
 */

$(function () {
    Base.init();
    Dropdown.init();
});

var Base = {
    init: function init() {
        this.removePreloader();
        this.removeElements();
        this.initListToggle();
        this.initCopyText();
        this.initTogglePhone();
        this.initCitySelect();
        this.initMobileSearch();
        this.initInputSearch();

        this.Form.init();
        this.Filter.init();

        this.Components.init();
        this.Utils.init();

        //Stop drag Img
        $('img').on('dragstart', function (e) {
            e.preventDefault();
        });
    },


    //Remove preloader
    removePreloader: function removePreloader() {
        setTimeout(function () {
            $('body').removeClass('loading loading-animation is-loading');
            $('._loading').removeClass('_loading');
        }, 1000);
    },
    removeElements: function removeElements() {
        //SimpleBar https://github.com/Grsmto/simplebar
        if ($(window).width() < 768) {
            $document.find('[data-simplebar]').removeAttr('data-simplebar');
        }

        // if ($(window).width() <= 480) {
        //     $document.find('.xs-hide').remove();
        // }
    },
    initListToggle: function initListToggle() {
        var $list = $('.js-list');
        var $checkbox = $list.find('.js-bb-checkbox');
        var $workList = $list.find('.js-list-toggle');
        if ($list.length) {
            if ($list.attr('data-reverse') === 'true') {
                if (!$checkbox.hasClass('is-checked')) {
                    $workList.hide();
                }
                $checkbox.on('click', function () {
                    if ($checkbox.hasClass('is-checked')) {
                        $workList.show();
                    } else {
                        $workList.hide();
                    }
                });
            } else {
                $checkbox.on('click', function () {
                    if ($checkbox.hasClass('is-checked')) {
                        $workList.hide();
                    } else {
                        $workList.show();
                    }
                });
            }
        }
    },


    //Copy text click link
    initCopyText: function initCopyText() {
        if (document.querySelector('.js-user-link')) {
            var cb = new Clipboard('.js-user-link');

            //if has input then copy input value in data attr
            this.initInputSearch();
        }
    },
    initInputSearch: function initInputSearch() {
        var $inputSearch = $document.find('.js-input');
        if ($inputSearch.length) {
            var btnBind = function btnBind() {
                $(this).closest('.js-search').find('.js-input').val('');
                $(this).hide().closest('.js-search').find('.bb-input__icon').not('.js-input--clear').show();

                $(this).closest('.js-search').find('.search__hint').css('display', 'none');
            };

            $inputSearch.each(function () {
                var $inputBox = $(this).closest('.js-input-box');
                var $inputIcon = $inputBox.find('.bb-input__icon');
                var $btnReset = $inputBox.find('.js-input--clear');
                var $btnSearch = $inputBox.find('.bb-input__icon-search');
                var $hint = $(this).closest('.js-search').find('.search__hint');

                $(this).off();

                $(this).on('keyup', function () {
                    var $parent = $(this).closest('.js-input-block');
                    var btn = $parent.find('.js-user-link');
                    var $btnData = $(this).data('clipboard-text');
                    var $inputVal = $(this).val();

                    btn.attr('data-clipboard-text', $btnData + $inputVal);

                    if ($inputVal != ' ') {
                        $btnReset.show();
                        $btnSearch.hide();
                    } else {
                        $btnReset.hide();
                        $btnSearch.show();
                    }
                }).on('blur', function () {
                    if ($(this).val() == '') {
                        if ($inputVal != ' ') {
                            $btnReset.show();
                            $btnSearch.hide();
                        } else {
                            $btnReset.hide();
                            $btnSearch.show();
                        }
                    }
                });
            });

            $('.js-input--clear').unbind('click');

            $('.js-input--clear').on('click', btnBind);
        }
    },


    //Show phone number
    initTogglePhone: function initTogglePhone() {
        $('.js-user-phone').each(function () {
            $(this).attr('href', 'javascript:void(0);').text($(this).data('phone-hiden'));
        });

        $(document).on('click', '.js-user-phone--show', function () {
            var userPhone = $(this).parent().find('.js-user-phone');
            var phone = userPhone.data('phone');
            userPhone.removeAttr('style').attr('href', 'tel:' + phone).text(phone);
            $(this).css('display', 'none');
        });
    },


    //City select
    initCitySelect: function initCitySelect() {
        var $changeCity = $('.js-city-select');
        var $changeCityTitle = $changeCity.find('.city-select__title span');
        var $input = $changeCity.find('input');

        $input.on('click focus', function (e) {
            e.stopPropagation();
        });

        $changeCity.find('.city-select__item').on('click', function () {
            $changeCityTitle.text($(this).text());
        });
    },


    //Mobile Search Btn open/close
    initMobileSearch: function initMobileSearch() {
        var $searchBtn = $('.js-mobile-search-btn');
        var open = false;
        $searchBtn.on('click', function () {
            if (open) {
                $wrapper.addClass('mobile-search--open');
                $('html').css('is-fixed');
                open = false;
                return false;
            } else {
                $wrapper.removeClass('mobile-search--open');
                $('html').removeClass('is-fixed');
                open = true;
                return false;
            }
        });
    },


    Form: {
        init: function init() {
            this._addEventListener();
        },
        checkValidation: function checkValidation() {
            var _this2 = this;

            var $btn = $('.form-success__role');
            var $formSuccess = $('.form-success__roles');

            $(this).css('z-index', '200');

            $btn.not($(this)).addClass('move-out');
            $formSuccess.addClass('is-error');

            setTimeout(function () {
                $btn.not($(_this2)).hide();
            }, 100);
        },
        _addEventListener: function _addEventListener() {
            $('.js-success-close').on('click', function (e) {
                $(e.target).closest('.choose-role').removeClass('choose-role');
            });

            $('.js-error-close').on('click', function (e) {
                $(e.target).closest('.is-error').removeClass('is-error');
            });
        }
    },

    Filter: {
        open: false,

        init: function init() {
            this._addEventListener();
        },
        _addEventListener: function _addEventListener() {
            var _this3 = this;

            $('.js-mobile-filter--open').on('click', function (e) {
                _this3._toggle(e);
            });
            $('.js-catalog-filter--close').on('click', function (e) {
                if (_this3.open) {
                    _this3._toggle(e);
                }
            });
        },
        _toggle: function _toggle(e) {
            var OPEN_CLASS = 'is-open';
            var $catalogFilter = $(document).find('.catalog-filter');
            if (!this.open) {
                $('html').addClass('is-fixed');
                $catalogFilter.addClass(OPEN_CLASS);
                this.open = true;

                if ($(window).width() > 480) {
                    $overlay.addClass('is-visible overlay--filter');
                }
            } else {
                $('html').removeAttr('style').removeClass('is-fixed');
                $overlay.removeClass('is-visible overlay--filter');
                $catalogFilter.removeClass(OPEN_CLASS);
                this.open = false;
            }
            e.preventDefault();
        }
    }
};

Base.define = function (namespace) {
    var parts = namespace.split('.'),
        parent = Base,
        i;

    //Убрать  начальный префикс если это являетсся глобальной переменной
    if (parts[0] == 'Base') {
        parts = parts.slice(1);
    }

    //Если в глобальном объекте  нет  свойства  - создать  его.
    for (var i = 0; i < parts.length; i++) {
        if (typeof parent[parts[i]] == 'undefined') {
            parent[parts[i]] = {};
        }
        parent = parent[parts[i]];
    }
    return parent;
};

/**
 * Base.Utils.js
 *
 * @author Anton Ustinoff <a.a.ustinoff@gmail.com>
 */

Base.define('Utils');

Base.Utils = function () {
    function _init() {
        _detectBrowserType();
        _initLazyLoadImage();
    }

    //Проверка типа браузера
    function _detectBrowserType() {
        window.addEventListener('load', function () {
            var html = document.querySelector('html');
            var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
            var isChrome = !!window.chrome && !isOpera;
            var isExplorer = typeof document !== 'undefined' && !!document.documentMode && !isEdge;
            var isFirefox = typeof window.InstallTrigger !== 'undefined';
            var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;

            // if (/windows phone/i.test(userAgent)) {
            //     html.classList.add('is-windows');
            // }

            if (/android/i.test(userAgent)) {
                html.classList.add('is-android');
            }

            if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                html.classList.add('is-ios');
            }

            if (isChrome) {
                html.classList.add('is-chrome');
            } else if (isSafari) {
                html.classList.add('is-safari');
            } else if (isFirefox) {
                html.classList.add('is-firefox');
            }
        });
    }

    //Проверка скорости соединения
    function _testSlowConnection() {
        var connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;

        var slowSpeed = void 0;

        if (typeof connection !== 'undefined' && connection.effectiveType === '3g') {
            slowSpeed = true;
        } else {
            slowSpeed = false;

            var loadTime = (window.performance.timing.domContentLoadedEventEnd - window.performance.timing.navigationStart) / 1000;

            if (loadTime > 4) {
                slowSpeed = true;
            }
        }
        return slowSpeed;
    }

    function _initLazyLoadImage() {
        new LazyLoad({
            elements_selector: '.lazyload'
        });
    }

    function _slideUp() {
        $(this).closest('[data-container]').slideUp();
    }

    function _slideToggle() {
        var OPEN_CLASS = 'is-open';
        var $container = $(this).closest('[data-container]');
        var $content = $container.find('[data-content]');

        if (!$container.hasClass(OPEN_CLASS)) {
            $content.slideDown();
            $container.addClass(OPEN_CLASS);
        } else {
            $content.slideUp();
            $container.removeClass(OPEN_CLASS);
        }
    }

    function _copyBox() {
        var $clone = $(this).closest('[data-copy-box]').clone();
        $clone.insertAfter($(this));
    }

    function _animateWidth() {
        var _this4 = this;

        var width = $(this).data('width');
        setTimeout(function () {
            $(_this4).animate({ width: width }, 600);
        }, 300);
        console.log('--- width', width);
        console.log('---', 'animate width');
    }

    function _fieldEdit() {
        var fieldEdit = '.js-field-edit';
        var $fieldEdit = $(document).find('.js-field-edit');

        if ($fieldEdit.length) {
            $fieldEdit.each(function () {
                var $fieldEditInput = $(this).find('.field-edit__input');
                var $fieldEditBtn = $(this).find('.field-edit__btn');

                $fieldEditBtn.on('click', function () {
                    var $fieldEditInput = $(this).closest(fieldEdit).find('.field-edit__input');

                    var $fieldEditText = $(this).closest(fieldEdit).find('.field-edit__text');

                    var fieldEditText = $fieldEditText.text();

                    $(this).hide();
                    $fieldEditText.hide();

                    $fieldEditInput.val(fieldEditText).show().select();
                });

                $fieldEditInput.blur(function () {
                    var $fieldEditText = $(this).closest(fieldEdit).find('.field-edit__text');

                    if ($.trim(this.value) == '') {
                        this.value = this.defaultValue ? this.defaultValue : '';
                    } else {
                        $fieldEditText.html(this.value);
                    }

                    $(this).hide();
                    $fieldEditBtn.show();
                    $fieldEditText.show();
                }).keypress(function (event) {
                    var $fieldEditText = $(this).closest(fieldEdit).find('.field-edit__text');

                    if (event.keyCode == '13') {
                        if ($.trim(this.value) == '') {
                            this.value = this.defaultValue ? this.defaultValue : '';
                        } else {
                            $fieldEditText.html(this.value);
                        }

                        $(this).hide();
                        $fieldEditBtn.removeAttr('style');
                        $fieldEditText.show();
                    }
                });
            });

            $(document).on('touchstart', function (e) {
                if ($(e.target).closest(fieldEdit).length) return;
                var $input = $fieldEdit.find('input');
                var $text = $fieldEdit.find('.field-edit__text');
                var $btn = $fieldEdit.find('.field-edit__btn');

                if ($.trim($input.value) == '') {
                    $input.value = $input.defaultValue ? $input.defaultValue : '';
                } else {
                    $text.html($input.value);
                }

                $fieldEdit.find('input').hide();
                $fieldEdit.find('.field-edit__text').show();
                $fieldEdit.find('.field-edit__btn').show();
            });
        }
    }

    function _toggleClassAtBlock(block, cl) {
        $(block + '--open').on('click', function () {
            $(block).addClass(cl);
        });

        $(block + '--close').on('click', function () {
            $(block).removeClass(cl);
        });
    }

    function _toggleClassAtBlockClickOutside(block, cl) {
        $(block).on('click', function () {
            $(this).toggleClass(cl);
        });

        $(document).on('click touchstart', function (e) {
            if ($(e.target).closest(block).length) return;
            $(block).removeClass(cl);
            e.stopPropagation();
        });
    }

    return {
        init: _init,
        initLazyLoadImage: _initLazyLoadImage,
        slideUp: _slideUp,
        slideToggle: _slideToggle,
        copyBox: _copyBox,
        animateWidth: _animateWidth,
        fieldEdit: _fieldEdit,
        toggleClassAtBlock: _toggleClassAtBlock,
        toggleClassAtBlockClickOutside: _toggleClassAtBlockClickOutside,
        testSlowConnection: _testSlowConnection
    };
}();

/**
 * Base.Components.js
 *
 * @author Anton Ustinoff <a.a.ustinoff@gmail.com>
 */

Base.define('Components');

Base.Components = function () {
    function _init() {
        _initCheckBox();
        _initCheckBoxSelectAll();
        _initTab();
        _initChanger();
        _initBoxColor();
        _initParallax();
        _initAccordeon();
        _initProgressLine();
        _initCopyBox();
        _initViewer();
        _initVideoLoader();
        _initStickyBlock();
        _initServicesBtnInfo();

        BB.Core.Button.init();
        BB.Core.Input.init();
        BB.Core.Popup.init();
        BB.Core.Select.init();
        BB.Core.Slider.init();
    }

    function _initCheckBox() {
        new CheckBox({ selector: '.js-bb-checkbox' }).init();
        new Radio({ selector: '.js-bb-radio' });
    }

    function _initCheckBoxSelectAll() {
        $(document).on('click', '.js-bb-checkbox--select-all', function () {
            if ($(this).hasClass('is-selected')) {
                $(this).removeClass('is-selected');
                var $checkboxs = $(this).closest('div').find('.js-bb-checkbox');
                $checkboxs.each(function () {
                    if (!$(this).hasClass('js-no-all-check')) {
                        $(this).removeClass('is-checked').find('input').removeAttr('checked');
                    }
                });
            } else {
                $(this).addClass('is-selected');
                var _$checkboxs = $(this).closest('div').find('.js-bb-checkbox');
                _$checkboxs.each(function () {
                    if (!$(this).hasClass('js-no-all-check')) {
                        $(this).addClass('is-checked').find('input').prop('checked', 'checked').attr('checked', true);
                    }
                });
            }
            return false;
        });
    }

    function _initTab() {
        var $tabs = $('.js-bb-tab');

        if ($tabs.length) {
            console.log('---', 'init');
            $tabs.tabs();
        }
    }

    function _initChanger() {
        var $changer = $document.find('.js-changer');
        var CHECKED_CLASS = 'is-checked';

        if ($changer.length) {
            $changer.each(function () {
                var $item = $(this).find('.changer__item');
                var $btnReset = $(this).find('.changer__reset');

                $item.on('click', function (e) {
                    $item.not($(this)).removeClass(CHECKED_CLASS);
                    $(this).addClass(CHECKED_CLASS);

                    // e.stopPropagation(); Не раскоментировать
                    // e.preventDefault(); Не раскоментировать
                });

                $btnReset.on('click', function (e) {
                    var $parent = $(this).closest('.changer__item');

                    $parent.removeClass(CHECKED_CLASS);
                    // e.stopPropagation(); Не раскоментировать
                });
            });
        }
    }

    function _initBoxColor() {
        new BoxColor();
    }

    function _initParallax() {
        new ParallaxBlock({ selector: '.js-parallax', transform: true }).init();
    }

    function _initProgressLine() {
        var elements = document.querySelectorAll('[data-progress]');
        if (elements.length) {
            setTimeout(function () {
                elements.forEach(function (elem) {
                    var data = elem.getAttribute('data-progress');
                    if (data > 100) return;
                    if (data < 90) {
                        elem.style.width = parseInt(data) + 5 + '%';
                        setTimeout(function () {
                            elem.style.width = parseInt(data) + '%';
                        }, 1000);
                    } else {
                        elem.style.width = parseInt(data) + '%';
                    }
                });
            }, 1000);
        }
    }

    function _initAccordeon() {
        new Accordeon({ selector: '.js-bb-accordeon' }).init();
    }

    function _initCopyBox() {
        new CopyBox({ selector: '.js-copy-box' }).init();
    }

    function _initViewer() {
        new Viewer({ selector: '.js-viewer' }).init();
    }

    function _initVideoLoader() {
        new VideoLoader().init();
    }

    function _initStickyBlock() {
        var stickyBlock = '[data-sticky]';
        var $stickyBlock = $(stickyBlock);
        var stickyBlockContainer = '[data-sticky-container]';
        var stickyBlockInner = '[data-sticky-inner]';
        var maxWindowWidth = $stickyBlock.data('sticky-width') || 768;
        var stickyBlockTopSpasing = $stickyBlock.data('sticky-top-spacing') || 10;
        var stickyBlockBottomSpasing = $stickyBlock.data('sticky-bottom-spacing') || 10;

        if ($stickyBlock.length && $(window).width() > maxWindowWidth) {
            new StickySidebar(stickyBlock, {
                topSpacing: stickyBlockTopSpasing,
                bottomSpacing: stickyBlockBottomSpasing,
                containerSelector: stickyBlockContainer,
                innerWrapperSelector: stickyBlockInner
            });
        }
    }

    function _initServicesBtnInfo() {
        var infoBtn = document.querySelectorAll('.js-card-services-item--info');
        if (infoBtn.length) {
            var _loop = function _loop(i) {
                var parent = infoBtn[i].closest('.card-services-item');
                var itemDescription = parent.querySelector('.card-services-item__desc');
                var btnClose = itemDescription.querySelector('.card-services-item__desc-hide');
                infoBtn[i].addEventListener('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    BB.Utils.slideToggle.call(infoBtn[i]);
                });
                btnClose.addEventListener('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    BB.Utils.slideToggle.call(infoBtn[i]);
                });
            };

            for (var i = 0; i < infoBtn.length; i++) {
                _loop(i);
            }
        }
    }

    return {
        init: _init,
        initCheckBox: _initCheckBox,
        initCheckBoxSelectAll: _initCheckBoxSelectAll,
        initTab: _initTab,
        initChanger: _initChanger,
        initBoxColor: _initBoxColor,
        initParallax: _initParallax,
        initProgressLine: _initProgressLine,
        initAccordeon: _initAccordeon,
        initCopyBox: _initCopyBox,
        initViewer: _initViewer,
        initVideoLoader: _initVideoLoader,
        initStickyBlock: _initStickyBlock,
        initServicesBtnInfo: _initServicesBtnInfo,
    };
}();

/* html example Accordeon
<div class="bb-accordeon js-bb-accordeon">
	<div class="bb-accordeon__item">
		<div class="bb-accordeon__title"></div>
		<div class="bb-accordeon__content"></div>
    </div>
</div>
*/

var Accordeon = function () {
    function Accordeon(args) {
        _classCallCheck(this, Accordeon);

        this.selector = args.selector;
        this.title = $(this.selector).find('[data-accordeon-title]');
        this.content = $(this.selector).find('[data-accordeon-content]');
        // this.all = $(this.selector).attr('data-accordeon-all');
        this.item = $(this.selector).find('.bb-accordeon__item');
        this.run = true;
    }

    _createClass(Accordeon, [{
        key: 'init',
        value: function init() {
            if (typeof this.selector !== 'undefined') {
                this.item.each(function () {
                    var $content = $(this).find('.bb-accordeon__content');

                    if ($(this).hasClass('is-open')) {
                        $content.slideDown();
                    } else {
                        $content.slideUp();
                    }
                });

                this._sameInit();
                this._addIventListener();
            }
        }

        //Render arrow icon in accordeon title

    }, {
        key: '_renderIcon',
        value: function _renderIcon(insertInto) {
            var icon = '<svg class="icon icon-angle--bold " xmlns="http://www.w3.org/2000/svg" viewBox="0 0 238.003 238.003"><path d="M181.776 107.719L78.705 4.648c-6.198-6.198-16.273-6.198-22.47 0s-6.198 16.273 0 22.47l91.883 91.883-91.883 91.883c-6.198 6.198-6.198 16.273 0 22.47s16.273 6.198 22.47 0l103.071-103.039a15.741 15.741 0 0 0 4.64-11.283c0-4.13-1.526-8.199-4.64-11.313z"></path></svg>';

            $(icon).appendTo(insertInto);
        }

        //Инициализация аккордеона по дата атрибутам на разрешении < 480

    }, {
        key: '_initDataAccordeon',
        value: function _initDataAccordeon() {
            var mainScope = this;
            var $item = $(document).find('[data-accordeon-item]');

            $('[data-accordeon]').addClass('bb-accordeon js-bb-accordeon');

            $('[data-accordeon]').each(function () {
                var customClass = $(this).data('accordeon-class');
                $(this).addClass(customClass);
            });

            $('[data-accordeon-title]').addClass('bb-accordeon__title');

            $('[data-accordeon-title]').each(function () {
                mainScope._renderIcon($(this));
            });

            $('[data-accordeon-content]').addClass('bb-accordeon__content').hide();

            $item.addClass('bb-accordeon__item');

            $item.each(function () {
                if ($(this).attr('data-accordeon-item') === 'open') {
                    $(this).addClass('is-open').find('[data-accordeon-content]').show();
                }
            });
        }
    }, {
        key: '_sameInit',
        value: function _sameInit() {
            if ($(window).width() <= 480) {
                this._initDataAccordeon();
            } else {
                // if (typeof this.all != 'undefined') {
                //     this._initDataAccordeon();
                // } else {
                //     this.destroy();
                // }
                this.destroy();
            }
        }
    }, {
        key: '_addIventListener',
        value: function _addIventListener() {
            var _this5 = this;

            $(document).on('click', '.js-bb-accordeon .bb-accordeon__title', function (e) {
                if (!$(_this5).parent().hasClass('jsCrmAddServicesToCategoryForCard')) {
                    if (_this5.run) {
                        _this5.run = false;
                        _this5._toggle(e);

                        setTimeout(function () {
                            _this5.run = true;
                        }, 500);
                    }
                }
            });

            $(window).on('resize', function () {
                _this5._sameInit();
            });
        }
    }, {
        key: '_toggle',
        value: function _toggle(e) {
            var mainScope = this;
            var $target = $(e.target);
            var $parent = $target.closest('.js-bb-accordeon');
            var $item = $target.parent('.bb-accordeon__item');
            var OPEN_CLASS = 'is-open';

            if ($parent.data('accordeon') === 'collapse') {
                if ($item.hasClass(OPEN_CLASS)) {
                    $item.removeClass(OPEN_CLASS).find('.bb-accordeon__content').slideUp();
                    // console.log('--- run', mainScope.run);
                } else {
                    $parent.find('.bb-accordeon__item').removeClass(OPEN_CLASS).find('.bb-accordeon__content').slideUp();
                    $item.addClass(OPEN_CLASS).find('.bb-accordeon__content').slideDown();

                    // mainScope.run = true;
                    // console.log('--- run', mainScope.run);
                }
            } else {
                if ($item.hasClass(OPEN_CLASS)) {
                    $item.removeClass(OPEN_CLASS).find('.bb-accordeon__content').slideUp();
                } else {
                    $item.addClass(OPEN_CLASS).find('.bb-accordeon__content').slideDown();
                }
            }

            e.stopPropagation();
            e.preventDefault();
        }
    }, {
        key: 'destroy',
        value: function destroy() {
            var accord = $(document).find('[data-accordeon]');
            accord.each(function () {
                var $this = $(this);
                $this.removeClass('bb-accordeon bb-accordeon--custom');
                $this.find('[data-accordeon-item]').removeClass('is-open').removeClass('bb-accordeon__item');
                $this.find('[data-accordeon-title]').removeClass('bb-accordeon__title').find('.icon').remove();
                $this.find('[data-accordeon-content]').removeClass('bb-accordeon__content').removeAttr('style');
            });
        }
    }]);

    return Accordeon;
}();

/* html example
<label class="bb-checkbox js-bb-checkbox">
    <input type="checkbox">
    <span class="bb-checkbox__box"></span>
    <span class="bb-checkbox__title">WhatsApp</span>
</label>
*/

var CheckBox = function () {
    function CheckBox(args) {
        _classCallCheck(this, CheckBox);

        this.selector = document.querySelectorAll(args.selector);

        if (typeof this.selector == 'undefined') {
            return;
        }

        // this.init();
        this.checkStatus();
    }

    _createClass(CheckBox, [{
        key: 'init',
        value: function init() {
            var checkbox = this.selector;
            for (var i = 0; i < checkbox.length; i++) {
                checkbox[i].addEventListener('click', function (e) {
                    var elem = this;
                    var elementToggle = elem.querySelector('input[type="checkbox"]');
                    if (elementToggle.checked) {
                        elem.classList.remove('is-checked');
                        elementToggle.checked = false;
                        elementToggle.removeAttribute('checked');
                    } else {
                        elem.classList.add('is-checked');
                        elementToggle.checked = true;
                        elementToggle.setAttribute('checked', 'checked');
                    }
                    e.preventDefault();
                });
            }
        }
    }, {
        key: 'checkStatus',
        value: function checkStatus() {
            var check = [];
            var noCheck = [];
            var togCheck = [];
            var togNoCheck = [];
            var checkbox = this.selector;

            var i = void 0;
            var len = void 0;

            for (i = 0, len = checkbox.length; i < len; i++) {
                var elem = checkbox[i];
                var elementToggle = elem.querySelector('input[type="checkbox"]');
                if (typeof elementToggle == 'null') {
                    if (!elementToggle.checked) {
                        noCheck.push(elem);
                        togNoCheck.push(elementToggle);
                    } else {
                        check.push(elem);
                        togCheck.push(elementToggle);
                    }
                }
            }
            for (i = 0, len = check.length; i < len; i++) {
                check[i].classList.add('is-checked');
            }

            for (i = 0, len = noCheck.length; i < len; i++) {
                noCheck[i].classList.remove('is-checked');
            }

            for (i = 0, len = togNoCheck.length; i < len; i++) {
                togNoCheck[i].removeAttribute('checked');
                togNoCheck[i].checked = false;
            }

            for (i = 0, len = togCheck.length; i < len; i++) {
                togCheck[i].setAttribute('checked', 'checked');
                togCheck[i].checked = true;
            }
        }
    }]);

    return CheckBox;
}();

// html example
// <label class="bb-checkbox bb-checkbox--radio js-bb-radio">
//     <input name="role" type="radio">
//     <span class="bb-checkbox__box"></span>
//     <span class="bb-checkbox__title">Салон</span>
// </label>

var Radio = function () {
    function Radio(args) {
        _classCallCheck(this, Radio);

        this.selector = args.selector;

        if (typeof this.selector == 'undefined') {
            return;
        }

        this.init();
    }

    _createClass(Radio, [{
        key: 'init',
        value: function init() {
            var mainScope = this;
            $(this.selector).click(function (event) {
                var element = $(this),
                    elementToggle = element.find('input[type="radio"]') || element.find('.bb-checkbox__toggle');
                var elementToggleName = elementToggle.attr('name');
                var allElements = $(mainScope.selector, '[name="' + elementToggleName + '"]').prevObject;
                for (var a = 0; a < allElements.length; a++) {
                    if (allElements[a] != elementToggle[0]) {
                        var otherRadio = $(mainScope._getClickElement(mainScope.selector.split('.')[1], allElements[a])),
                            otherRadioToggle = otherRadio.find('input[type="radio"]');
                        otherRadioToggle.removeAttr('checked');
                        otherRadioToggle.prop('checked', false).trigger('change');
                        otherRadio.removeClass('is-checked');
                    }
                }
                if (!elementToggle.prop('checked')) {
                    elementToggle.attr('checked', 'checked');
                    elementToggle.prop('checked', true).trigger('change');
                    element.addClass('is-checked');
                }
            });
        }
    }, {
        key: '_getClickElement',
        value: function _getClickElement(elementClass, newTarget) {
            var target = newTarget != undefined ? newTarget : event.target,
                body = document.querySelector('body');
            while (!target.classList.contains(elementClass) && target != body) {
                target = target.parentNode;
            }
            return target == body ? undefined : target;
        }
    }]);

    return Radio;
}();

/* html example PushUp
<button
	data-push
	data-push-delay="2500"
	data-push-message-error="{{ messageError }}"
	data-push-message-success="{{ messageSuccess }}"
	data-push-status="{{ messageStatus | escape }}"
>
</button>
*/

function pushUp(options) {
    var text = options.text || 'Вам новая заявка';
    var status = options.status || 'success';
    var timeOut = options.timeOut || 1500;

    var $pushContainer = $('<div>').addClass('push-up push-up--center push-up--transparent');
    var $pushIconSuccess = $('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"\n        width="611.994px" height="611.994px" viewBox="0 0 611.994 611.994"\n        xml:space="preserve" class="push-up__icon">\n            <path d="M248.172,423.918l-89.545-89.534c-5.637-5.637-5.637-14.778,0-20.416c5.643-5.644,14.78-5.644,20.417,0l69.128,69.122\n                l184.796-184.802c5.644-5.643,14.78-5.643,20.417,0c5.644,5.637,5.644,14.78,0,20.417L248.172,423.918z"/>\n                <path d="M306.031,611.994v-14.438l-0.022,14.438C137.286,611.994,0.012,474.726,0,306.003C0,137.274,137.274,0,305.997,0\n                    c168.729,0,305.997,137.274,305.997,305.997C612,474.726,474.743,611.994,306.031,611.994z M305.997,28.878\n                    c-152.805,0-277.119,124.314-277.119,277.119C28.89,458.796,153.209,583.11,306.009,583.11h0.022\n                    c152.788,0,277.091-124.314,277.091-277.113C583.122,153.192,458.802,28.878,305.997,28.878z"/>\n        </svg>');

    var $pushIconError = $('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"\n            viewBox="0 0 78.561 78.561" xml:space="preserve" class="push-up__icon">\n            <circle cx="39.28" cy="57.772" r="3.632"/>\n            <path d="M38.792,48.347c1.104,0,2-0.896,2-2v-19c0-1.104-0.896-2-2-2s-2,0.896-2,2v19C36.792,47.451,37.688,48.347,38.792,48.347z\n                "/>\n            <path d="M46.57,11.542l-0.091-0.141c-1.852-2.854-3.766-5.806-7.199-5.806c-3.578,0-5.45,2.994-7.26,5.891\n                c-0.009,0.014-0.065,0.104-0.074,0.119L0.278,65.266C0.096,65.574,0,65.735,0,66.092c0,3.896,3.135,6.874,6.988,6.874h64.585\n                c3.854,0,6.988-2.979,6.988-6.874c0-0.357-0.096-0.614-0.277-0.921L46.57,11.542z M71.573,68.966H6.988\n                c-1.461,0-2.717-0.951-2.95-2.394l31.374-53.061c1.554-2.487,2.572-3.963,3.868-3.963c1.261,0,2.457,1.87,3.843,4.006\n                l31.399,53.007C74.29,68.003,73.034,68.966,71.573,68.966z"/>\n        </svg>\n');

    $pushContainer.text(text).appendTo($('body'));

    if (status === 'error') {
        $pushContainer.addClass('is-error');
        $pushIconError.prependTo($pushContainer);
    } else {
        $pushContainer.addClass('is-success');
        $pushIconSuccess.prependTo($pushContainer);
    }

    _poshPos();

    window.requestAnimationFrame(function () {
        $pushContainer.addClass('is-active');
    });

    setTimeout(function () {
        $pushContainer.removeClass('is-active');
        _poshPos();
    }, timeOut);

    setTimeout(function () {
        $pushContainer.remove();
        _poshPos();
    }, timeOut + 500);

    $(document).on('click', '.js-push-up--close', function () {
        var $parent = $(this).closest('.push-up');
        $parent.removeClass('is-active');
        setTimeout(function () {
            $parent.remove();
        }, 300);
        _poshPos();
    });

    function _poshPos() {
        $('.push-up').each(function (e) {
            var height = $('.push-up').outerHeight(true);
            $(this).css('top', height * e + 10 + e);
        });
    }
}

var Viewer = function () {
    function Viewer(args) {
        _classCallCheck(this, Viewer);

        this.selector = document.querySelectorAll(args.selector);
    }

    _createClass(Viewer, [{
        key: 'init',
        value: function init() {
            if (!Element.prototype.remove) {
                Element.prototype.remove = function remove() {
                    if (this.parentNode) {
                        this.parentNode.removeChild(this);
                    }
                };
            }
            this.hide();
            this.btnHermit();
        }
    }, {
        key: 'hide',
        value: function hide() {
            var i = void 0;
            var l = void 0;
            var r = void 0;
            var len = void 0;
            var item = void 0;
            var startViewBlock = void 0;
            var elems = void 0;
            var btn = void 0;
            var newElems = [];
            var array = this.selector;
            var t = void 0;

            for (i = 0, len = array.length; i < len; i++) {
                item = array[i];
                elems = item.childNodes;
                newElems.length = 0;
                btn = undefined;
                startViewBlock = item.getAttribute('data-view-block') || 4;

                if (item.lastElementChild.classList.contains('js-viewer-btn') || item.lastElementChild.classList.contains('js-viewer-all-btn')) {
                    btn = item.lastElementChild;
                    l = elems.length - 2;
                    t = true;
                } else {
                    l = elems.length;
                    t = false;
                }

                if (l >= startViewBlock) {
                    for (r = 0; r < l; r++) {
                        if (r & 1) {
                            newElems.push(elems[r]);
                        }
                    }

                    if (t) {
                        for (r = 0, l = newElems.length; r < l; r++) {
                            if (r + 1 > startViewBlock) {
                                newElems[r].style.display = 'none';
                            }
                        }
                        this.events(btn);
                    } else {
                        if (newElems[0].classList.contains('js-viewer-btn') || newElems[0].classList.contains('js-viewer-all-btn')) {
                            btn = newElems[0];
                            newElems.slice(0, 1);
                            this.events(btn);
                        }

                        for (r = 0, l = newElems.length; r < l; r++) {
                            if (r + 1 > startViewBlock) {
                                newElems[r].style.display = 'none';
                            }
                        }
                    }
                } else {
                    item.removeChild(btn);
                }
            }
        }
    }, {
        key: 'events',
        value: function events(btn) {
            // провереям, есть ли у элементов метод удалениея, если нет, то добавляем его

            if (btn.classList.contains('js-viewer-btn')) {
                btn.removeEventListener('click', showNeighbors, false);
                btn.addEventListener('click', showNeighbors, false);
            }

            if (btn.classList.contains('js-viewer-all-btn')) {
                btn.removeEventListener('click', showAll, false);
                btn.addEventListener('click', showAll, false);
            }

            // функция показа только соседей
            function showNeighbors() {
                var elems = this.parentElement.childNodes;
                for (var i = 0, len = elems.length; i < len; i++) {
                    if (i & 1) {
                        elems[i].removeAttribute('style');
                    }
                }
                this.remove();
            }

            //функция показа всех дочерних элементов
            function showAll() {
                var elems = this.parentElement.querySelectorAll('[style="display: none;"]');
                for (var i = 0, len = elems.length; i < len; i++) {
                    elems[i].removeAttribute('style');
                }
                this.remove();
            }
        }

        // вешаем событие на кнопку, если она лежит на одном уровне с блоком js-viewer

    }, {
        key: 'btnHermit',
        value: function btnHermit() {
            var btn = document.querySelectorAll('.js-viewer-hermit-btn');
            if (btn.length) {
                var i = void 0; //счетчик
                var len = btn.length;
                for (i = 0; i < len; i++) {
                    btn[i].addEventListener('click', function () {
                        var elem = this.parentElement.querySelector('.js-viewer').childNodes;
                        var r = void 0; //счетчик
                        var l = void 0; //счетчик
                        for (r = 0, l = elem.length; r < l; r++) {
                            if (r & 1) {
                                elem[r].removeAttribute('style');
                            }
                        }

                        this.remove();
                    });
                }
            }
        }
    }]);

    return Viewer;
}();

/*  html example
    <div class="js-copy-box">
        @content
        <button class="js-copy-box-btn"></button>
    </div>
*/

var CopyBox = function () {
    function CopyBox(args) {
        _classCallCheck(this, CopyBox);

        this.selector = args.selector;

        if (typeof this.selector == 'undefined') {
            return;
        }

        this._onClick = this._onClick.bind(this);
    }

    _createClass(CopyBox, [{
        key: 'init',
        value: function init() {
            this._addEventListeners();
        }
    }, {
        key: '_addEventListeners',
        value: function _addEventListeners() {
            var _this6 = this;

            document.addEventListener('click', function (e) {
                _this6._onClick(e);
            });
            document.addEventListener('click', function (e) {
                _this6._removeElement(e);
            });
        }

        //Копируем элемент очищая его поля инпутов, селектов и вставляем клон выше

    }, {
        key: '_onClick',
        value: function _onClick(e) {
            var $target = $(e.target);

            if ($target.closest(this.selector + '-btn--add').length) {
                var $parent = $target.closest(this.selector);
                var $clone = void 0;

                $clone = $parent.clone().addClass('is-cloned');
                $clone.find('input, select, textarea').val('');
                $clone.find('[data-copy-remove]').remove();
                $clone.insertBefore($parent);
            }
        }
    }, {
        key: '_removeElement',
        value: function _removeElement(e) {
            var $target = $(e.target);

            if ($target.closest(this.selector + '-btn--remove').length) {
                $target.closest(this.selector).remove();
            }
        }
    }]);

    return CopyBox;
}();

/* <div class="video__wrap">
<div class="video">
    <a class="video__link" href="https://youtu.be/vU3dN_qcLLo">
        <picture>
            <source
                srcset="https://i.ytimg.com/vi_webp/vU3dN_qcLLo/maxresdefault.webp"
                type="image/webp"
            />            <img
                class="video__media"
                src="https://i.ytimg.com/vi/vU3dN_qcLLo/maxresdefault.jpg"
                alt="Ведущая Татьяна Скоцкая"
            />
        </picture>
    </a>
    <button
        class="video__button"
        type="button"
        aria-label="Запустить видео"
    >
        <svg width="68" height="48" viewBox="0 0 68 48">
            <path
                class="video__button-shape"
                d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z"
            ></path>
            <path class="video__button-icon" d="M 45,24 27,14 27,34"></path>
        </svg>
    </button>
</div>
</div> */

// .video {
//     position: relative;
//     width: 100%;
//     height: 0;
//     padding-bottom: 56.25%;
//     background-color: #000000;

//     &--enabled {
//         cursor: pointer;

//         & .video__button {
//             display: block;
//         }
//     }

//     &__link {
//         position: absolute;
//         top: 0;
//         left: 0;
//         width: 100%;
//         height: 100%;
//     }

//     &__media {
//         position: absolute;
//         top: 0;
//         left: 0;
//         width: 100%;
//         height: 100%;
//         border: none;
//     }

//     &__button {
//         position: absolute;
//         top: 50%;
//         left: 50%;
//         z-index: 1;
//         display: none;
//         padding: 0;
//         width: 68px;
//         height: 48px;
//         border: none;
//         background-color: transparent;
//         transform: translate(-50%, -50%);
//         cursor: pointer;

//         &:focus {
//             outline: none;
//         }
//     }

//     &__button-shape {
//         fill: #212121;
//         fill-opacity: 0.8;
//     }

//     &__button-icon {
//         fill: #ffffff;
//     }

//     &__wrap {
//         margin: 100px auto;
//         width: 50vw;
//         display: flex;
//         justify-content: center;
//         align-items: center;

//         @include xs-block {
//             width: 90vw;
//         }
//     }

//     .video:hover .video__button-shape,
//     .video__button:focus .video__button-shape {
//         fill: #ff0000;
//         fill-opacity: 1;
//     }
// }

var VideoLoader = function () {
    function VideoLoader() {
        _classCallCheck(this, VideoLoader);

        this.videos = document.querySelectorAll('.video');
    }

    _createClass(VideoLoader, [{
        key: 'init',
        value: function init() {
            if (this.videos.length) this.findVideos();
        }
    }, {
        key: 'findVideos',
        value: function findVideos() {
            for (var i = 0; i < this.videos.length; i++) {
                this.setupVideo(this.videos[i]);
            }
        }
    }, {
        key: 'setupVideo',
        value: function setupVideo(video) {
            var _this7 = this;

            var link = video.querySelector('.video__link');
            var media = video.querySelector('.video__media');
            var button = video.querySelector('.video__button');
            var id = this.parseMediaURL(media);

            video.addEventListener('click', function () {
                var iframe = _this7.createIframe(id);

                link.remove();
                button.remove();
                video.appendChild(iframe);
            });

            link.removeAttribute('href');
            video.classList.add('video--enabled');
        }
    }, {
        key: 'parseMediaURL',
        value: function parseMediaURL(media) {
            var regexp = /https:\/\/i\.ytimg\.com\/vi\/([a-zA-Z0-9_-]+)\/maxresdefault\.jpg/i;
            var url = media.src;
            var match = url.match(regexp);

            return match[1];
        }
    }, {
        key: 'createIframe',
        value: function createIframe(id) {
            var iframe = document.createElement('iframe');

            iframe.setAttribute('allowfullscreen', '');
            iframe.setAttribute('allow', 'autoplay');
            iframe.setAttribute('src', this.generateURL(id));
            iframe.classList.add('video__media');

            return iframe;
        }
    }, {
        key: 'generateURL',
        value: function generateURL(id) {
            var query = '?rel=0&showinfo=0&autoplay=1';

            return 'https://www.youtube.com/embed/' + id + query;
        }
    }]);

    return VideoLoader;
}();

var AddForm = function () {
    function AddForm(args) {
        _classCallCheck(this, AddForm);

        this.selector = args.selector;
        this.forms = document.querySelectorAll('.' + this.selector);

        this._onSubmit = this._onSubmit.bind(this);
    }

    _createClass(AddForm, [{
        key: 'init',
        value: function init() {
            for (var i = 0; i < this.forms.length; i++) {
                this._setProps(this.forms[i]);
            }
            this._removeEventListener();
            this._addEventListener();
        }
    }, {
        key: '_addEventListener',
        value: function _addEventListener() {
            document.addEventListener('submit', this._onSubmit);
        }
    }, {
        key: '_removeEventListener',
        value: function _removeEventListener() {
            document.removeEventListener('submit', this._onSubmit);
        }

        //Обработчик события на Submit

    }, {
        key: '_onSubmit',
        value: function _onSubmit(e) {
            var target = e.target;

            if (target.classList.contains(this.selector)) {
                this._setValue(target);
            }
            e.preventDefault();
            e.stopPropagation();
        }
    }, {
        key: '_setValue',
        value: function _setValue(element) {
            var input = element.querySelector('input');
            var value = input.value;

            if (value) {
                this._addProp(element, value);
                input.value = '';
            }
        }
    }, {
        key: '_setProps',
        value: function _setProps(element) {
            var checked = element.previousElementSibling.getAttribute('data-checked');
            if (checked != null) {
                var arrayProps = checked.split(', ');
                for (var t = 0; t < arrayProps.length; t++) {
                    this._addProp(element, arrayProps[t]);
                }
            }
        }
    }, {
        key: '_addProp',
        value: function _addProp(element, value) {
            var props = element.previousElementSibling;
            var checked = props.getAttribute('data-checked');
            var child = props.childNodes;
            var arrayProps = void 0;

            if (checked == null) {
                arrayProps = [];
            } else {
                arrayProps = props.getAttribute('data-checked').split(', ');
            }

            if (child.length === arrayProps.length) {
                var index = arrayProps.indexOf(value);

                if (index == -1) {
                    arrayProps.push(value);
                }

                props.setAttribute('data-checked', arrayProps.join(', '));
                props.appendChild(this._createProp(value, props));
            } else {
                var _arrayProps = [];
                var _index = _arrayProps.indexOf(value);

                if (_index == -1) {
                    _arrayProps.push(value);
                }

                props.setAttribute('data-checked', _arrayProps.join(', '));
                props.appendChild(this._createProp(value, props));
            }
        }
    }, {
        key: '_deleteProp',
        value: function _deleteProp(element, value) {
            var arrayProps = element.getAttribute('data-checked').split(', ');
            var index = arrayProps.indexOf(value);
            if (index != -1) {
                arrayProps.splice(index, 1);
            }
            element.setAttribute('data-checked', arrayProps.join(', '));
        }
    }, {
        key: '_createProp',
        value: function _createProp(value, element) {
            var _this8 = this;

            var div = document.createElement('div');
            var span = document.createElement('span');
            var btnReset = document.createElement('button');
            var closeIcon = '<span><svg class="icon icon-close--bold "><use xlink:href="files/sprite.svg#close--bold"></use></svg></span>';

            btnReset.classList.add('btn-reset');

            btnReset.innerHTML = closeIcon;

            btnReset.addEventListener('click', function () {
                _this8._deleteProp(element, value);
                element.removeChild(btnReset.parentElement);
            });

            span.innerText = value;

            div.classList.add('add-form__prop');
            div.setAttribute('data-copy-remove', '');
            div.appendChild(span);
            div.appendChild(btnReset);

            return div;
        }
    }]);

    return AddForm;
}();

/**
 * Base.Components.Button.js
 *
 * @author Anton Ustinoff <a.a.ustinoff@gmail.com>
 */

Base.define('Components.Button');

Base.Components.Button = function () {
    function _init() {
        _initPushUp();
        _initExpanded();
        _initHoverAnimate();
        _initFloating();
        _initStatusAnimate();
        _initToggle();
        _initSelect();
        _initGoTop();
        _initGoTo();
    }

    function _initPushUp() {
        $document.on('click', '[data-push]', function () {
            var _this9 = this;

            var messageSuccess = $(this).attr('data-push-message-success');
            var messageError = $(this).attr('data-push-message-error');
            var delay = $(this).attr('data-push-delay') || 300;
            var timeOut = $(this).attr('data-push-timeout') || 1500;
            var status = void 0;

            setTimeout(function () {
                status = $(_this9).attr('data-push-status') || 'success';
            }, 100);

            setTimeout(function () {
                if (status === 'error') {
                    pushUp({
                        text: messageError,
                        status: status,
                        timeOut: timeOut
                    });
                } else {
                    pushUp({
                        text: messageSuccess,
                        status: status,
                        timeOut: timeOut
                    });
                }
            }, delay);
        });
    }
    //btn expanded
    function _initExpanded() {
        Base.Utils.toggleClassAtBlockClickOutside('.js-btn-expanded', 'is-active');
    }

    //btn animate on hover
    function _initHoverAnimate() {
        $document.on('mouseenter', '.js-btn-animate', function (e) {
            var parentOffset = $(this).offset(),
                relX = e.pageX - parentOffset.left,
                relY = e.pageY - parentOffset.top;
            $(this).find('.button-animate__hover').css({
                top: relY,
                left: relX
            });
        }).on('mouseout', '.js-btn-animate', function (e) {
            var parentOffset = $(this).offset(),
                relX = e.pageX - parentOffset.left,
                relY = e.pageY - parentOffset.top;
            $(this).find('.button-animate__hover').css({
                top: relY,
                left: relX
            });
        });
    }

    //floating btn animatin
    function _initFloating() {
        var $btn = $document.find('.js-btn-floating');
        var run = true;

        if (!$btn.find('.btn-floating__list').length) {
            $btn.find('.btn-floating__link').css('pointer-events', 'auto');
        }

        //Обработчик добавляет классы затем отписыватеся от события
        var hendler = function hendler() {
            var _this10 = this;

            $(this).removeClass('fa-enter-active').addClass('fa-leave-active');
            $btn.off('transitionend webkitTransitionEnd oTransitionEnd', hendler);
            setTimeout(function () {
                $(_this10).removeClass('fa-leave-active');
            }, 1000);
        };

        //Анимация закрытия
        function _removeAnimation(el) {
            el.on('transitionend webkitTransitionEnd oTransitionEnd', hendler);
            setTimeout(function () {
                el.removeClass('fa-leave-active');
            }, 1000);
        }

        if ($(window).width() > 768) {
            if (!run) {
                return;
            }

            $document.on('mouseenter', '.js-btn-floating', function () {
                run = false;
                $(this).addClass('fa-enter-active');
            }).on('mouseleave', '.js-btn-floating', hendler);
        } else {
            $document.on('click', '.js-btn-floating', function () {
                if ($(this).find('.btn-floating__list').length) {
                    $(this).addClass('fa-enter-active').css('z-index', 1000);
                    $overlay.addClass('is-visible').addClass('overlay--btn-floating');
                } else {
                    var btnId = $(this).find('.btn-floating__link').not('.md-hide');
                    btnId.trigger('click');
                }
            });

            $document.on('click', '.js-btn-floating .btn-floating__link', function (e) {
                $btn.removeClass('fa-enter-active').removeAttr('style');
                _removeAnimation($(this));
                $overlay.removeClass('is-visible overlay--btn-floating');
                e.stopPropagation();
            });

            //Клик в не кнопки скрывает оверлей и кнопки
            $document.on('click touchstart', '.overlay--btn-floating', function (e) {
                $btn.removeClass('fa-enter-active').addClass('fa-leave-active');
                setTimeout(function () {
                    $overlay.removeClass('is-visible').removeClass('overlay--btn-floating');
                }, 100);

                setTimeout(function () {
                    $btn.removeClass('fa-leave-active');
                }, 1500);
            });
        }

        //Если ссылка открывает модалку, то по открытию модалки скрывает кнопки
        $('.modal').on('show.bs.modal', function () {
            $btn.removeClass('fa-enter-active').addClass('fa-leave-active');
            $overlay.removeAttr('style');
            setTimeout(function () {
                $btn.removeClass('fa-leave-active');
            }, 1500);
        });
    }

    //btn status animate
    function _initStatusAnimate() {
        var run = true;
        $document.on('click', '.btn-animate', function (e) {
            var _this11 = this;

            if (run) {
                run = false;
                $(this).addClass('is-animate is-ready');

                setTimeout(function () {
                    $(_this11).removeClass('is-animate is-ready');
                    run = true;
                }, 2500);
                setTimeout(function () {
                    $(_this11).addClass('is-ready');
                }, 5000);
            }

            e.preventDefault();
        });
    }

    function _initToggle() {
        var $btnToggle = $document.find('.js-view-toggle');

        $btnToggle.on('click', function (e) {
            var $target = $(e.target);

            if ($target.is($(this).children().last())) {
                $(this).addClass('is-active');
            } else {
                $(this).removeClass('is-active');
            }
        });
    }

    function _initSelect() {
        var $btn = $(document).find('.js-btn-select');
        var ACTIVE_CLASS = 'is-active';
        $btn.on('click', function (e) {
            if ($(this).find('.btn-select__list').length) {
                if ($(this).hasClass(ACTIVE_CLASS)) {
                    $(this).removeClass(ACTIVE_CLASS);
                } else {
                    $(this).addClass(ACTIVE_CLASS);
                }
            }
        });
    }

    //btn scroll to top
    function _initGoTop() {
        $('.js-go-top').on('click', function (e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 800);
        });
    }

    //btn scroll to element
    function _initGoTo() {
        //Click event to scroll to section whith id like href
        $('.js-goto').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var elementClick = $(this).attr('href');
            var destination = $(elementClick).offset().top;
            var wrapScroll = $('.wrapper__inner').scrollTop();
            var offset = void 0;

            if ($(window).width() > 480) {
                offset = 90;
            } else {
                offset = 60;
            }

            $('html, body').animate({
                scrollTop: destination - offset + 'px'
            });
        });
    }

    return {
        init: _init,
        initPushUp: _initPushUp,
        initExpanded: _initExpanded,
        initHoverAnimate: _initHoverAnimate,
        initFloating: _initFloating,
        initStatusAnimate: _initStatusAnimate,
        initToggle: _initToggle,
        initSelect: _initSelect,
        initGoTop: _initGoTop,
        initGoTo: _initGoTo
    };
}();

/**
 * Base.Components.Input.js
 *
 * @author Anton Ustinoff <a.a.ustinoff@gmail.com>
 */

Base.define('Components.Input');

Base.Components.Input = function () {
    function _init() {
        _initMask();
        _addEventListener();
    }

    //Masked inputmask https://github.com/RobinHerbots/Inputmask
    function _initMask() {
        if ($('.js-phone-mask').length) {
            $('.js-phone-mask').inputmask({
                mask: '+7 (999) 999-99-99'
            });
        }
        if ($('.js-time-mask').length) {
            $('.js-time-mask').inputmask({
                mask: '99:99'
            });
        }
        if ($('.js-code-mask').length) {
            $('.js-code-mask').inputmask({
                mask: '9 9 9 9'
            });
        }
        if ($('.js-born-mask').length) {
            $('.js-born-mask').inputmask({
                mask: '99.99.9999'
            });
        }
        if ($('.js-confirm-mask').length) {
            $('.js-confirm-mask').inputmask({
                mask: '9999'
            });
        }
        // if ($('.js-email-mask').length) {
        //     $('.js-email-mask').inputmask({
        //         mask:
        //             '*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]',
        //         greedy: false,
        //         onBeforePaste: function(pastedValue, opts) {
        //             pastedValue = pastedValue.toLowerCase();
        //             return pastedValue.replace('mailto:', '');
        //         },
        //         definitions: {
        //             '*': {
        //                 validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~-]",
        //                 cardinality: 1,
        //                 casing: 'lower'
        //             }
        //         }
        //     });
        // }
    }

    function _addEventListener() {
        var $bbInput = $(document).find('.js-bb-input');
        if ($bbInput.length) {
            $bbInput.each(function () {
                var $parent = $(this).parent('.bb-input, .bb-text');

                $(this).on('focus', function () {
                    $parent.addClass('is-focus');
                }).on('blur', function () {
                    if ($(this).val() === '') {
                        $parent.removeClass('is-focus');
                    }
                });

                if ($(this).val() !== '') {
                    $parent.addClass('is-focus');
                } else {
                    $parent.removeClass('is-focus');
                }
            });
        }

        $('.js-input--copy').on('click', function () {
            var input = $(this).parent().find('input');
            input.select();
            document.execCommand('Copy');
        });

        $('.js-copy-text').on('click', function () {
            var input = $(this).parent().find('.user-share__link');
            input.text();
            document.execCommand('Copy');
        });

        //Click input select value
        $('.js-input-focus--copy').on('focus', function () {
            $(this).select();
        });

        //Show Password
        $('.js-bb-input-password--show').on('click', function () {
            $(this).css('display', 'none');
            $(this).next().css('display', 'block');
            $(this).parent().find('input[type="password"]').attr('type', 'text');
        });

        //Hide Password
        $('.js-bb-input-password--hide').on('click', function () {
            $(this).css('display', 'none');
            $(this).prev().css('display', 'block');
            $(this).parent().find('input[type="text"]').attr('type', 'password');
        });

        $(document).on('click', '.js-bb-input-tip', function () {
            if ($(this).hasClass('no-close')) {
                return;
            }
            $(this).parent().removeClass('is-info is-error is-invalid').end().hide();
        });
    }

    return {
        init: _init
    };
}();

/**
 * Base.Components.Popup.js
 *
 * @author Anton Ustinoff <a.a.ustinoff@gmail.com>
 */

Base.define('Components.Popup');

Base.Components.Popup = function () {
    function _init() {
        _fancyBox();
        _whoIs();
        _changeFormTitle();
        _reinit();
    }

    //Modal FancyBox 3 https://fancyapps.com/fancybox/3/
    function _fancyBox() {
        var $fancyImagePopup = $document.find('[data-fancybox="images"]');
        if ($fancyImagePopup.length) {
            $fancyImagePopup.fancybox({
                baseClass: 'fancybox-container--image',
                toolbar: true,
                smallBtn: true,
                closeClickOutside: true,
                mobile: {
                    clickContent: 'close',
                    clickSlide: 'close'
                }
            });
        }
    }

    //Form Who Is?
    function _whoIs() {
        $('.js-whois').on('click', function () {
            var whois = $(this).data('whois');
            var form = $('#auth-form').find('.form');
            if (whois === 'master') {
                form.addClass('is-master');
            } else if (whois === 'studio') {
                form.addClass('is-studio');
            } else {
                form.addClass('is-client');
            }
        });
    }

    //Dunamicly change form title
    function _changeFormTitle() {
        var $formTitle = $document.find('.js-form-title');
        $('.js-form-title').on('click', function () {
            console.log('---', 'CLICK');
            var text = $(this).data('title');

            $formTitle.not($(this)).removeClass('is-active');

            $(this).addClass('is-active').closest('.form').find('.form__btn').text(text);
        });
    }

    function _reinit() {
        $document.on('show.bs.modal', '.modal', function (e) {
            Base.Components.Select.initSelectColor();
            Base.Components.Popup.changeFormTitle();
        });
    }

    return {
        init: _init,
        fancyBox: _fancyBox,
        whoIs: _whoIs,
        changeFormTitle: _changeFormTitle,
        reinit: _reinit
    };
}();

/**
 * Base.Components.Select.js
 *
 * @author Anton Ustinoff <a.a.ustinoff@gmail.com>
 * Custom Select https://select2.org/
 */

Base.define('Components.Select');

Base.Components.Select = function () {
    function _init() {
        $('.js-select').select2({
            language: 'ru'
        });

        $('.js-select--multiple').select2({
            language: 'ru',
            tags: true
        });

        $('.js-select.bb-select--metro').select2({
            language: 'ru',
            templateResult: addUserPic
        });

        $('.js-select.no-search').select2({
            language: 'ru',
            minimumResultsForSearch: -1
        });

        //Добавляем иконку метро в селект
        function addUserPic(opt) {
            if (!opt.id) {
                return opt.text;
            }
            var optimage = $(opt.element).data('image');
            if (!optimage) {
                return opt.text;
            } else {
                var $opt = $('<span class="metro-icon metro-icon--' + optimage + '">' + $(opt.element).text() + '</span>');
                return $opt;
            }
        }

        _initServices();
        _initNative();
        _initMultiple();
        _initColor();
        _initIcon();
        _initBorn();
        _initShowYear();
        _initHideYear();
        _initPhoneCode();
        _initMobile();
        _initAddEventListener();
        _initInputCustom();
        _initInputCustomControl();
        _initSelectColor();
    }

    function _initServices() {
        var $selectServices = $('.js-select--services');

        $selectServices.each(function () {
            var $parent = $(this).closest('.bb-input--select');
            $(this).select2({
                language: 'ru',
                dropdownParent: $parent,
                templateSelection: timeAndPrice,
                templateResult: timeAndPrice
            });
        });

        //Select Add Price Time & Price
        function timeAndPrice(opt) {
            var originalTime = $(opt.element).data('time');
            var originalPrice = $(opt.element).data('price');

            return $('<div class=custom-select__results>' + '<span>' + opt.text + '</span>' + '<span>' + originalTime + '</span>' + '<span>' + originalPrice + '</span>' + '</div>');
        }
    }

    function _initNative() {
        var $selectNative = $document.find('.js-select-native');
        if ($selectNative.length) {
            if ($(window).width() > 480) {
                $selectNative.each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        var $parent = $(this).closest('.bb-input--select, .profile-editor__select');
                        $(this).select2({
                            language: 'ru',
                            minimumResultsForSearch: -1,
                            dropdownParent: $parent
                        });
                    }
                });
            } else {
                $selectNative.each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }

                    var _this = $(this);
                    var $parent = _this.closest('.bb-input');

                    var $title = $parent.find('.bb-input__title');
                    var titleText = $title.text();

                    var placeholder = _this.data('placeholder');
                    var $firstOption = _this.find('option:first-child');
                    var $newOption = $('<option>').attr({
                        disabled: 'disabled',
                        selected: 'selected'
                    });
                    var type = $parent.data('type');

                    var text = void 0;
                    if (titleText !== '' || titleText !== 'undefined') {
                        text = titleText;
                    } else if (placeholder !== '' || placeholder !== 'undefined') {
                        text = placeholder;
                    } else {
                        return;
                    }

                    if ($parent.hasClass('bb-input--transform')) {
                        if ($firstOption.is(':empty')) {
                            if (type === 'selected') {
                                $firstOption.remove();
                                $parent.addClass('is-focus');
                            } else {
                                $firstOption.remove();

                                _this.removeAttr('data-placeholder').val(text);

                                _addResetBtn(_this);
                            }
                            //firstOption not empty
                        } else {
                            if (type === 'selected') {
                                $parent.addClass('is-focus');
                            } else {
                                $newOption.prependTo(_this);

                                _addResetBtn(_this);
                            }
                        }
                    } else {
                        if ($firstOption.is(':empty')) {
                            $firstOption.val(placeholder).text(placeholder).attr({
                                selected: 'selected',
                                disabled: 'disabled'
                            });
                            _this.addClass('has-placeholder').removeAttr('data-placeholder').val(placeholder);
                        }

                        _addResetBtn(_this);
                    }

                    $(this).on('change', function () {
                        if ($(this).hasClass('has-placeholder')) {
                            $(this).removeClass('has-placeholder');
                        }

                        var $firstOption = _this.find('option:first-child');
                        if ($(this).val() !== '') {
                            $parent.addClass('is-focus');

                            if ($firstOption.is(':empty')) {
                                $firstOption.remove();
                            }
                        } else {
                            $parent.removeClass('is-focus');
                        }
                    });

                    $(this).wrap('<label class="bb-select">');
                });
            }
        }
    }

    function _initMultiple() {
        var $selectMultiple = $document.find('.js-select--multiple');

        if ($selectMultiple.length) {
            if ($(window).width() > 480) {
                $selectMultiple.each(function () {
                    var _this = $(this);
                    var $parent = _this.closest('.bb-input');
                    _this.select2({
                        language: 'ru',
                        tags: true,
                        minimumResultsForSearch: 2,
                        dropdownParent: $parent
                    });
                });
            } else {
                $selectMultiple.each(function () {
                    var _this = $(this);

                    var $parent = _this.closest('.bb-input');
                    var $select2Input = $parent.find('input');
                    var placeholder = $select2Input.attr('placeholder');
                    _this.children().addClass('needsclick');
                    _this.select2({
                        language: 'ru',
                        tags: true,
                        minimumResultsForSearch: 2,
                        dropdownParent: $parent
                    });

                    if ($parent.hasClass('bb-input--transform')) {
                        $select2Input.attr('placeholder', '');
                    }

                    // откат тайтла
                    $(this).on('select2:open', function () {
                        if ($(this).hasClass('has-placeholder')) {
                            $(this).removeClass('has-placeholder');
                            $('.select2-search__field').attr('readonly', true);
                        }

                        if ($(this).val() !== '') {
                            $parent.addClass('is-focus');
                            $select2Input.attr('placeholder', placeholder);
                        } else {
                            $parent.removeClass('is-focus');
                        }
                    }).on('select2:close', function () {
                        if ($(this).val() == '') {
                            $parent.removeClass('is-focus');
                            $select2Input.removeAttr('placeholder').blur();
                        } else {
                            $parent.addClass('is-focus');
                        }
                    });
                });
            }
        }
    }

    function _initAddEventListener() {
        $document.on('focus', '.select2-search__field', function (e) {
            e.stopPropagation();
        });

        var $icon = $document.find('.js-icon-toggle');
        $icon.each(function () {
            var _this = $(this);
            var $select = _this.find('.js-select-native');
            var $optionSelected = _this.find('.js-select-native :selected').data('icon');
            var $icon = _this.find('.icon');
            $icon.each(function () {
                var $dataInIcon = $(this).data('icon');
                if ($dataInIcon != $optionSelected) {
                    $(this).hide();
                }
            });

            $select.on('change', function () {
                var $selectChange = _this.find('.js-select-native :selected').data('icon');
                $icon.each(function () {
                    var $dataInIcon = $(this).data('icon');
                    if ($dataInIcon != $selectChange) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            });
        });
    }

    function _initIcon() {
        var $iconSelect = $document.find('.js-select--icon');

        $iconSelect.each(function () {
            var $parent = $(this).closest('.bb-input--select');

            $(this).select2({
                language: 'ru',
                templateSelection: iformat,
                templateResult: iformat,
                dropdownParent: $parent,
                minimumResultsForSearch: -1
            });
        });

        //Icon fontawesome inside select
        function iformat(icon) {
            var originalOption = icon.element;
            return $('<span><i class="select2__icon' + ' ' + $(originalOption).data('icon') + '"></i> ' + icon.text + '</span>');
        }
    }

    function _initColor() {
        var $colorSelect = $document.find('.js-select--color');
        if ($colorSelect.length) {
            $colorSelect.each(function () {
                colorCustom(this);
            });
        }

        function colorNative(elem) {
            var _this = $(elem);
            var $parent = _this.closest('.bb-input');

            var $title = $parent.find('.bb-input__title');
            var titleText = $title.text();

            var placeholder = _this.data('placeholder');
            var $firstOption = _this.find('option:first-child');
            var $newOption = $('<option>').attr({
                disabled: 'disabled',
                selected: 'selected'
            });
            var type = $parent.data('type');

            var text = void 0;
            if (titleText !== '' || typeof titleText !== 'undefined') {
                text = titleText;
            } else if (placeholder !== '' || typeof placeholder !== 'undefined') {
                text = placeholder;
            } else {
                return;
            }

            if ($parent.hasClass('bb-input--transform')) {
                if ($firstOption.is(':empty')) {
                    if (type === 'selected') {
                        $firstOption.remove();
                        $parent.addClass('is-focus');
                    } else {
                        $firstOption.remove();

                        _this.removeAttr('data-placeholder').val(text);

                        _addResetBtn(_this);
                    }
                    //firstOption not empty
                } else {
                    if (type === 'selected') {
                        $parent.addClass('is-focus');
                    } else {
                        $newOption.prependTo(_this);

                        _addResetBtn(_this);
                    }
                }
            } else {
                if ($firstOption.is(':empty')) {
                    $firstOption.val(placeholder).text(placeholder).attr({
                        selected: 'selected',
                        disabled: 'disabled'
                    });
                    _this.addClass('has-placeholder').removeAttr('data-placeholder').val(placeholder);
                }
            }

            $(elem).on('change', function () {
                if ($(elem).hasClass('has-placeholder')) {
                    $(elem).removeClass('has-placeholder');
                }

                var $firstOption = _this.find('option:first-child');
                if ($(elem).val() !== '') {
                    $parent.addClass('is-focus');

                    if ($firstOption.is(':empty')) {
                        $firstOption.remove();
                    }
                } else {
                    $parent.removeClass('is-focus');
                }
            });

            $(elem).wrap('<label class="bb-select">');
        }

        function colorCustom(elem) {
            var $parent = $(elem).closest('.select-color');
            if ($(elem).hasClass('search-enabled')) {
                $(elem).select2({
                    language: 'ru',
                    templateSelection: iBall,
                    templateResult: iBall,
                    dropdownParent: $parent
                });
            } else {
                $(elem).select2({
                    language: 'ru',
                    minimumResultsForSearch: -1,
                    templateSelection: iBall,
                    templateResult: iBall,
                    dropdownParent: $parent
                });
            }
        }

        function iBall(color) {
            var $parent = $(color.element).closest('.select-color');
            var $originalOption = color.element;
            var colorBall = $($originalOption).data('color');

            if (typeof colorBall != 'undefined') {
                if (color.text.length) {
                    $parent.removeClass('select-color--palette');

                    return $('<div class=select-color__item> <span class="select-color__marker" style="background-color: ' + colorBall + '"></span><p> ' + color.text + ' </p></div>');
                } else {
                    $parent.addClass('select-color--palette');

                    return $('<div class=select-color__item> <span class="select-color__ball" style="background-color: ' + colorBall + ' "> </span> </div>');
                }
            } else {
                return $('<div class=select-color__item> ' + color.text + ' </div>');
            }
        }
    }

    function _initBorn() {
        var $bornSelect = $document.find('.js-select-born');

        if ($bornSelect.length) {
            if ($(window).width() > 480) {
                $bornSelect.each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            language: 'ru',
                            minimumResultsForSearch: -1,
                            allowClear: true
                        });
                    }
                });
            } else {
                $bornSelect.each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                    var $parent = $(this).closest('.bb-input--born');
                    var $select = $(this).closest('.bb-input-born__select');
                    var placeholder = $(this).data('placeholder');
                    var $firstOption = $(this).find('option:first-child');

                    if ($parent.hasClass('bb-input--transform')) {
                        $parent.find('.bb-input__title').detach();

                        $parent.find('.bb-input-born').addClass('bb-input-born--transform');
                    }

                    $firstOption.text(placeholder).val(placeholder).attr('disabled', 'disabled');

                    $(this).removeAttr('data-placeholder');

                    $(this).wrap('<label class="bb-select">');

                    $(this).on('change', function () {
                        if ($(this).val() !== '') {
                            $select.addClass('is-focus');
                        } else {
                            $select.removeClass('is-focus');
                        }
                    });

                    // function checkFocus() {
                    //     if ($(this).val() !== '') {
                    //         $select.addClass('is-focus');
                    //     } else {
                    //         $select.removeClass('is-focus');
                    //     }
                    // }
                    // checkFocus();

                    _addResetBtn($(this));
                });
            }
        }
    }

    function _initShowYear() {
        $document.on('click', '.js-set-year', function () {
            $(this).hide();
            $(this).prev().show();
        });
    }

    function _initHideYear() {
        var $yearSelect = $('.js-select-born--clear');

        $yearSelect.on('select2:unselecting', function () {
            $(this).on('select2:opening', function (e) {
                e.preventDefault();
            });
        }).on('select2:unselect', function () {
            var _this12 = this;

            setTimeout(function () {
                $(_this12).off('select2:opening');
            }, 100);
        }).on('change', function () {
            if ($(this).val() == '' && $(this).attr('data-born') === 'year') {
                $('.js-set-year').show();
                $('.js-set-year').prev().hide();
            }
        });
    }

    function _addResetBtn(el) {
        var renderIcon = true;
        var $select = el;
        var $parent = $select.closest('.bb-input');
        var resetBtn = '<button class="bb-select__reset js-select--reset">\n                            <span>\n                                <svg viewBox="0 0 47.971 47.971">\n                                <path d="M28.228,23.986L47.092,5.122c1.172-1.171,1.172-3.071,0-4.242c-1.172-1.172-3.07-1.172-4.242,0L23.986,19.744L5.121,0.88\n                                    c-1.172-1.172-3.07-1.172-4.242,0c-1.172,1.171-1.172,3.071,0,4.242l18.865,18.864L0.879,42.85c-1.172,1.171-1.172,3.071,0,4.242\n                                    C1.465,47.677,2.233,47.97,3,47.97s1.535-0.293,2.121-0.879l18.865-18.864L42.85,47.091c0.586,0.586,1.354,0.879,2.121,0.879\n                                    s1.535-0.293,2.121-0.879c1.172-1.171,1.172-3.071,0-4.242L28.228,23.986z"/>\n                                </svg>\n                            </span>\n                        </button>';

        var $newOption = $('<option>').attr({
            disabled: 'disabled',
            selected: 'selected'
        });

        $select.on('change', function () {
            var $parent = $(this).parent('.bb-select, .bb-input-born__select');

            if (renderIcon) {
                if ($(window).width() > 480) {
                    $(this).parent().find('.select2-selection__clear').text('').append(resetBtn);
                } else {
                    $parent.append(resetBtn);
                }
                renderIcon = false;
            }
        });

        $document.on('click', '.js-select--reset', function (e) {
            var $parent = void 0,
                $select = void 0;

            if ($(this).siblings('.js-select-born').length) {
                $select = $(this).siblings('.js-select-born');
                $parent = $(this).closest('.bb-input-born__select');
            } else {
                $select = $(this).siblings('.js-select-native');
                $parent = $(this).closest('.bb-input');

                if ($parent.hasClass('bb-input--transform')) {
                    $newOption.prependTo($select);
                }
            }

            $select.val($parent.find('option:first-child').val()).blur();

            $parent.removeClass('is-focus');

            $(this).remove();

            if ($parent.hasClass('bb-input-born__select--year')) {
                $parent.next('.bb-input-born__btn').show();
                $parent.hide();
            }

            renderIcon = true;

            e.stopPropagation();
            e.preventDefault();
        });
    }

    function _initPhoneCode() {
        //Change select results to option value
        function selectCodeSelection(opt) {
            var optVal = $(opt.element).val();

            return $('<span class=custom-select__results>' + optVal + '</span>');
        }

        //Add city name to option
        function selectCodeResult(opt) {
            var country = $(opt.element).data('country'),
                optVal = $(opt.element).val();

            return $('<div class=custom-select__results>' + '<span>' + country + '</span>' + '<span>' + optVal + '</span>' + '</div>');
        }

        var $phoneCodeBox = $document.find('.js-input-phone-code');

        if ($phoneCodeBox.length) {
            $phoneCodeBox.each(function () {
                var $select = $(this).find('.select-value');
                var $parent = $(this).parent();
                var $input = $(this).find('.bb-input__input');

                if ($(window).width() >= 768) {
                    $select.select2({
                        language: 'ru',
                        templateResult: selectCodeResult,
                        templateSelection: selectCodeSelection,
                        dropdownParent: $(this)
                    }).on('select2:select', function () {
                        $(this).parent().parent().find('input').focus();
                    });
                } else {
                    $parent.addClass('bb-select').append('<div class="bb-input--select-value"></div>');

                    var optionSelect = $parent.find('option');
                    var selectValue = $parent.find('.bb-input--select-value');

                    selectValue.text(optionSelect.eq(0).val());

                    $select.change(function () {
                        var counter = $(this)[0].selectedIndex;
                        selectValue.text(optionSelect.eq(counter).val());

                        $(this).parent().parent().find('input').focus();
                    });
                }

                $input.inputmask({
                    mask: '(999) 999-99-99'
                });

                $input.on('focus', addFocus).on('blur', removeFocus);
                $select.on('select2:open', addFocus).on('select2:close', removeFocus);

                function addFocus() {
                    $(this).closest('.js-input-phone-code').addClass('is-focus');
                }

                function removeFocus() {
                    if ($(this).val() == '') {
                        $(this).closest('.js-input-phone-code').removeClass('is-focus');
                    }
                }
            });
        }
    }

    function _initMobile() {
        var $select = $document.find('.js-move-select');

        $select.each(function () {
            var $inputSearch = $(this).find('.move-select__field');
            var $resultItem = $(this).find('.move-select__result');
            var $item = $(this).find('.move-select__result');
            var $btnClose = $(this).find('.js-move-select--close');

            $(this).on('click', function () {
                $(this).addClass('is-active');
                $inputSearch.blur();
                $('html, body').animate({
                    scrollTop: 0
                });
            });

            $item.on('click', function (e) {
                var $name = $(this).find('.user__name').text().trim();

                var $service = $(this).find('.item-info__title span').text().trim().split(' ').join(' + ');

                $inputSearch.val($name || $service);

                $(this).closest('.js-move-select').removeClass('is-active').closest('.bb-input--transform').addClass('is-focus');

                // e.stopPropagation();
                e.preventDefault();
            });

            $btnClose.on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                $(this).closest('.js-move-select').removeClass('is-active');
                $inputSearch.blur();
            });

            $(document).on('click', '.move-select__result', function () {
                $resultItem.removeClass('is-selected');
                $(this).addClass('is-selected');
            });
        });
    }

    function _initInputCustom() {
        var inputCustom = document.querySelectorAll('.js-bb-input-custom');

        if (inputCustom.length) {
            var _loop2 = function _loop2(i) {
                var inputValue = inputCustom[i].querySelector('.bb-input-custom__value');

                inputCustom[i].onclick = function (e) {
                    e.preventDefault();
                };

                inputValue.onclick = function (event) {
                    var e = event.target;
                    event.preventDefault();

                    if (!e.closest('.bb-input-custom').classList.contains('is-open')) {
                        for (var _i = 0; _i < inputCustom.length; _i++) {
                            inputCustom[_i].classList.remove('is-open');
                        }
                        inputCustom[i].classList.add('is-open');
                    } else {
                        inputCustom[i].classList.remove('is-open');
                    }
                };

                document.onclick = function (event) {
                    var target = event.target;

                    if (target.querySelectorAll('.js-bb-input-custom').length || !target.closest('.bb-input-custom__value')) {
                        for (var _i2 = 0; _i2 < inputCustom.length; _i2++) {
                            inputCustom[_i2].classList.remove('is-open');
                        }
                    }
                };
            };

            for (var i = 0; i < inputCustom.length; i++) {
                _loop2(i);
            }
        }
    }

    function _initInputCustomControl() {
        var inputCustom = document.querySelectorAll('.js-bb-input-custom');

        if (inputCustom.length) {
            var _loop3 = function _loop3(i) {
                var minus = inputCustom[i].querySelector('.bb-input-custom__minus');
                var plus = inputCustom[i].querySelector('.bb-input-custom__plus');
                var input = inputCustom[i].querySelector('.bb-input-custom__input');
                var item = inputCustom[i].querySelectorAll('.bb-input-custom__item');

                var _loop4 = function _loop4(_i3) {
                    var offer = item[_i3].querySelector('.bb-input-custom__offer');

                    if (offer != null) {
                        item[_i3].onclick = function () {
                            input.value = offer.innerHTML;
                        };
                    }
                };

                for (var _i3 = 0; _i3 < item.length; _i3++) {
                    _loop4(_i3);
                }

                if (minus != null && plus != null) {
                    minus.onclick = function (e) {
                        var target = e.target;

                        target.closest('.bb-input-custom').classList.remove('is-open');

                        if (input.value > 1) {
                            input.value--;
                        } else {
                            input.value = 1;
                        }

                        e.preventDefault();
                        e.stopPropagation();
                    };
                    plus.onclick = function (e) {
                        var target = e.target;

                        target.closest('.bb-input-custom').classList.remove('is-open');
                        input.value++;

                        e.preventDefault();
                        e.stopPropagation();
                    };
                }
            };

            for (var i = 0; i < inputCustom.length; i++) {
                _loop3(i);
            }
        }
    }

    function _initSelectColor() {
        var inputCustom = document.querySelectorAll('.js-bb-input-custom');
        if (inputCustom.length) {
            var _loop5 = function _loop5(i) {
                var value = inputCustom[i].querySelector('.bb-input-custom__color');
                var title = inputCustom[i].querySelector('.bb-input-custom__title');

                if (value != null && title != null) {
                    var _item = inputCustom[i].querySelectorAll('.bb-input-custom__item');

                    var _loop6 = function _loop6(_i4) {
                        var color = _item[_i4].querySelector('.bb-input-custom__color');
                        var titlePick = _item[_i4].querySelector('.bb-input-custom__title').innerHTML;
                        var data = color.getAttribute('data-color-input');

                        _item[_i4].onclick = function () {
                            title.innerHTML = titlePick;
                            value.style.backgroundColor = data;
                        };
                    };

                    for (var _i4 = 0; _i4 < _item.length; _i4++) {
                        _loop6(_i4);
                    }
                }
            };

            for (var i = 0; i < inputCustom.length; i++) {
                _loop5(i);
            }
        }
    }

    return {
        init: _init,
        initServices: _initServices,
        initNative: _initNative,
        initMultiple: _initMultiple,
        initColor: _initColor,
        initIcon: _initIcon,
        initBorn: _initBorn,
        initShowYear: _initShowYear,
        initHideYear: _initHideYear,
        initPhoneCode: _initPhoneCode,
        initMobile: _initMobile,
        initAddEventListener: _initAddEventListener,
        initInputCustom: _initInputCustom,
        initInputCustomControl: _initInputCustomControl,
        initSelectColor: _initSelectColor
    };
}();

/**
 * Base.Components.Slider.js
 *
 * @author Anton Ustinoff <a.a.ustinoff@gmail.com>
 */

Base.define('Components.Slider');

Base.Components.Slider = function () {
    function _init() {
        _initSlick();
        _initSwiper();
        _initPreviewSlider();
        _initTriumphSlider();
        _initCatalogItemSlider();
        _reinitAfterOpenPopup();
    }

    function _initSlick() {
        var $slider = $('.js-bb-slider');
        if ($slider.length) {
            $slider.each(function () {
                var $slids = $(this).find('.bb-slider__slides');
                var $slide = $(this).find('.bb-slider__slide');
                var $prevArrow = $(this).find('.bb-slider__arrow--prev');
                var $nextArrow = $(this).find('.bb-slider__arrow--next');

                if ($slide.length) {
                    $slids.not('.slick-initialized').slick({
                        prevArrow: $prevArrow,
                        nextArrow: $nextArrow,
                        autoplay: true,
                        autoplaySpeed: 2000,
                        speed: 1000,
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        infinite: true,
                        arrows: true,
                        dots: false,

                        responsive: [{
                            breakpoint: 481,
                            settings: {
                                slidesToShow: 1,
                                dots: true,
                                arrows: false
                            }
                        }]
                    });
                }
            });
        }
    }

    function _initSwiper() {
        var slider = '.js-swiper-slider';
        var $slider = $(slider);

        if ($slider.length) {
            $slider.each(function () {
                var slides = $(this).data('slider-slides');
                var offset = $(this).data('slider-offset');
                var freeMode = $(this).data('slider-free-mode');
                var mouseWheel = $(this).data('slider-mouse-wheel');
                var ratio = $(this).data('slider-ratio');
                var center = $(this).data('slider-center');

                var settings = {
                    pagination: {
                        el: $(this).find('.swiper-pagination'),
                        clickable: true
                    },

                    navigation: {
                        nextEl: $(this).find('.swiper-button-next'),
                        prevEl: $(this).find('.swiper-button-prev')
                    },

                    centerInsufficientSlides: center ? true : false,
                    slidesPerView: slides ? slides : 1,
                    spaceBetween: offset ? offset : false,
                    freeMode: freeMode ? true : false,
                    mousewheel: mouseWheel ? true : false,
                    lazy: true,

                    breakpoints: {
                        // 320: {
                        //     slidesPerView: slides ? (slides / ratio) * 3 : 1,
                        // },
                        480: {
                            slidesPerView: slides ? slides / ratio * 2 : 1
                        },
                        768: {
                            slidesPerView: slides ? ratio / 2.66 : 1
                        }
                        // 1024: {
                        //     slidesPerView: slides ? (slides / ratio) * 2 : 1,
                        // },
                    }
                };

                // $(this).swiper(settings);

                new Swiper(this, settings);
            });
        }
    }

    function _initPreviewSlider() {
        var slider = '.js-swiper-slider--preview';
        var $slider = $(slider);

        if ($slider.length && $(window).width() > 480) {
            var settings = {
                touchRatio: 0.5,
                touchAngle: 30,
                slidesPerView: 4.75,
                spaceBetween: 2,
                freeMode: true,
                mousewheel: true,
                lazy: true,
                breakpoints: {
                    320: {
                        slidesPerView: 1.5
                    },
                    480: {
                        slidesPerView: 2.5
                    },
                    1024: {
                        slidesPerView: 4.5
                    }
                }
            };

            new Swiper(slider, settings);
        }
    }

    function _initTriumphSlider() {
        var $slider = $('.js-bb-slider--triumph');

        if ($slider.length) {
            $slider.each(function () {
                var $slides = $(this).find('.bb-slider__slides');
                var $slide = $(this).find('.bb-slider__slide');
                var $parent = $(this).closest('.triumph');
                var $btnNext = $parent.find('.js-bb-slider-btn--next');

                if ($slide.length > 1) {
                    $slides.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: false,
                        dots: true,
                        touchMove: false,
                        infinite: false
                    });
                }

                $(this).on('afterChange', function (event, slick, currentSlide, nextSlide) {
                    if (currentSlide + 1 === slick.slideCount) {
                        $btnNext.text('Готово').removeClass('js-bb-slider-btn--next').attr('data-dismiss', 'modal');
                    } else {
                        $btnNext.on('click', function () {
                            $slides.slick('slickNext');
                        });
                    }
                });

                $btnNext.on('click', function () {
                    $slides.slick('slickNext');
                });

                //Disable change slide on click dots
                $slider.find('.slick-dots li button').on('click', function (e) {
                    e.stopPropagation();
                });
            });
        }
    }

    function _initCatalogItemSlider() {
        var $catalogItemSlider = $document.find('.js-catalog-item-slider');

        if ($catalogItemSlider.length) {
            $catalogItemSlider.each(function () {
                var _this = $(this);
                var $slides = $(this).find('.bb-slider__slides');
                var $slide = $(this).find('.bb-slider__slide');
                var $sliderDots = $(this).find('.bb-slider__dots');
                $sliderDots.hide();

                _this.on('init', function (event, slick) {
                    $sliderDots.prepend("<span class='bb-slider__pager bb-slider__pager--now'>1</span>" + '/');
                    $sliderDots.append("<span class='bb-slider__pager bb-slider__pager--total'>" + slick.slideCount + '</span>');
                }).on('afterChange', function (event, slick, currentSlide, nextSlide) {
                    var i = (currentSlide ? currentSlide : 0) + 1;
                    _this.find('.bb-slider__pager--now').html(i);
                });

                if ($slide.length > 1) {
                    $sliderDots.show();

                    $slides.not('.slick-initialized').slick({
                        lazyLoad: 'ondemand',
                        speed: 400,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: true,
                        infinite: false,
                        dots: false,

                        responsive: [{
                            breakpoint: 481,
                            settings: {
                                arrows: false
                            }
                        }]
                    });
                }
            });

            if ($(window).width() > 480) {
                $('.js-catalog-item').find('.bb-slider__slides').on('click', function (e) {
                    if ($(this).hasClass('slick-initialized')) {
                        e.stopPropagation();
                        e.preventDefault();
                    }
                });
            }
        }
    }

    //Reinit slider after popup open
    function _reinitAfterOpenPopup() {
        $('.modal').on('shown.bs.modal', function () {
            var $slider = $(this).find('.bb-slider__slides').filter('.slick-initialized');
            if ($slider.length) {
                $slider[0].slick.setPosition();
                setTimeout(function () {
                    $slider.addClass('is-visible');
                }, 50);
            }
        });
    }

    return {
        init: _init,
        initSlick: _initSlick,
        initSwiper: _initSwiper,
        initPreviewSlider: _initPreviewSlider,
        initTriumphSlider: _initTriumphSlider,
        initCatalogItemSlider: _initCatalogItemSlider,
        reinitAfterOpenPopup: _reinitAfterOpenPopup
    };
}();

/* html example box color
    <div data-color="#a5dff8"></div>
*/

var BoxColor = function () {
    function BoxColor() {
        _classCallCheck(this, BoxColor);

        this.element = $('[data-color]');
        this.color;
        this.init();
    }

    _createClass(BoxColor, [{
        key: 'init',
        value: function init() {
            this.element.each(function () {
                var _this = $(this);
                var color = $(this).attr('data-color');
                var colorItem = $(this).find('[data-color-item]');

                //Создаем блок с цветом категории
                var $colorBlock = $('<span>').addClass('color-line').css('background-color', color);

                if (colorItem.length) {
                    if (!colorItem.find('.color-line').length) {
                        $colorBlock.prependTo(colorItem);
                    }
                } else {
                    if (!colorItem.find('.color-line').length) {
                        $colorBlock.prependTo(_this);
                    }
                }
            });
        }
    }]);

    return BoxColor;
}();

var ParallaxBlock = function () {
    function ParallaxBlock(args) {
        _classCallCheck(this, ParallaxBlock);

        this.selector = $(args.selector);
        this.transform = args.transform;
    }

    _createClass(ParallaxBlock, [{
        key: 'init',
        value: function init() {
            if (typeof this.selector !== 'undefined') {
                this.parallax();
            }
        }
    }, {
        key: 'parallax',
        value: function parallax() {
            var _this13 = this;

            this.selector.each(function (index, elem) {
                // let i = index;
                var _this = $(elem);
                var speed = $(elem).data('parallax-speed') || 15;

                $(document).on('scroll', function (e) {
                    var scroll = $(e.target).scrollTop();
                    var cords = scroll / speed;
                    if (_this13.transform) {
                        var transform = _this.css('transform');
                        var matrix = transform.split('(')[1].split(')')[0].split(',').map(parseFloat);
                    }
                    _this.css('transform', 'matrix(' + matrix[0] + ', ' + matrix[1] + ', ' + matrix[2] + ', ' + matrix[3] + ', ' + matrix[4] + ', -' + cords + ')');

                    // if (i & 1) {
                    //     _this.css('transform', `translateY(${cords})`);
                    // } else {
                    //     _this.css('transform', `translateY(-${cords})`);
                    // }
                });
            });
        }
    }]);

    return ParallaxBlock;
}();

/* html example
<div class="bb-dropdown bb-dropdown--small bb-dropdown--transform bb-dropdown--hover js-bb-dropdown" data-dropdown-position="right">
	<div class="bb-dropdown__list">
	</div>
</div>
*/

var Dropdown = function () {
    var $html = $('html');
    var $overlay = $('.js-overlay');

    var dropdown = {};
    var $dropdown = $(document).find('.js-bb-dropdown');
    var $btnDropdownClose = $('<button class="btn-icon btn-icon--close js-bb-dropdown--close"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="icon icon-close" viewBox="0 0 512 512"><path d="M505.943,6.058c-8.077-8.077-21.172-8.077-29.249,0L6.058,476.693c-8.077,8.077-8.077,21.172,0,29.249\n        C10.096,509.982,15.39,512,20.683,512c5.293,0,10.586-2.019,14.625-6.059L505.943,35.306\n        C514.019,27.23,514.019,14.135,505.943,6.058z"/>\n            <path d="M505.942,476.694L35.306,6.059c-8.076-8.077-21.172-8.077-29.248,0c-8.077,8.076-8.077,21.171,0,29.248l470.636,470.636\n        c4.038,4.039,9.332,6.058,14.625,6.058c5.293,0,10.587-2.019,14.624-6.057C514.018,497.866,514.018,484.771,505.942,476.694z"/></svg></button>');
    var $btnFloating = $(document).find('.js-btn-floating');
    var ACTIVE_CLASS = 'is-active';
    var VISIBLE_CLASS = 'is-visible';
    var DROPDOWN_OWERLAY_CLASS = 'overlay--dropdown';
    var _this = void 0,
        $list = void 0;
    var open = false;

    var styleDesctop = {
        display: 'block',
        position: 'fixed',
        filter: 'none',
        opacity: 1,
        'pointer-events': 'auto',
        visibility: 'visible'
    };

    var styleTransformMenu = {
        display: 'block',
        position: 'fixed',
        top: 'auto',
        bottom: 10,
        left: 10,
        right: 10,
        zIndex: 9999
    };

    var styleTransformInfo = {
        display: 'block',
        position: 'fixed',
        bottom: 0,
        left: 0,
        right: 0,
        zIndex: 9999
    };

    dropdown.init = function () {
        if ($dropdown.length) {
            if ($(window).width() <= 768) {
                $dropdown.removeClass('bb-dropdown--hover');
            }
            dropdown.events();
            dropdown.transform();
            dropdown.render();
        }
    };

    //Добавляем и отрисовываем мобильный дропдаун
    dropdown.render = function () {
        if ($(window).width() <= 768) {
            var _$dropdown = $(document).find('.js-bb-dropdown.bb-dropdown--transform');
            _$dropdown.each(function () {
                var $btnClose = $('<button class="bb-dropdown__close js-bb-dropdown--close">Закрыть</button>');
                var $dropdownOverlay = $('<div class="bb-dropdown__overlay">');

                var $dropdownList = $(this).find('.bb-dropdown__list');

                $btnClose.appendTo($dropdownList);
                $dropdownOverlay.insertAfter($dropdownList);
                $dropdownList.find('.info-block__icon').remove();
            });
        }
    };

    //Эвенты кликов по основным элементам
    dropdown.events = function () {
        $(document).on('click', '.js-bb-dropdown', function (e) {
            _this = $(this);
            $list = $(this).find('.bb-dropdown__list');

            //Активируем оверлей, если клик не поклассу bb-dropdown--another
            if (!$(this).hasClass('bb-dropdown--another')) {
                $overlay.addClass(DROPDOWN_OWERLAY_CLASS);
            }

            if ($(window).width() > 768) {
                dropdown._toggle($(this));
                // dropdown._setStyle($(this));
            } else {
                if (!$(this).hasClass('bb-dropdown--another')) {
                    $('html').addClass('no-touch-events');
                    $btnFloating.fadeOut();
                    open = true;

                    //Анимация выпадающего списка
                    $list.insertAfter('.wrapper');
                    setTimeout(function () {
                        $list.addClass(VISIBLE_CLASS);
                    }, 200);

                    if ($(this).hasClass('bb-dropdown--transform')) {
                        $list.css(styleTransformMenu).addClass('_transform');
                    } else {
                        $btnDropdownClose.prependTo($list);
                        $list.css(styleTransformInfo).addClass('_transform_info');
                    }
                } else {
                    dropdown._toggle($(this));
                }
            }

            e.stopPropagation();
        });

        //Toggle fixed class from body
        $(document).on('click', '.js-bb-dropdown.request-info', function (e) {
            if ($(window).width() <= 480) {
                if (!open) {
                    $('html').addClass('is-fixed');
                    open = true;
                } else {
                    $('html').removeClass('is-fixed');
                    open = false;
                }
            }
        });

        //Скрываем дропдаун по клику в не блока
        $(document).on('click', function (e) {
            if ($(e.target).closest('.js-bb-dropdown').length) return;
            $dropdown.removeClass(ACTIVE_CLASS);
            open = false;
        });

        //По клику на оверлей скрываем дропдаун
        $(document).on('click', '.overlay--dropdown', function () {
            open = false;
            $dropdown.removeClass(ACTIVE_CLASS);
            dropdown._close();
            $btnFloating.fadeIn();
        });

        var link = '.bb-dropdown__list .info-block__item, .bb-dropdown__list .info-block__link';

        $(document).on('click', link, function (e) {
            $dropdown.removeClass(ACTIVE_CLASS);
            dropdown._close();
            $btnFloating.fadeIn();
        });

        //По клику на кнопку закрыть скрываем дропдаун
        $(document).on('click', '.js-bb-dropdown--close', function (e) {
            $btnFloating.fadeIn();
            dropdown._close();
            e.stopPropagation();
        });
    };

    dropdown.transform = function () {
        if ($(window).width() <= 480) {
            $(document).find('.js-bb-dropdown').each(function () {
                if ($(this).is('[data-dropdown-transform]')) {
                    var _$list = $(this).find('.bb-dropdown__list');
                    $(this).removeClass('bb-dropdown--another').addClass('bb-dropdown--transform');
                    _$list.find('ul').addClass('info-block__list');
                    _$list.find('li').addClass('info-block__item');
                    _$list.find('a').addClass('info-block__link');
                }
            });
        }
    };

    // dropdown._setStyle = function(el) {
    //     let dPosition = el.data('position');
    //     let wWidth = $(window).width();
    //     let wHeight = $(window).height();
    //     let position = el.offset();

    //     let top = position.top + el.outerHeight(true);
    //     let left;

    //     left = wWidth - $list.width() * 2;

    //     if (position.left > wWidth / 2) {
    //         styleDesctop.left = left + 'px';
    //     } else {
    //         styleDesctop.left = position.left;
    //     }

    //     styleDesctop.top = top + 'px';
    //     styleDesctop.right = 'auto';

    //     console.log('--- styleDesctop', styleDesctop);

    //     if (!open) {
    //         $list.css(styleDesctop);
    //         console.log('---', 1);
    //     } else {
    //         $list.removeAttr('style');
    //         console.log('---', 2);
    //     }
    // };

    dropdown._toggle = function (el) {
        if (!el.hasClass(ACTIVE_CLASS)) {
            el.addClass(ACTIVE_CLASS);
            $btnFloating.fadeIn();
            open = true;
        } else {
            $dropdown.removeClass(ACTIVE_CLASS);
            el.removeClass(ACTIVE_CLASS);
            open = false;

            if (el.hasClass('bb-dropdown--transform') && $(window).width() <= 480) {
                $btnFloating.fadeOut();
            }
        }
    };

    dropdown._close = function () {
        setTimeout(function () {
            $list.removeClass(VISIBLE_CLASS);
            _this.removeClass(ACTIVE_CLASS);
            $btnFloating.fadeIn();
            $('html').removeClass('no-touch-events');
        }, 100);

        setTimeout(function () {
            $list.removeAttr('style').removeClass('_transform').removeClass('_transform_info').appendTo(_this);
            $overlay.removeClass('overlay--dropdown');
            $('html').removeClass('no-touch-events');
        }, 300);
    };

    return dropdown;
}();

var Menu = function () {
    var menu = {};
    var $html = $('html');
    var $wrapper = $('.wrapper');
    var $header = $('.header');
    var $overlay = $('.js-overlay');
    var $menu = $('.js-menu');
    var hamburger = '.js-main-nav-btn';
    var $hamburger = $(hamburger);
    var $hamburgerCrm = $('.js-hamburger');
    var hamburgerCrm = '.js-hamburger';
    var $menuItem = $('.js-menu').find('.menu__item');
    var $menuOvelay = $('.js-menu-overlay');
    var $menuItemDropdown = $(document).find('.js-menu-item-dropdown');
    var $btnFloat = $(document).find('.js-btn-floating');
    var ACTIVE_CLASS = 'is-active';
    var dropdownActiveClass = 'menu-dropdown--open';

    menu.init = function () {
        this.events();
        this.menuItemDropdownEvent();

        //Для безпроблемной трансформации меню при перевороте телефона
        $(window).on('resize', function (e) {
            menu._close(e);
            $wrapper.removeClass('menu-open');
        });
    };

    menu.events = function () {
        $(document).on('click', hamburger, function (e) {
            if ($(this).hasClass('on')) {
                menu._close();
            } else {
                menu._open();
            }
            e.stopPropagation();
            e.preventDefault();
        });

        $(document).on('click', hamburgerCrm, function (e) {
            if ($(this).hasClass('on')) {
                menu._close(e);
            } else {
                menu._open();
            }
        });

        $menuItem.on('click', function (e) {
            var $target = $(e.target);
            //Если нет вложенного меню
            if (!$(this).hasClass('js-menu-item-dropdown')) {
                $menuItem.removeClass(ACTIVE_CLASS).removeClass(dropdownActiveClass);
                $(this).addClass(ACTIVE_CLASS);
                // e.stopPropagation();
            } else {
                //Если есть вложенное меню
                //Если таргет ссылка но не кнока Отменить
                if ($target.hasClass('menu-dropdown__link') && !$target.hasClass('menu-dropdown__link--abort')) {
                    var $parent = $target.parent('.menu-dropdown__item');

                    //Переключаем активный класс у главной ссылки меню и открываем вложенное меню
                    $menuItem.removeClass(activeClass);
                    $(this).addClass(dropdownActiveClass).addClass(activeClass);

                    //Переключаем активный класс у вложенных li
                    $('.menu-dropdown__item').removeClass(activeClass);
                    $parent.addClass(activeClass);

                    if ($(window).width() > 480) {
                        //Сдвигаем контент
                        $wrapper.addClass('menu-open');
                    } else {
                        menu._close(e);
                    }

                    // e.stopPropagation();
                } else if (
                    //Если таргет кнока Отменить просто закрываем меню
                    $target.hasClass('menu-dropdown__link') && $target.hasClass('menu-dropdown__link--abort')) {
                    menu._close(e);
                    // e.stopPropagation();
                } else {
                    //Если таргет НЕ ссылка, проверяем на наличие активного класса у дропдауна
                    if ($(this).hasClass(dropdownActiveClass)) {
                        $(this).removeClass(dropdownActiveClass);
                        $wrapper.removeClass('menu-open');
                    } else {
                        $menuItemDropdown.removeClass(dropdownActiveClass);
                        $(this).addClass(dropdownActiveClass);

                        if ($(window).width() > 480) {
                            $wrapper.addClass('menu-open');
                        } else {
                            $btnFloat.fadeOut();
                            $menuOvelay.addClass('is-visible');
                        }
                    }
                }
            }

            if (!$(this).hasClass(ACTIVE_CLASS)) {
                $menuItem.removeClass(ACTIVE_CLASS);
                $(this).addClass(ACTIVE_CLASS);
            }
        });

        $('.js-mobile-nav--close').on('click', function (e) {
            menu._close(e);
        });

        //Ивент клика по аакодеону внутри меню
        $(document).find('.js-mobile-nav').find('.mobile-nav__item').on('click', function (e) {
            if (!$(this).hasClass('bb-accordeon__item')) {
                menu._close(e);
            }
        }).end().find('.bb-accordeon__content a').on('click', function (e) {
            menu._close(e);
        });

        //Закрваем меню по клюку на оверлэй
        $(document).on('click', '.overlay--menu', function (e) {
            menu._close(e);
            e.stopPropagation();
        });

        //Закрваем меню по клюку на оверлэй
        $(document).on('click', '.js-menu-overlay', function (e) {
            menu._close(e);
            e.stopPropagation();
        });
    };

    menu.menuItemDropdownEvent = function () {
        $(document).on('click', '.js-menu-item-dropdown', function (e) {
            if ($(this).hasClass(dropdownActiveClass)) {
                $menuItemDropdown.removeClass(dropdownActiveClass);
                $(this).addClass(dropdownActiveClass);
            } else {
                $(this).removeClass(dropdownActiveClass);
            }
            e.stopPropagation();
        });

        $(document).on('click', '.js-menu-item-dropdown .menu__link', function (e) {
            e.preventDefault();
        });
    };

    menu._open = function () {
        if (!$(document).find('.jsCrmBlurEventStop')) {
            $(document).find('input').blur();
        }

        if ($(window).width() > 480) {
            $hamburgerCrm.addClass('on');

            if ($wrapper.hasClass('page-cabinet')) {
                $menu.addClass('is-open');
                $(document).find('.header').addClass('is-moving');
                $wrapper.addClass('menu-open');
                $menuItemDropdown.removeClass('is-active');
            } else {
                $wrapper.addClass('mobile-nav--open');
                $overlay.addClass('is-visible').addClass('overlay--menu');
            }
        } else {
            $hamburger.addClass('on');
            $wrapper.addClass('mobile-nav--open');
            $overlay.addClass('is-visible').addClass('overlay--menu');
            $('html').addClass('is-fixed');
        }

        if ($wrapper.hasClass('page-onepage')) {
            $hamburger.addClass('on');
            $wrapper.addClass('mobile-nav--open');
            $overlay.addClass('is-visible').addClass('overlay--menu');
        }
    };

    menu._close = function (e) {
        $hamburger.removeClass('on');
        $hamburgerCrm.removeClass('on');
        $menu.removeClass('is-open');
        $menuItemDropdown.removeClass(dropdownActiveClass);
        $(document).find('.header').removeClass('is-moving').removeClass('is-open');
        $btnFloat.fadeIn();
        $wrapper.removeClass('mobile-nav--open');
        $('html').removeClass('is-fixed');

        var target = $(e.target);
        if (target.is('.js-hamburger') || target.is('.js-menu-item-dropdown')) {
            $wrapper.removeClass('menu-open');
        }

        setTimeout(function () {
            $('.overlay--menu').removeClass('is-visible');
        }, 200);

        if ($(window).width() < 480) {
            setTimeout(function () {
                $menuOvelay.removeClass('is-visible');
            }, 100);
        }
    };

    return menu;
}();

$(function () {
    Menu.init();

    //Pages
    Catalog.init();
    Card.init();
});

/**
 * Main
 *
 * @author Anton Ustinoff <a.a.ustinoff@gmail.com>
 */
var Main = {
    init: function init() {}
};

/**
 * Catalog
 *
 * @author Anton Ustinoff <a.a.ustinoff@gmail.com>
 */
var Catalog = {
    init: function init() {
        this.mapToggle();
        // this.btnFilterOpen();
        this.btnShowCatalog();
        this.btnShowMap();
        // this.stickyFilter();
        this.filterCategory();
        this.moveBlocks();
        this.ifPageCatalog();
    },
    //Catalog map Toggle
    mapToggle: function mapToggle() {
        $('.js-catalog--show').on('click', function () {
            $(this).addClass('is-active');
            $('.js-catalog-map--show').removeClass('is-active');
        });
        $('.js-catalog-map--show').on('click', function () {
            $(this).addClass('is-active');
            $('.js-catalog--show').removeClass('is-active');
        });
    },
    //Btn show catalog
    btnShowCatalog: function btnShowCatalog() {
        $('.js-show--list').on('click', function () {
            $('.js-catalog-map').removeAttr('style');
            $('.catalog-content__inner').removeAttr('style');
            $('.js-catalog-map').removeClass('is-checked');
            $(this).parent().removeClass('is-active');
        });
    },
    //Btn show map - hide catalog
    btnShowMap: function btnShowMap() {
        $('.js-show--map').on('click', function () {
            $('.js-catalog-map').css('display', 'block');
            $('.catalog-content__inner').css('display', 'none');
            $('.js-stiky-block').removeAttr('style');
            $('.js-catalog-map').addClass('is-checked');
            $(this).parent().addClass('is-active');
        });
    },
    //filter category
    filterCategory: function filterCategory() {
        if ($(window).width() > 768) {
            $('.js-category').find('.category__link').on('click', function () {
                $(this).parent().addClass('is-selected');
                $('.js-category').addClass('is-checked').find('.category__link').not(this).parent().css('display', 'none');
            });
            $('.js-category--reset').on('click', function () {
                $(this).parent().removeClass('is-selected').closest('.js-category').removeClass('is-checked');
                $(this).closest('.js-category').find('.category__item').removeAttr('style');
            });
        } else {
            $('.js-category').find('.category__link').on('click', function () {
                if ($(this).parent().hasClass('is-selected')) {
                    $(this).parent().removeClass('is-selected');
                    $('.js-category').removeClass('is-checked').find('.category__link').parent().removeAttr('style');
                } else {
                    $(this).parent().addClass('is-selected');
                    $('.js-category').addClass('is-checked').find('.category__link').not(this).parent().css('display', 'none');
                }
            });
        }
    },
    //Move block in media screen
    moveBlocks: function moveBlocks() {
        if ($(window).width() <= 480) {
            $('.js-view-toggle').insertBefore('.catalog__inner');
        }
    },
    //If page catalog filter transform accordeon
    ifPageCatalog: function ifPageCatalog() {
        if ($wrapper.hasClass('page-catalog')) {
            $header.addClass('header--fixed');
            // $main.css('padding-top', $('.header').outerHeight());
            if ($(window).width() <= 768) {
                $('.catalog-filter__body').addClass('bb-accordeon bb-accordeon--custom bb-accordeon--other js-bb-accordeon');
                $('.js-catalog-filter-item').each(function () {
                    $(this).addClass('bb-accordeon__item').find('.catalog-filter__title').not('.catalog-filter__title_category').addClass('bb-accordeon__title');
                    $(this).find('.catalog-filter__content').addClass('bb-accordeon__content').slideUp();
                });
                $('.js-catalog-filter-item:lt(2)').addClass('is-open').find('.bb-accordeon__content').slideDown();
            }
        }
    }
};

/**
 * Card
 *
 * @author Anton Ustinoff <a.a.ustinoff@gmail.com>
 */
var Card = {
    init: function init() {
        this.slider();
        this.scrollspy();
        this.cardSticky();

        if ($(window).width() <= 768) {
            Card.requestToggle();
            Card.requestBlockMoveItems();
            $(window).resize(Card.requestBlockMoveItems);
        }
    },


    //Card Slider
    slider: function slider() {
        var $cardSlider = $('.js-card-slider');
        if ($cardSlider.length) {
            $cardSlider.each(function () {
                var _this = $(this);
                var $slides = _this.find('.bb-slider__slides');
                var $sliderDots = $(this).find('.bb-slider__dots').hide();
                var settings = {
                    nextArrow: '.bb-slider__arrow--next',
                    prevArrow: '.bb-slider__arrow--prev',
                    speed: 400,
                    infinite: false,
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    arrows: true,
                    dots: false,
                    lazyLoad: 'ondemand',

                    responsive: [{
                        breakpoint: 1201,
                        settings: {
                            slidesToShow: 3
                        }
                    }, {
                        breakpoint: 769,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1
                        }
                    }, {
                        breakpoint: 481,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }]
                };

                if ($(window).width() <= 480) {
                    $sliderDots.show();

                    _this.on('init', function (event, slick) {
                        $sliderDots.prepend("<span class='bb-slider__pager bb-slider__pager--now'>1</span>" + '/');
                        $sliderDots.append("<span class='bb-slider__pager bb-slider__pager--total'>" + slick.slideCount + '</span>');
                    }).on('afterChange', function (event, slick, currentSlide, nextSlide) {
                        var i = (currentSlide ? currentSlide : 0) + 1;
                        _this.find('.bb-slider__pager--now').html(i);
                    });
                }

                $slides.not('.slick-initialized').slick(settings);
            });
        }
    },


    //Card request show / hide
    requestToggle: function requestToggle() {
        var cardInfoRequest = $('.card-info__request');

        $('.js-card-request--show').on('click', function () {
            if (cardInfoRequest.hasClass('is-open')) {
                $('html').removeAttr('style');
            } else {
                cardInfoRequest.addClass('is-open');
                $('html').css('overflow', 'hidden');
            }
            return false;
        });
        $('.js-card-request--hide').on('click', function () {
            if (cardInfoRequest.hasClass('is-open')) {
                cardInfoRequest.removeClass('is-open');
                $('html').removeAttr('style');
            }
        });
    },


    //Move blocks when window width < 768
    requestBlockMoveItems: function requestBlockMoveItems() {
        $('.js-card-title').insertAfter('.card-gallary__wrap');
        $('.js-card-about').insertBefore('.card-adress');
        $('.card-info__inner').insertAfter('.card-adress');

        $('.card-info__request').wrapInner('<div class="card-info__request_inner">');
        $('.card-info__header--mobile').insertBefore('.card-info__request_inner');
        $('.js-card-info-category').prependTo('.card-info__request_inner');
        $('.js-move-card-policy').appendTo('.card-request-form');
    },


    //Card Scrollspy
    scrollspy: function scrollspy() {
        var $scrollingСontainer = $('.js-scrollspy');
        var offset = void 0;

        if ($scrollingСontainer.length) {
            $(window).width() > 480 ? offset = -100 : offset = -60;
            console.log('--- offset', offset);

            setTimeout(function () {
                $scrollingСontainer.scrollspy({ offset: offset });
            }, 1000);
        }
    },
    cardSticky: function cardSticky() {
        if ($('.js-card-fixed').length) {
            var _fixCardUserInfo = function _fixCardUserInfo() {
                $(window).scroll(function () {
                    var scroll = $(this).scrollTop();
                    if (scroll >= stickyBlockOffset && scroll < fixedBlock.outerHeight(true) + fixedBlockOffset - stickyBlock.outerHeight()) {
                        stickyBlock.css({
                            position: 'fixed',
                            top: -1 + 'px',
                            width: 375 + 'px',
                            bottom: 'auto'
                        });
                    } else if (scroll >= stickyBlockOffset && scroll > fixedBlock.outerHeight(true) + fixedBlockOffset - stickyBlock.outerHeight() - 30) {
                        stickyBlock.css({
                            position: 'absolute',
                            top: 'auto',
                            bottom: 0,
                            width: 375 + 'px'
                        });
                    } else {
                        stickyBlock.removeAttr('style');
                    }
                });
            };

            var _cardMenuFixed = function _cardMenuFixed() {
                $(window).on('scroll touchmove', function () {
                    var scroll = $(this).scrollTop();

                    if (scroll >= cardMenuOffset) {
                        cardMenuClone.show();
                        cardMenu.css(cardMenuFixedStyle).addClass('is-sticky');
                    } else {
                        cardMenuClone.hide();
                        cardMenu.removeAttr('style').removeClass('is-sticky');
                    }
                });
            };

            var stickyBlock = $('.js-card-sticky');
            var stickyBlockOffset = stickyBlock.offset().top;
            var fixedBlock = $('.js-card-fixed');
            var fixedBlockOffset = fixedBlock.offset().top;

            var cardContent = $('.js-card-content-fixed');
            var cardMenu = $document.find('.js-card-menu');

            var cardMenuClone = $('<div class="card-menu__clone">').css('height', $('.js-card-menu').outerHeight(true)).insertAfter(cardMenu).hide();

            var cardMenuOffset = cardMenu.offset().top;

            var cardMenuFixedStyle = {
                position: 'fixed',
                top: 0,
                left: 0,
                right: 0,
                zIndex: 99
            };

            if (stickyBlock.length > 0 && fixedBlock.length > 0 && stickyBlock.height() < cardContent.height() && $(window).width() > 768) {
                _fixCardUserInfo();
            }

            if (cardMenu.length) {
                _cardMenuFixed();
            }
        }
    }
};
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1haW4uanMiXSwibmFtZXMiOlsiJHdpbmRvdyIsIiQiLCJ3aW5kb3ciLCIkZG9jdW1lbnQiLCJkb2N1bWVudCIsIiRib2R5IiwiJGh0bWwiLCIkd3JhcHBlciIsIiRvdmVybGF5IiwiJGhlYWRlciIsIiRtYWluIiwiJGNhYmluZXQiLCJCYXNlIiwiaW5pdCIsIkRyb3Bkb3duIiwicmVtb3ZlUHJlbG9hZGVyIiwicmVtb3ZlRWxlbWVudHMiLCJpbml0TGlzdFRvZ2dsZSIsImluaXRDb3B5VGV4dCIsImluaXRUb2dnbGVQaG9uZSIsImluaXRDaXR5U2VsZWN0IiwiaW5pdE1vYmlsZVNlYXJjaCIsImluaXRJbnB1dFNlYXJjaCIsIkZvcm0iLCJGaWx0ZXIiLCJDb21wb25lbnRzIiwiVXRpbHMiLCJvbiIsImUiLCJwcmV2ZW50RGVmYXVsdCIsInNldFRpbWVvdXQiLCJyZW1vdmVDbGFzcyIsIndpZHRoIiwiZmluZCIsInJlbW92ZUF0dHIiLCIkbGlzdCIsIiRjaGVja2JveCIsIiR3b3JrTGlzdCIsImxlbmd0aCIsImF0dHIiLCJoYXNDbGFzcyIsImhpZGUiLCJzaG93IiwicXVlcnlTZWxlY3RvciIsImNiIiwiQ2xpcGJvYXJkIiwiJGlucHV0U2VhcmNoIiwiYnRuQmluZCIsImNsb3Nlc3QiLCJ2YWwiLCJub3QiLCJjc3MiLCJlYWNoIiwiJGlucHV0Qm94IiwiJGlucHV0SWNvbiIsIiRidG5SZXNldCIsIiRidG5TZWFyY2giLCIkaGludCIsIm9mZiIsIiRwYXJlbnQiLCJidG4iLCIkYnRuRGF0YSIsImRhdGEiLCIkaW5wdXRWYWwiLCJ1bmJpbmQiLCJ0ZXh0IiwidXNlclBob25lIiwicGFyZW50IiwicGhvbmUiLCIkY2hhbmdlQ2l0eSIsIiRjaGFuZ2VDaXR5VGl0bGUiLCIkaW5wdXQiLCJzdG9wUHJvcGFnYXRpb24iLCIkc2VhcmNoQnRuIiwib3BlbiIsImFkZENsYXNzIiwiX2FkZEV2ZW50TGlzdGVuZXIiLCJjaGVja1ZhbGlkYXRpb24iLCIkYnRuIiwiJGZvcm1TdWNjZXNzIiwidGFyZ2V0IiwiX3RvZ2dsZSIsIk9QRU5fQ0xBU1MiLCIkY2F0YWxvZ0ZpbHRlciIsImRlZmluZSIsIm5hbWVzcGFjZSIsInBhcnRzIiwic3BsaXQiLCJpIiwic2xpY2UiLCJfaW5pdCIsIl9kZXRlY3RCcm93c2VyVHlwZSIsIl9pbml0TGF6eUxvYWRJbWFnZSIsImFkZEV2ZW50TGlzdGVuZXIiLCJodG1sIiwiaXNPcGVyYSIsIm9wZXJhIiwibmF2aWdhdG9yIiwidXNlckFnZW50IiwiaW5kZXhPZiIsImlzQ2hyb21lIiwiY2hyb21lIiwiaXNFeHBsb3JlciIsImRvY3VtZW50TW9kZSIsImlzRWRnZSIsImlzRmlyZWZveCIsIkluc3RhbGxUcmlnZ2VyIiwiaXNTYWZhcmkiLCJ0ZXN0IiwidmVuZG9yIiwiY2xhc3NMaXN0IiwiYWRkIiwiTVNTdHJlYW0iLCJfdGVzdFNsb3dDb25uZWN0aW9uIiwiY29ubmVjdGlvbiIsIm1vekNvbm5lY3Rpb24iLCJ3ZWJraXRDb25uZWN0aW9uIiwic2xvd1NwZWVkIiwiZWZmZWN0aXZlVHlwZSIsImxvYWRUaW1lIiwicGVyZm9ybWFuY2UiLCJ0aW1pbmciLCJkb21Db250ZW50TG9hZGVkRXZlbnRFbmQiLCJuYXZpZ2F0aW9uU3RhcnQiLCJMYXp5TG9hZCIsImVsZW1lbnRzX3NlbGVjdG9yIiwiX3NsaWRlVXAiLCJzbGlkZVVwIiwiX3NsaWRlVG9nZ2xlIiwiJGNvbnRhaW5lciIsIiRjb250ZW50Iiwic2xpZGVEb3duIiwiX2NvcHlCb3giLCIkY2xvbmUiLCJjbG9uZSIsImluc2VydEFmdGVyIiwiX2FuaW1hdGVXaWR0aCIsImFuaW1hdGUiLCJjb25zb2xlIiwibG9nIiwiX2ZpZWxkRWRpdCIsImZpZWxkRWRpdCIsIiRmaWVsZEVkaXQiLCIkZmllbGRFZGl0SW5wdXQiLCIkZmllbGRFZGl0QnRuIiwiJGZpZWxkRWRpdFRleHQiLCJmaWVsZEVkaXRUZXh0Iiwic2VsZWN0IiwiYmx1ciIsInRyaW0iLCJ2YWx1ZSIsImRlZmF1bHRWYWx1ZSIsImtleXByZXNzIiwiZXZlbnQiLCJrZXlDb2RlIiwiJHRleHQiLCJfdG9nZ2xlQ2xhc3NBdEJsb2NrIiwiYmxvY2siLCJjbCIsIl90b2dnbGVDbGFzc0F0QmxvY2tDbGlja091dHNpZGUiLCJ0b2dnbGVDbGFzcyIsImluaXRMYXp5TG9hZEltYWdlIiwic2xpZGVUb2dnbGUiLCJjb3B5Qm94IiwiYW5pbWF0ZVdpZHRoIiwidG9nZ2xlQ2xhc3NBdEJsb2NrIiwidG9nZ2xlQ2xhc3NBdEJsb2NrQ2xpY2tPdXRzaWRlIiwidGVzdFNsb3dDb25uZWN0aW9uIiwiX2luaXRDaGVja0JveCIsIl9pbml0Q2hlY2tCb3hTZWxlY3RBbGwiLCJfaW5pdFRhYiIsIl9pbml0Q2hhbmdlciIsIl9pbml0Qm94Q29sb3IiLCJfaW5pdFBhcmFsbGF4IiwiX2luaXRBY2NvcmRlb24iLCJfaW5pdFByb2dyZXNzTGluZSIsIl9pbml0Q29weUJveCIsIl9pbml0Vmlld2VyIiwiX2luaXRWaWRlb0xvYWRlciIsIl9pbml0U3RpY2t5QmxvY2siLCJfaW5pdFNlcnZpY2VzQnRuSW5mbyIsIl9pbml0QWRkRm9ybSIsIkJ1dHRvbiIsIklucHV0IiwiUG9wdXAiLCJTZWxlY3QiLCJTbGlkZXIiLCJDaGVja0JveCIsInNlbGVjdG9yIiwiUmFkaW8iLCIkY2hlY2tib3hzIiwicHJvcCIsIiR0YWJzIiwidGFicyIsIiRjaGFuZ2VyIiwiQ0hFQ0tFRF9DTEFTUyIsIiRpdGVtIiwiQm94Q29sb3IiLCJQYXJhbGxheEJsb2NrIiwidHJhbnNmb3JtIiwiZWxlbWVudHMiLCJxdWVyeVNlbGVjdG9yQWxsIiwiZm9yRWFjaCIsImVsZW0iLCJnZXRBdHRyaWJ1dGUiLCJzdHlsZSIsInBhcnNlSW50IiwiQWNjb3JkZW9uIiwiQ29weUJveCIsIlZpZXdlciIsIlZpZGVvTG9hZGVyIiwic3RpY2t5QmxvY2siLCIkc3RpY2t5QmxvY2siLCJzdGlja3lCbG9ja0NvbnRhaW5lciIsInN0aWNreUJsb2NrSW5uZXIiLCJtYXhXaW5kb3dXaWR0aCIsInN0aWNreUJsb2NrVG9wU3Bhc2luZyIsInN0aWNreUJsb2NrQm90dG9tU3Bhc2luZyIsIlN0aWNreVNpZGViYXIiLCJ0b3BTcGFjaW5nIiwiYm90dG9tU3BhY2luZyIsImNvbnRhaW5lclNlbGVjdG9yIiwiaW5uZXJXcmFwcGVyU2VsZWN0b3IiLCJpbmZvQnRuIiwiaXRlbURlc2NyaXB0aW9uIiwiYnRuQ2xvc2UiLCJjYWxsIiwiQWRkRm9ybSIsImluaXRDaGVja0JveCIsImluaXRDaGVja0JveFNlbGVjdEFsbCIsImluaXRUYWIiLCJpbml0Q2hhbmdlciIsImluaXRCb3hDb2xvciIsImluaXRQYXJhbGxheCIsImluaXRQcm9ncmVzc0xpbmUiLCJpbml0QWNjb3JkZW9uIiwiaW5pdENvcHlCb3giLCJpbml0Vmlld2VyIiwiaW5pdFZpZGVvTG9hZGVyIiwiaW5pdFN0aWNreUJsb2NrIiwiaW5pdFNlcnZpY2VzQnRuSW5mbyIsImluaXRBZGRGb3JtIiwiYXJncyIsInRpdGxlIiwiY29udGVudCIsIml0ZW0iLCJydW4iLCJfc2FtZUluaXQiLCJfYWRkSXZlbnRMaXN0ZW5lciIsImluc2VydEludG8iLCJpY29uIiwiYXBwZW5kVG8iLCJtYWluU2NvcGUiLCJjdXN0b21DbGFzcyIsIl9yZW5kZXJJY29uIiwiX2luaXREYXRhQWNjb3JkZW9uIiwiZGVzdHJveSIsIiR0YXJnZXQiLCJhY2NvcmQiLCIkdGhpcyIsInJlbW92ZSIsImNoZWNrU3RhdHVzIiwiY2hlY2tib3giLCJlbGVtZW50VG9nZ2xlIiwiY2hlY2tlZCIsInJlbW92ZUF0dHJpYnV0ZSIsInNldEF0dHJpYnV0ZSIsImNoZWNrIiwibm9DaGVjayIsInRvZ0NoZWNrIiwidG9nTm9DaGVjayIsImxlbiIsInB1c2giLCJjbGljayIsImVsZW1lbnQiLCJlbGVtZW50VG9nZ2xlTmFtZSIsImFsbEVsZW1lbnRzIiwicHJldk9iamVjdCIsImEiLCJvdGhlclJhZGlvIiwiX2dldENsaWNrRWxlbWVudCIsIm90aGVyUmFkaW9Ub2dnbGUiLCJ0cmlnZ2VyIiwiZWxlbWVudENsYXNzIiwibmV3VGFyZ2V0IiwidW5kZWZpbmVkIiwiYm9keSIsImNvbnRhaW5zIiwicGFyZW50Tm9kZSIsInB1c2hVcCIsIm9wdGlvbnMiLCJzdGF0dXMiLCJ0aW1lT3V0IiwiJHB1c2hDb250YWluZXIiLCIkcHVzaEljb25TdWNjZXNzIiwiJHB1c2hJY29uRXJyb3IiLCJwcmVwZW5kVG8iLCJfcG9zaFBvcyIsInJlcXVlc3RBbmltYXRpb25GcmFtZSIsImhlaWdodCIsIm91dGVySGVpZ2h0IiwiRWxlbWVudCIsInByb3RvdHlwZSIsInJlbW92ZUNoaWxkIiwiYnRuSGVybWl0IiwibCIsInIiLCJzdGFydFZpZXdCbG9jayIsImVsZW1zIiwibmV3RWxlbXMiLCJhcnJheSIsInQiLCJjaGlsZE5vZGVzIiwibGFzdEVsZW1lbnRDaGlsZCIsImRpc3BsYXkiLCJldmVudHMiLCJyZW1vdmVFdmVudExpc3RlbmVyIiwic2hvd05laWdoYm9ycyIsInNob3dBbGwiLCJwYXJlbnRFbGVtZW50IiwiX29uQ2xpY2siLCJiaW5kIiwiX2FkZEV2ZW50TGlzdGVuZXJzIiwiX3JlbW92ZUVsZW1lbnQiLCJpbnNlcnRCZWZvcmUiLCJ2aWRlb3MiLCJmaW5kVmlkZW9zIiwic2V0dXBWaWRlbyIsInZpZGVvIiwibGluayIsIm1lZGlhIiwiYnV0dG9uIiwiaWQiLCJwYXJzZU1lZGlhVVJMIiwiaWZyYW1lIiwiY3JlYXRlSWZyYW1lIiwiYXBwZW5kQ2hpbGQiLCJyZWdleHAiLCJ1cmwiLCJzcmMiLCJtYXRjaCIsImNyZWF0ZUVsZW1lbnQiLCJnZW5lcmF0ZVVSTCIsInF1ZXJ5IiwiZm9ybXMiLCJfb25TdWJtaXQiLCJfc2V0UHJvcHMiLCJfcmVtb3ZlRXZlbnRMaXN0ZW5lciIsIl9zZXRWYWx1ZSIsImlucHV0IiwiX2FkZFByb3AiLCJwcmV2aW91c0VsZW1lbnRTaWJsaW5nIiwiYXJyYXlQcm9wcyIsInByb3BzIiwiY2hpbGQiLCJpbmRleCIsImpvaW4iLCJfY3JlYXRlUHJvcCIsInNwbGljZSIsImRpdiIsInNwYW4iLCJidG5SZXNldCIsImNsb3NlSWNvbiIsImlubmVySFRNTCIsIl9kZWxldGVQcm9wIiwiaW5uZXJUZXh0IiwiX2luaXRQdXNoVXAiLCJfaW5pdEV4cGFuZGVkIiwiX2luaXRIb3ZlckFuaW1hdGUiLCJfaW5pdEZsb2F0aW5nIiwiX2luaXRTdGF0dXNBbmltYXRlIiwiX2luaXRUb2dnbGUiLCJfaW5pdFNlbGVjdCIsIl9pbml0R29Ub3AiLCJfaW5pdEdvVG8iLCJtZXNzYWdlU3VjY2VzcyIsIm1lc3NhZ2VFcnJvciIsImRlbGF5IiwicGFyZW50T2Zmc2V0Iiwib2Zmc2V0IiwicmVsWCIsInBhZ2VYIiwibGVmdCIsInJlbFkiLCJwYWdlWSIsInRvcCIsImhlbmRsZXIiLCJfcmVtb3ZlQW5pbWF0aW9uIiwiZWwiLCJidG5JZCIsIiRidG5Ub2dnbGUiLCJpcyIsImNoaWxkcmVuIiwibGFzdCIsIkFDVElWRV9DTEFTUyIsInNjcm9sbFRvcCIsImVsZW1lbnRDbGljayIsImRlc3RpbmF0aW9uIiwid3JhcFNjcm9sbCIsImluaXRQdXNoVXAiLCJpbml0RXhwYW5kZWQiLCJpbml0SG92ZXJBbmltYXRlIiwiaW5pdEZsb2F0aW5nIiwiaW5pdFN0YXR1c0FuaW1hdGUiLCJpbml0VG9nZ2xlIiwiaW5pdFNlbGVjdCIsImluaXRHb1RvcCIsImluaXRHb1RvIiwiX2luaXRNYXNrIiwiaW5wdXRtYXNrIiwibWFzayIsIiRiYklucHV0IiwiZXhlY0NvbW1hbmQiLCJuZXh0IiwicHJldiIsImVuZCIsIl9mYW5jeUJveCIsIl93aG9JcyIsIl9jaGFuZ2VGb3JtVGl0bGUiLCJfcmVpbml0IiwiJGZhbmN5SW1hZ2VQb3B1cCIsImZhbmN5Ym94IiwiYmFzZUNsYXNzIiwidG9vbGJhciIsInNtYWxsQnRuIiwiY2xvc2VDbGlja091dHNpZGUiLCJtb2JpbGUiLCJjbGlja0NvbnRlbnQiLCJjbGlja1NsaWRlIiwid2hvaXMiLCJmb3JtIiwiJGZvcm1UaXRsZSIsImluaXRTZWxlY3RDb2xvciIsImNoYW5nZUZvcm1UaXRsZSIsImZhbmN5Qm94Iiwid2hvSXMiLCJyZWluaXQiLCJzZWxlY3QyIiwibGFuZ3VhZ2UiLCJ0YWdzIiwidGVtcGxhdGVSZXN1bHQiLCJhZGRVc2VyUGljIiwibWluaW11bVJlc3VsdHNGb3JTZWFyY2giLCJvcHQiLCJvcHRpbWFnZSIsIiRvcHQiLCJfaW5pdFNlcnZpY2VzIiwiX2luaXROYXRpdmUiLCJfaW5pdE11bHRpcGxlIiwiX2luaXRDb2xvciIsIl9pbml0SWNvbiIsIl9pbml0Qm9ybiIsIl9pbml0U2hvd1llYXIiLCJfaW5pdEhpZGVZZWFyIiwiX2luaXRQaG9uZUNvZGUiLCJfaW5pdE1vYmlsZSIsIl9pbml0QWRkRXZlbnRMaXN0ZW5lciIsIl9pbml0SW5wdXRDdXN0b20iLCJfaW5pdElucHV0Q3VzdG9tQ29udHJvbCIsIl9pbml0U2VsZWN0Q29sb3IiLCIkc2VsZWN0U2VydmljZXMiLCJkcm9wZG93blBhcmVudCIsInRlbXBsYXRlU2VsZWN0aW9uIiwidGltZUFuZFByaWNlIiwib3JpZ2luYWxUaW1lIiwib3JpZ2luYWxQcmljZSIsIiRzZWxlY3ROYXRpdmUiLCJfdGhpcyIsIiR0aXRsZSIsInRpdGxlVGV4dCIsInBsYWNlaG9sZGVyIiwiJGZpcnN0T3B0aW9uIiwiJG5ld09wdGlvbiIsImRpc2FibGVkIiwic2VsZWN0ZWQiLCJ0eXBlIiwiX2FkZFJlc2V0QnRuIiwid3JhcCIsIiRzZWxlY3RNdWx0aXBsZSIsIiRzZWxlY3QySW5wdXQiLCIkaWNvbiIsIiRzZWxlY3QiLCIkb3B0aW9uU2VsZWN0ZWQiLCIkZGF0YUluSWNvbiIsIiRzZWxlY3RDaGFuZ2UiLCIkaWNvblNlbGVjdCIsImlmb3JtYXQiLCJvcmlnaW5hbE9wdGlvbiIsIiRjb2xvclNlbGVjdCIsImNvbG9yQ3VzdG9tIiwiY29sb3JOYXRpdmUiLCJpQmFsbCIsImNvbG9yIiwiJG9yaWdpbmFsT3B0aW9uIiwiY29sb3JCYWxsIiwiJGJvcm5TZWxlY3QiLCJhbGxvd0NsZWFyIiwiZGV0YWNoIiwiJHllYXJTZWxlY3QiLCJyZW5kZXJJY29uIiwicmVzZXRCdG4iLCJhcHBlbmQiLCJzaWJsaW5ncyIsInNlbGVjdENvZGVTZWxlY3Rpb24iLCJvcHRWYWwiLCJzZWxlY3RDb2RlUmVzdWx0IiwiY291bnRyeSIsIiRwaG9uZUNvZGVCb3giLCJmb2N1cyIsIm9wdGlvblNlbGVjdCIsInNlbGVjdFZhbHVlIiwiZXEiLCJjaGFuZ2UiLCJjb3VudGVyIiwic2VsZWN0ZWRJbmRleCIsImFkZEZvY3VzIiwicmVtb3ZlRm9jdXMiLCIkcmVzdWx0SXRlbSIsIiRidG5DbG9zZSIsIiRuYW1lIiwiJHNlcnZpY2UiLCJpbnB1dEN1c3RvbSIsImlucHV0VmFsdWUiLCJvbmNsaWNrIiwibWludXMiLCJwbHVzIiwib2ZmZXIiLCJ0aXRsZVBpY2siLCJiYWNrZ3JvdW5kQ29sb3IiLCJpbml0U2VydmljZXMiLCJpbml0TmF0aXZlIiwiaW5pdE11bHRpcGxlIiwiaW5pdENvbG9yIiwiaW5pdEljb24iLCJpbml0Qm9ybiIsImluaXRTaG93WWVhciIsImluaXRIaWRlWWVhciIsImluaXRQaG9uZUNvZGUiLCJpbml0TW9iaWxlIiwiaW5pdEFkZEV2ZW50TGlzdGVuZXIiLCJpbml0SW5wdXRDdXN0b20iLCJpbml0SW5wdXRDdXN0b21Db250cm9sIiwiX2luaXRTbGljayIsIl9pbml0U3dpcGVyIiwiX2luaXRQcmV2aWV3U2xpZGVyIiwiX2luaXRUcml1bXBoU2xpZGVyIiwiX2luaXRDYXRhbG9nSXRlbVNsaWRlciIsIl9yZWluaXRBZnRlck9wZW5Qb3B1cCIsIiRzbGlkZXIiLCIkc2xpZHMiLCIkc2xpZGUiLCIkcHJldkFycm93IiwiJG5leHRBcnJvdyIsInNsaWNrIiwicHJldkFycm93IiwibmV4dEFycm93IiwiYXV0b3BsYXkiLCJhdXRvcGxheVNwZWVkIiwic3BlZWQiLCJzbGlkZXNUb1Nob3ciLCJzbGlkZXNUb1Njcm9sbCIsImluZmluaXRlIiwiYXJyb3dzIiwiZG90cyIsInJlc3BvbnNpdmUiLCJicmVha3BvaW50Iiwic2V0dGluZ3MiLCJzbGlkZXIiLCJzbGlkZXMiLCJmcmVlTW9kZSIsIm1vdXNlV2hlZWwiLCJyYXRpbyIsImNlbnRlciIsInBhZ2luYXRpb24iLCJjbGlja2FibGUiLCJuYXZpZ2F0aW9uIiwibmV4dEVsIiwicHJldkVsIiwiY2VudGVySW5zdWZmaWNpZW50U2xpZGVzIiwic2xpZGVzUGVyVmlldyIsInNwYWNlQmV0d2VlbiIsIm1vdXNld2hlZWwiLCJsYXp5IiwiYnJlYWtwb2ludHMiLCJTd2lwZXIiLCJ0b3VjaFJhdGlvIiwidG91Y2hBbmdsZSIsIiRzbGlkZXMiLCIkYnRuTmV4dCIsInRvdWNoTW92ZSIsImN1cnJlbnRTbGlkZSIsIm5leHRTbGlkZSIsInNsaWRlQ291bnQiLCIkY2F0YWxvZ0l0ZW1TbGlkZXIiLCIkc2xpZGVyRG90cyIsInByZXBlbmQiLCJsYXp5TG9hZCIsImZpbHRlciIsInNldFBvc2l0aW9uIiwiaW5pdFNsaWNrIiwiaW5pdFN3aXBlciIsImluaXRQcmV2aWV3U2xpZGVyIiwiaW5pdFRyaXVtcGhTbGlkZXIiLCJpbml0Q2F0YWxvZ0l0ZW1TbGlkZXIiLCJyZWluaXRBZnRlck9wZW5Qb3B1cCIsImNvbG9ySXRlbSIsIiRjb2xvckJsb2NrIiwicGFyYWxsYXgiLCJzY3JvbGwiLCJjb3JkcyIsIm1hdHJpeCIsIm1hcCIsInBhcnNlRmxvYXQiLCJkcm9wZG93biIsIiRkcm9wZG93biIsIiRidG5Ecm9wZG93bkNsb3NlIiwiJGJ0bkZsb2F0aW5nIiwiVklTSUJMRV9DTEFTUyIsIkRST1BET1dOX09XRVJMQVlfQ0xBU1MiLCJzdHlsZURlc2N0b3AiLCJwb3NpdGlvbiIsIm9wYWNpdHkiLCJ2aXNpYmlsaXR5Iiwic3R5bGVUcmFuc2Zvcm1NZW51IiwiYm90dG9tIiwicmlnaHQiLCJ6SW5kZXgiLCJzdHlsZVRyYW5zZm9ybUluZm8iLCJyZW5kZXIiLCIkZHJvcGRvd25PdmVybGF5IiwiJGRyb3Bkb3duTGlzdCIsImZhZGVPdXQiLCJfY2xvc2UiLCJmYWRlSW4iLCJNZW51IiwibWVudSIsIiRtZW51IiwiaGFtYnVyZ2VyIiwiJGhhbWJ1cmdlciIsIiRoYW1idXJnZXJDcm0iLCJoYW1idXJnZXJDcm0iLCIkbWVudUl0ZW0iLCIkbWVudU92ZWxheSIsIiRtZW51SXRlbURyb3Bkb3duIiwiJGJ0bkZsb2F0IiwiZHJvcGRvd25BY3RpdmVDbGFzcyIsIm1lbnVJdGVtRHJvcGRvd25FdmVudCIsIl9vcGVuIiwiYWN0aXZlQ2xhc3MiLCJDYXRhbG9nIiwiQ2FyZCIsIk1haW4iLCJtYXBUb2dnbGUiLCJidG5TaG93Q2F0YWxvZyIsImJ0blNob3dNYXAiLCJmaWx0ZXJDYXRlZ29yeSIsIm1vdmVCbG9ja3MiLCJpZlBhZ2VDYXRhbG9nIiwic2Nyb2xsc3B5IiwiY2FyZFN0aWNreSIsInJlcXVlc3RUb2dnbGUiLCJyZXF1ZXN0QmxvY2tNb3ZlSXRlbXMiLCJyZXNpemUiLCIkY2FyZFNsaWRlciIsImNhcmRJbmZvUmVxdWVzdCIsIndyYXBJbm5lciIsIiRzY3JvbGxpbmfQoW9udGFpbmVyIiwiZml4Q2FyZFVzZXJJbmZvIiwic3RpY2t5QmxvY2tPZmZzZXQiLCJmaXhlZEJsb2NrIiwiZml4ZWRCbG9ja09mZnNldCIsImNhcmRNZW51Rml4ZWQiLCJjYXJkTWVudU9mZnNldCIsImNhcmRNZW51Q2xvbmUiLCJjYXJkTWVudSIsImNhcmRNZW51Rml4ZWRTdHlsZSIsImNhcmRDb250ZW50Il0sIm1hcHBpbmdzIjoiOzs7Ozs7QUFBQTs7Ozs7OztBQU9BO0FBQ0EsSUFBTUEsVUFBVUMsRUFBRUMsTUFBRixDQUFoQjtBQUNBLElBQU1DLFlBQVlGLEVBQUVHLFFBQUYsQ0FBbEI7QUFDQSxJQUFNQyxRQUFRSixFQUFFLE1BQUYsQ0FBZDtBQUNBLElBQU1LLFFBQVFMLEVBQUUsTUFBRixDQUFkO0FBQ0EsSUFBTU0sV0FBV04sRUFBRSxVQUFGLENBQWpCO0FBQ0EsSUFBTU8sV0FBV1AsRUFBRSxVQUFGLENBQWpCO0FBQ0EsSUFBTVEsVUFBVVIsRUFBRSxTQUFGLENBQWhCO0FBQ0EsSUFBTVMsUUFBUVQsRUFBRSxPQUFGLENBQWQ7QUFDQSxJQUFNVSxXQUFXVixFQUFFLFVBQUYsQ0FBakI7O0FB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QUNKO0FBck9RLENBQWIiLCJmaWxlIjoibWFpbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICpcbiAqIENvcHlyaWdodCAoYykgMjAxOSBCZWF1dHlib3ggSW5jLiBBbGwgUmlnaHRzIFJlc2VydmVkLlxuICpcbiAqIEBhdXRob3IgQW50b24gVXN0aW5vZmYgPGEuYS51c3Rpbm9mZkBnbWFpbC5jb20+XG4gKi9cblxuLy9HbG9iYWwgVmFyc1xuY29uc3QgJHdpbmRvdyA9ICQod2luZG93KTtcbmNvbnN0ICRkb2N1bWVudCA9ICQoZG9jdW1lbnQpO1xuY29uc3QgJGJvZHkgPSAkKCdib2R5Jyk7XG5jb25zdCAkaHRtbCA9ICQoJ2h0bWwnKTtcbmNvbnN0ICR3cmFwcGVyID0gJCgnLndyYXBwZXInKTtcbmNvbnN0ICRvdmVybGF5ID0gJCgnLm92ZXJsYXknKTtcbmNvbnN0ICRoZWFkZXIgPSAkKCcuaGVhZGVyJyk7XG5jb25zdCAkbWFpbiA9ICQoJy5tYWluJyk7XG5jb25zdCAkY2FiaW5ldCA9ICQoJy5jYWJpbmV0Jyk7XG5cbi8qKlxuICogQmFzZS5qc1xuICpcbiAqIEBhdXRob3IgQW50b24gVXN0aW5vZmYgPGEuYS51c3Rpbm9mZkBnbWFpbC5jb20+XG4gKi9cblxuJChmdW5jdGlvbigpIHtcbiAgICBCYXNlLmluaXQoKTtcbiAgICBEcm9wZG93bi5pbml0KCk7XG59KTtcblxuY29uc3QgQmFzZSA9IHtcbiAgICBpbml0KCkge1xuICAgICAgICB0aGlzLnJlbW92ZVByZWxvYWRlcigpO1xuICAgICAgICB0aGlzLnJlbW92ZUVsZW1lbnRzKCk7XG4gICAgICAgIHRoaXMuaW5pdExpc3RUb2dnbGUoKTtcbiAgICAgICAgdGhpcy5pbml0Q29weVRleHQoKTtcbiAgICAgICAgdGhpcy5pbml0VG9nZ2xlUGhvbmUoKTtcbiAgICAgICAgdGhpcy5pbml0Q2l0eVNlbGVjdCgpO1xuICAgICAgICB0aGlzLmluaXRNb2JpbGVTZWFyY2goKTtcbiAgICAgICAgdGhpcy5pbml0SW5wdXRTZWFyY2goKTtcbiAgICAgICAgLy8gdGhpcy5pbml0UGFyYWxsYXgoKTtcbiAgICAgICAgLy8gdGhpcy5pbml0Qm94Q29sb3IoKTtcbiAgICAgICAgLy8gdGhpcy5jaGVja0JveFNlbGVjdEFsbCgpO1xuICAgICAgICAvLyB0aGlzLmNhdGFsb2dJdGVtU2xpZGVyKCk7XG4gICAgICAgIC8vIHRoaXMudGFiKCk7XG4gICAgICAgIC8vIHRoaXMuaW5pdENoZWNrQm94KCk7XG4gICAgICAgIC8vIHRoaXMuc2xpZGVyKCk7XG4gICAgICAgIC8vIHRoaXMuc3RpY2t5RmlsdGVyKCk7XG4gICAgICAgIC8vIHRoaXMuc3RpY2t5QmxvY2soKTtcbiAgICAgICAgdGhpcy5Gb3JtLmluaXQoKTtcbiAgICAgICAgdGhpcy5GaWx0ZXIuaW5pdCgpO1xuXG4gICAgICAgIHRoaXMuQ29tcG9uZW50cy5pbml0KCk7XG4gICAgICAgIHRoaXMuVXRpbHMuaW5pdCgpO1xuXG4gICAgICAgIC8vU3RvcCBkcmFnIEltZ1xuICAgICAgICAkKCdpbWcnKS5vbignZHJhZ3N0YXJ0JywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLy9SZW1vdmUgcHJlbG9hZGVyXG4gICAgcmVtb3ZlUHJlbG9hZGVyKCkge1xuICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgICAgICAgICQoJ2JvZHknKS5yZW1vdmVDbGFzcygnbG9hZGluZyBsb2FkaW5nLWFuaW1hdGlvbiBpcy1sb2FkaW5nJyk7XG4gICAgICAgICAgICAkKCcuX2xvYWRpbmcnKS5yZW1vdmVDbGFzcygnX2xvYWRpbmcnKTtcbiAgICAgICAgfSwgMTAwMCk7XG4gICAgfSxcblxuICAgIHJlbW92ZUVsZW1lbnRzKCkge1xuICAgICAgICAvL1NpbXBsZUJhciBodHRwczovL2dpdGh1Yi5jb20vR3JzbXRvL3NpbXBsZWJhclxuICAgICAgICBpZiAoJHdpbmRvdy53aWR0aCgpIDwgNzY4KSB7XG4gICAgICAgICAgICAkZG9jdW1lbnQuZmluZCgnW2RhdGEtc2ltcGxlYmFyXScpLnJlbW92ZUF0dHIoJ2RhdGEtc2ltcGxlYmFyJyk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBpZiAoJCh3aW5kb3cpLndpZHRoKCkgPD0gNDgwKSB7XG4gICAgICAgIC8vICAgICAkZG9jdW1lbnQuZmluZCgnLnhzLWhpZGUnKS5yZW1vdmUoKTtcbiAgICAgICAgLy8gfVxuICAgIH0sXG5cbiAgICBpbml0TGlzdFRvZ2dsZSgpIHtcbiAgICAgICAgbGV0ICRsaXN0ID0gJCgnLmpzLWxpc3QnKTtcbiAgICAgICAgdmFyICRjaGVja2JveCA9ICRsaXN0LmZpbmQoJy5qcy1iYi1jaGVja2JveCcpO1xuICAgICAgICB2YXIgJHdvcmtMaXN0ID0gJGxpc3QuZmluZCgnLmpzLWxpc3QtdG9nZ2xlJyk7XG4gICAgICAgIGlmICgkbGlzdC5sZW5ndGgpIHtcbiAgICAgICAgICAgIGlmICgkbGlzdC5hdHRyKCdkYXRhLXJldmVyc2UnKSA9PT0gJ3RydWUnKSB7XG4gICAgICAgICAgICAgICAgaWYgKCEkY2hlY2tib3guaGFzQ2xhc3MoJ2lzLWNoZWNrZWQnKSkge1xuICAgICAgICAgICAgICAgICAgICAkd29ya0xpc3QuaGlkZSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAkY2hlY2tib3gub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmICgkY2hlY2tib3guaGFzQ2xhc3MoJ2lzLWNoZWNrZWQnKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJHdvcmtMaXN0LnNob3coKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICR3b3JrTGlzdC5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJGNoZWNrYm94Lm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICBpZiAoJGNoZWNrYm94Lmhhc0NsYXNzKCdpcy1jaGVja2VkJykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICR3b3JrTGlzdC5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkd29ya0xpc3Quc2hvdygpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9LFxuXG4gICAgLy9Db3B5IHRleHQgY2xpY2sgbGlua1xuICAgIGluaXRDb3B5VGV4dCgpIHtcbiAgICAgICAgaWYgKGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJy5qcy11c2VyLWxpbmsnKSkge1xuICAgICAgICAgICAgbGV0IGNiID0gbmV3IENsaXBib2FyZCgnLmpzLXVzZXItbGluaycpO1xuXG4gICAgICAgICAgICAvL2lmIGhhcyBpbnB1dCB0aGVuIGNvcHkgaW5wdXQgdmFsdWUgaW4gZGF0YSBhdHRyXG4gICAgICAgICAgICB0aGlzLmluaXRJbnB1dFNlYXJjaCgpO1xuICAgICAgICB9XG4gICAgfSxcblxuICAgIGluaXRJbnB1dFNlYXJjaCgpIHtcbiAgICAgICAgbGV0ICRpbnB1dFNlYXJjaCA9ICRkb2N1bWVudC5maW5kKCcuanMtaW5wdXQnKTtcbiAgICAgICAgaWYgKCRpbnB1dFNlYXJjaC5sZW5ndGgpIHtcbiAgICAgICAgICAgICRpbnB1dFNlYXJjaC5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGxldCAkaW5wdXRCb3ggPSAkKHRoaXMpLmNsb3Nlc3QoJy5qcy1pbnB1dC1ib3gnKTtcbiAgICAgICAgICAgICAgICBsZXQgJGlucHV0SWNvbiA9ICRpbnB1dEJveC5maW5kKCcuYmItaW5wdXRfX2ljb24nKTtcbiAgICAgICAgICAgICAgICBsZXQgJGJ0blJlc2V0ID0gJGlucHV0Qm94LmZpbmQoJy5qcy1pbnB1dC0tY2xlYXInKTtcbiAgICAgICAgICAgICAgICBsZXQgJGJ0blNlYXJjaCA9ICRpbnB1dEJveC5maW5kKCcuYmItaW5wdXRfX2ljb24tc2VhcmNoJyk7XG4gICAgICAgICAgICAgICAgbGV0ICRoaW50ID0gJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAuY2xvc2VzdCgnLmpzLXNlYXJjaCcpXG4gICAgICAgICAgICAgICAgICAgIC5maW5kKCcuc2VhcmNoX19oaW50Jyk7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMpLm9mZigpO1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAub24oJ2tleXVwJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBsZXQgJHBhcmVudCA9ICQodGhpcykuY2xvc2VzdCgnLmpzLWlucHV0LWJsb2NrJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBsZXQgYnRuID0gJHBhcmVudC5maW5kKCcuanMtdXNlci1saW5rJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBsZXQgJGJ0bkRhdGEgPSAkKHRoaXMpLmRhdGEoJ2NsaXBib2FyZC10ZXh0Jyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBsZXQgJGlucHV0VmFsID0gJCh0aGlzKS52YWwoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgYnRuLmF0dHIoJ2RhdGEtY2xpcGJvYXJkLXRleHQnLCAkYnRuRGF0YSArICRpbnB1dFZhbCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICgkaW5wdXRWYWwgIT0gJyAnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGJ0blJlc2V0LnNob3coKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkYnRuU2VhcmNoLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGJ0blJlc2V0LmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkYnRuU2VhcmNoLnNob3coKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAgICAgLm9uKCdibHVyJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoJCh0aGlzKS52YWwoKSA9PSAnJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmICgkaW5wdXRWYWwgIT0gJyAnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRidG5SZXNldC5zaG93KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRidG5TZWFyY2guaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRidG5SZXNldC5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRidG5TZWFyY2guc2hvdygpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgJCgnLmpzLWlucHV0LS1jbGVhcicpLnVuYmluZCgnY2xpY2snKTtcblxuICAgICAgICAgICAgJCgnLmpzLWlucHV0LS1jbGVhcicpLm9uKCdjbGljaycsIGJ0bkJpbmQpO1xuXG4gICAgICAgICAgICBmdW5jdGlvbiBidG5CaW5kKCkge1xuICAgICAgICAgICAgICAgICQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgLmNsb3Nlc3QoJy5qcy1zZWFyY2gnKVxuICAgICAgICAgICAgICAgICAgICAuZmluZCgnLmpzLWlucHV0JylcbiAgICAgICAgICAgICAgICAgICAgLnZhbCgnJyk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAuaGlkZSgpXG4gICAgICAgICAgICAgICAgICAgIC5jbG9zZXN0KCcuanMtc2VhcmNoJylcbiAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy5iYi1pbnB1dF9faWNvbicpXG4gICAgICAgICAgICAgICAgICAgIC5ub3QoJy5qcy1pbnB1dC0tY2xlYXInKVxuICAgICAgICAgICAgICAgICAgICAuc2hvdygpO1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAuY2xvc2VzdCgnLmpzLXNlYXJjaCcpXG4gICAgICAgICAgICAgICAgICAgIC5maW5kKCcuc2VhcmNoX19oaW50JylcbiAgICAgICAgICAgICAgICAgICAgLmNzcygnZGlzcGxheScsICdub25lJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9LFxuXG4gICAgLy9TaG93IHBob25lIG51bWJlclxuICAgIGluaXRUb2dnbGVQaG9uZSgpIHtcbiAgICAgICAgJCgnLmpzLXVzZXItcGhvbmUnKS5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgIC5hdHRyKCdocmVmJywgJ2phdmFzY3JpcHQ6dm9pZCgwKTsnKVxuICAgICAgICAgICAgICAgIC50ZXh0KCQodGhpcykuZGF0YSgncGhvbmUtaGlkZW4nKSk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtdXNlci1waG9uZS0tc2hvdycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgbGV0IHVzZXJQaG9uZSA9ICQodGhpcylcbiAgICAgICAgICAgICAgICAucGFyZW50KClcbiAgICAgICAgICAgICAgICAuZmluZCgnLmpzLXVzZXItcGhvbmUnKTtcbiAgICAgICAgICAgIHZhciBwaG9uZSA9IHVzZXJQaG9uZS5kYXRhKCdwaG9uZScpO1xuICAgICAgICAgICAgdXNlclBob25lXG4gICAgICAgICAgICAgICAgLnJlbW92ZUF0dHIoJ3N0eWxlJylcbiAgICAgICAgICAgICAgICAuYXR0cignaHJlZicsICd0ZWw6JyArIHBob25lKVxuICAgICAgICAgICAgICAgIC50ZXh0KHBob25lKTtcbiAgICAgICAgICAgICQodGhpcykuY3NzKCdkaXNwbGF5JywgJ25vbmUnKTtcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8vQ2l0eSBzZWxlY3RcbiAgICBpbml0Q2l0eVNlbGVjdCgpIHtcbiAgICAgICAgbGV0ICRjaGFuZ2VDaXR5ID0gJCgnLmpzLWNpdHktc2VsZWN0Jyk7XG4gICAgICAgIGxldCAkY2hhbmdlQ2l0eVRpdGxlID0gJGNoYW5nZUNpdHkuZmluZCgnLmNpdHktc2VsZWN0X190aXRsZSBzcGFuJyk7XG4gICAgICAgIGxldCAkaW5wdXQgPSAkY2hhbmdlQ2l0eS5maW5kKCdpbnB1dCcpO1xuXG4gICAgICAgICRpbnB1dC5vbignY2xpY2sgZm9jdXMnLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICB9KTtcblxuICAgICAgICAkY2hhbmdlQ2l0eS5maW5kKCcuY2l0eS1zZWxlY3RfX2l0ZW0nKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICRjaGFuZ2VDaXR5VGl0bGUudGV4dCgkKHRoaXMpLnRleHQoKSk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvL01vYmlsZSBTZWFyY2ggQnRuIG9wZW4vY2xvc2VcbiAgICBpbml0TW9iaWxlU2VhcmNoKCkge1xuICAgICAgICB2YXIgJHNlYXJjaEJ0biA9ICQoJy5qcy1tb2JpbGUtc2VhcmNoLWJ0bicpO1xuICAgICAgICBsZXQgb3BlbiA9IGZhbHNlO1xuICAgICAgICAkc2VhcmNoQnRuLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgaWYgKG9wZW4pIHtcbiAgICAgICAgICAgICAgICAkd3JhcHBlci5hZGRDbGFzcygnbW9iaWxlLXNlYXJjaC0tb3BlbicpO1xuICAgICAgICAgICAgICAgICRodG1sLmNzcygnaXMtZml4ZWQnKTtcbiAgICAgICAgICAgICAgICBvcGVuID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkd3JhcHBlci5yZW1vdmVDbGFzcygnbW9iaWxlLXNlYXJjaC0tb3BlbicpO1xuICAgICAgICAgICAgICAgICRodG1sLnJlbW92ZUNsYXNzKCdpcy1maXhlZCcpO1xuICAgICAgICAgICAgICAgIG9wZW4gPSB0cnVlO1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIEZvcm06IHtcbiAgICAgICAgaW5pdCgpIHtcbiAgICAgICAgICAgIHRoaXMuX2FkZEV2ZW50TGlzdGVuZXIoKTtcbiAgICAgICAgfSxcblxuICAgICAgICBjaGVja1ZhbGlkYXRpb24oKSB7XG4gICAgICAgICAgICBsZXQgJGJ0biA9ICQoJy5mb3JtLXN1Y2Nlc3NfX3JvbGUnKTtcbiAgICAgICAgICAgIGxldCAkZm9ybVN1Y2Nlc3MgPSAkKCcuZm9ybS1zdWNjZXNzX19yb2xlcycpO1xuXG4gICAgICAgICAgICAkKHRoaXMpLmNzcygnei1pbmRleCcsICcyMDAnKTtcblxuICAgICAgICAgICAgJGJ0bi5ub3QoJCh0aGlzKSkuYWRkQ2xhc3MoJ21vdmUtb3V0Jyk7XG4gICAgICAgICAgICAkZm9ybVN1Y2Nlc3MuYWRkQ2xhc3MoJ2lzLWVycm9yJyk7XG5cbiAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgICAgICRidG4ubm90KCQodGhpcykpLmhpZGUoKTtcbiAgICAgICAgICAgIH0sIDEwMCk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgX2FkZEV2ZW50TGlzdGVuZXIoKSB7XG4gICAgICAgICAgICAkKCcuanMtc3VjY2Vzcy1jbG9zZScpLm9uKCdjbGljaycsIGUgPT4ge1xuICAgICAgICAgICAgICAgICQoZS50YXJnZXQpXG4gICAgICAgICAgICAgICAgICAgIC5jbG9zZXN0KCcuY2hvb3NlLXJvbGUnKVxuICAgICAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ2Nob29zZS1yb2xlJyk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgJCgnLmpzLWVycm9yLWNsb3NlJykub24oJ2NsaWNrJywgZSA9PiB7XG4gICAgICAgICAgICAgICAgJChlLnRhcmdldClcbiAgICAgICAgICAgICAgICAgICAgLmNsb3Nlc3QoJy5pcy1lcnJvcicpXG4gICAgICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnaXMtZXJyb3InKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9LFxuICAgIH0sXG5cbiAgICBGaWx0ZXI6IHtcbiAgICAgICAgb3BlbjogZmFsc2UsXG5cbiAgICAgICAgaW5pdCgpIHtcbiAgICAgICAgICAgIHRoaXMuX2FkZEV2ZW50TGlzdGVuZXIoKTtcbiAgICAgICAgfSxcblxuICAgICAgICBfYWRkRXZlbnRMaXN0ZW5lcigpIHtcbiAgICAgICAgICAgICQoJy5qcy1tb2JpbGUtZmlsdGVyLS1vcGVuJykub24oJ2NsaWNrJywgZSA9PiB7XG4gICAgICAgICAgICAgICAgdGhpcy5fdG9nZ2xlKGUpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAkKCcuanMtY2F0YWxvZy1maWx0ZXItLWNsb3NlJykub24oJ2NsaWNrJywgZSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKHRoaXMub3Blbikge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLl90b2dnbGUoZSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgX3RvZ2dsZShlKSB7XG4gICAgICAgICAgICBsZXQgT1BFTl9DTEFTUyA9ICdpcy1vcGVuJztcbiAgICAgICAgICAgIGxldCAkY2F0YWxvZ0ZpbHRlciA9ICQoZG9jdW1lbnQpLmZpbmQoJy5jYXRhbG9nLWZpbHRlcicpO1xuICAgICAgICAgICAgaWYgKCF0aGlzLm9wZW4pIHtcbiAgICAgICAgICAgICAgICAkaHRtbC5hZGRDbGFzcygnaXMtZml4ZWQnKTtcbiAgICAgICAgICAgICAgICAkY2F0YWxvZ0ZpbHRlci5hZGRDbGFzcyhPUEVOX0NMQVNTKTtcbiAgICAgICAgICAgICAgICB0aGlzLm9wZW4gPSB0cnVlO1xuXG4gICAgICAgICAgICAgICAgaWYgKCQod2luZG93KS53aWR0aCgpID4gNDgwKSB7XG4gICAgICAgICAgICAgICAgICAgICRvdmVybGF5LmFkZENsYXNzKCdpcy12aXNpYmxlIG92ZXJsYXktLWZpbHRlcicpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJGh0bWwucmVtb3ZlQXR0cignc3R5bGUnKS5yZW1vdmVDbGFzcygnaXMtZml4ZWQnKTtcbiAgICAgICAgICAgICAgICAkb3ZlcmxheS5yZW1vdmVDbGFzcygnaXMtdmlzaWJsZSBvdmVybGF5LS1maWx0ZXInKTtcbiAgICAgICAgICAgICAgICAkY2F0YWxvZ0ZpbHRlci5yZW1vdmVDbGFzcyhPUEVOX0NMQVNTKTtcbiAgICAgICAgICAgICAgICB0aGlzLm9wZW4gPSBmYWxzZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgfSxcbiAgICB9LFxufTtcblxuQmFzZS5kZWZpbmUgPSBmdW5jdGlvbihuYW1lc3BhY2UpIHtcbiAgICB2YXIgcGFydHMgPSBuYW1lc3BhY2Uuc3BsaXQoJy4nKSxcbiAgICAgICAgcGFyZW50ID0gQmFzZSxcbiAgICAgICAgaTtcblxuICAgIC8v0KPQsdGA0LDRgtGMICDQvdCw0YfQsNC70YzQvdGL0Lkg0L/RgNC10YTQuNC60YEg0LXRgdC70Lgg0Y3RgtC+INGP0LLQu9GP0LXRgtGB0YHRjyDQs9C70L7QsdCw0LvRjNC90L7QuSDQv9C10YDQtdC80LXQvdC90L7QuVxuICAgIGlmIChwYXJ0c1swXSA9PSAnQmFzZScpIHtcbiAgICAgICAgcGFydHMgPSBwYXJ0cy5zbGljZSgxKTtcbiAgICB9XG5cbiAgICAvL9CV0YHQu9C4INCyINCz0LvQvtCx0LDQu9GM0L3QvtC8INC+0LHRitC10LrRgtC1ICDQvdC10YIgINGB0LLQvtC50YHRgtCy0LAgIC0g0YHQvtC30LTQsNGC0YwgINC10LPQvi5cbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IHBhcnRzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgIGlmICh0eXBlb2YgcGFyZW50W3BhcnRzW2ldXSA9PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgICAgcGFyZW50W3BhcnRzW2ldXSA9IHt9O1xuICAgICAgICB9XG4gICAgICAgIHBhcmVudCA9IHBhcmVudFtwYXJ0c1tpXV07XG4gICAgfVxuICAgIHJldHVybiBwYXJlbnQ7XG59O1xuXG4vKipcbiAqIEJhc2UuVXRpbHMuanNcbiAqXG4gKiBAYXV0aG9yIEFudG9uIFVzdGlub2ZmIDxhLmEudXN0aW5vZmZAZ21haWwuY29tPlxuICovXG5cbkJhc2UuZGVmaW5lKCdVdGlscycpO1xuXG5CYXNlLlV0aWxzID0gKGZ1bmN0aW9uKCkge1xuICAgIGZ1bmN0aW9uIF9pbml0KCkge1xuICAgICAgICBfZGV0ZWN0QnJvd3NlclR5cGUoKTtcbiAgICAgICAgX2luaXRMYXp5TG9hZEltYWdlKCk7XG4gICAgfVxuXG4gICAgLy/Qn9GA0L7QstC10YDQutCwINGC0LjQv9CwINCx0YDQsNGD0LfQtdGA0LBcbiAgICBmdW5jdGlvbiBfZGV0ZWN0QnJvd3NlclR5cGUoKSB7XG4gICAgICAgIHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKCdsb2FkJywgKCkgPT4ge1xuICAgICAgICAgICAgbGV0IGh0bWwgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdodG1sJyk7XG4gICAgICAgICAgICBsZXQgaXNPcGVyYSA9XG4gICAgICAgICAgICAgICAgISF3aW5kb3cub3BlcmEgfHwgbmF2aWdhdG9yLnVzZXJBZ2VudC5pbmRleE9mKCcgT1BSLycpID49IDA7XG4gICAgICAgICAgICBsZXQgaXNDaHJvbWUgPSAhIXdpbmRvdy5jaHJvbWUgJiYgIWlzT3BlcmE7XG4gICAgICAgICAgICBsZXQgaXNFeHBsb3JlciA9XG4gICAgICAgICAgICAgICAgdHlwZW9mIGRvY3VtZW50ICE9PSAndW5kZWZpbmVkJyAmJlxuICAgICAgICAgICAgICAgICEhZG9jdW1lbnQuZG9jdW1lbnRNb2RlICYmXG4gICAgICAgICAgICAgICAgIWlzRWRnZTtcbiAgICAgICAgICAgIGxldCBpc0ZpcmVmb3ggPSB0eXBlb2Ygd2luZG93Lkluc3RhbGxUcmlnZ2VyICE9PSAndW5kZWZpbmVkJztcbiAgICAgICAgICAgIGxldCBpc1NhZmFyaSA9IC9eKCg/IWNocm9tZXxhbmRyb2lkKS4pKnNhZmFyaS9pLnRlc3QoXG4gICAgICAgICAgICAgICAgbmF2aWdhdG9yLnVzZXJBZ2VudFxuICAgICAgICAgICAgKTtcbiAgICAgICAgICAgIGxldCB1c2VyQWdlbnQgPVxuICAgICAgICAgICAgICAgIG5hdmlnYXRvci51c2VyQWdlbnQgfHwgbmF2aWdhdG9yLnZlbmRvciB8fCB3aW5kb3cub3BlcmE7XG5cbiAgICAgICAgICAgIC8vIGlmICgvd2luZG93cyBwaG9uZS9pLnRlc3QodXNlckFnZW50KSkge1xuICAgICAgICAgICAgLy8gICAgIGh0bWwuY2xhc3NMaXN0LmFkZCgnaXMtd2luZG93cycpO1xuICAgICAgICAgICAgLy8gfVxuXG4gICAgICAgICAgICBpZiAoL2FuZHJvaWQvaS50ZXN0KHVzZXJBZ2VudCkpIHtcbiAgICAgICAgICAgICAgICBodG1sLmNsYXNzTGlzdC5hZGQoJ2lzLWFuZHJvaWQnKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKC9pUGFkfGlQaG9uZXxpUG9kLy50ZXN0KHVzZXJBZ2VudCkgJiYgIXdpbmRvdy5NU1N0cmVhbSkge1xuICAgICAgICAgICAgICAgIGh0bWwuY2xhc3NMaXN0LmFkZCgnaXMtaW9zJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChpc0Nocm9tZSkge1xuICAgICAgICAgICAgICAgIGh0bWwuY2xhc3NMaXN0LmFkZCgnaXMtY2hyb21lJyk7XG4gICAgICAgICAgICB9IGVsc2UgaWYgKGlzU2FmYXJpKSB7XG4gICAgICAgICAgICAgICAgaHRtbC5jbGFzc0xpc3QuYWRkKCdpcy1zYWZhcmknKTtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAoaXNGaXJlZm94KSB7XG4gICAgICAgICAgICAgICAgaHRtbC5jbGFzc0xpc3QuYWRkKCdpcy1maXJlZm94Jyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8v0J/RgNC+0LLQtdGA0LrQsCDRgdC60L7RgNC+0YHRgtC4INGB0L7QtdC00LjQvdC10L3QuNGPXG4gICAgZnVuY3Rpb24gX3Rlc3RTbG93Q29ubmVjdGlvbigpIHtcbiAgICAgICAgbGV0IGNvbm5lY3Rpb24gPVxuICAgICAgICAgICAgbmF2aWdhdG9yLmNvbm5lY3Rpb24gfHxcbiAgICAgICAgICAgIG5hdmlnYXRvci5tb3pDb25uZWN0aW9uIHx8XG4gICAgICAgICAgICBuYXZpZ2F0b3Iud2Via2l0Q29ubmVjdGlvbjtcblxuICAgICAgICBsZXQgc2xvd1NwZWVkO1xuXG4gICAgICAgIGlmIChcbiAgICAgICAgICAgIHR5cGVvZiBjb25uZWN0aW9uICE9PSAndW5kZWZpbmVkJyAmJlxuICAgICAgICAgICAgY29ubmVjdGlvbi5lZmZlY3RpdmVUeXBlID09PSAnM2cnXG4gICAgICAgICkge1xuICAgICAgICAgICAgc2xvd1NwZWVkID0gdHJ1ZTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIHNsb3dTcGVlZCA9IGZhbHNlO1xuXG4gICAgICAgICAgICBsZXQgbG9hZFRpbWUgPVxuICAgICAgICAgICAgICAgICh3aW5kb3cucGVyZm9ybWFuY2UudGltaW5nLmRvbUNvbnRlbnRMb2FkZWRFdmVudEVuZCAtXG4gICAgICAgICAgICAgICAgICAgIHdpbmRvdy5wZXJmb3JtYW5jZS50aW1pbmcubmF2aWdhdGlvblN0YXJ0KSAvXG4gICAgICAgICAgICAgICAgMTAwMDtcblxuICAgICAgICAgICAgaWYgKGxvYWRUaW1lID4gNCkge1xuICAgICAgICAgICAgICAgIHNsb3dTcGVlZCA9IHRydWU7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIHNsb3dTcGVlZDtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdExhenlMb2FkSW1hZ2UoKSB7XG4gICAgICAgIG5ldyBMYXp5TG9hZCh7XG4gICAgICAgICAgICBlbGVtZW50c19zZWxlY3RvcjogJy5sYXp5LWltZycsXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9zbGlkZVVwKCkge1xuICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAuY2xvc2VzdCgnW2RhdGEtY29udGFpbmVyXScpXG4gICAgICAgICAgICAuc2xpZGVVcCgpO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9zbGlkZVRvZ2dsZSgpIHtcbiAgICAgICAgbGV0IE9QRU5fQ0xBU1MgPSAnaXMtb3Blbic7XG4gICAgICAgIGxldCAkY29udGFpbmVyID0gJCh0aGlzKS5jbG9zZXN0KCdbZGF0YS1jb250YWluZXJdJyk7XG4gICAgICAgIGxldCAkY29udGVudCA9ICRjb250YWluZXIuZmluZCgnW2RhdGEtY29udGVudF0nKTtcblxuICAgICAgICBpZiAoISRjb250YWluZXIuaGFzQ2xhc3MoT1BFTl9DTEFTUykpIHtcbiAgICAgICAgICAgICRjb250ZW50LnNsaWRlRG93bigpO1xuICAgICAgICAgICAgJGNvbnRhaW5lci5hZGRDbGFzcyhPUEVOX0NMQVNTKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICRjb250ZW50LnNsaWRlVXAoKTtcbiAgICAgICAgICAgICRjb250YWluZXIucmVtb3ZlQ2xhc3MoT1BFTl9DTEFTUyk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfY29weUJveCgpIHtcbiAgICAgICAgbGV0ICRjbG9uZSA9ICQodGhpcylcbiAgICAgICAgICAgIC5jbG9zZXN0KCdbZGF0YS1jb3B5LWJveF0nKVxuICAgICAgICAgICAgLmNsb25lKCk7XG4gICAgICAgICRjbG9uZS5pbnNlcnRBZnRlcigkKHRoaXMpKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfYW5pbWF0ZVdpZHRoKCkge1xuICAgICAgICBsZXQgd2lkdGggPSAkKHRoaXMpLmRhdGEoJ3dpZHRoJyk7XG4gICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgJCh0aGlzKS5hbmltYXRlKHsgd2lkdGg6IHdpZHRoIH0sIDYwMCk7XG4gICAgICAgIH0sIDMwMCk7XG4gICAgICAgIGNvbnNvbGUubG9nKCctLS0gd2lkdGgnLCB3aWR0aCk7XG4gICAgICAgIGNvbnNvbGUubG9nKCctLS0nLCAnYW5pbWF0ZSB3aWR0aCcpO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9maWVsZEVkaXQoKSB7XG4gICAgICAgIGxldCBmaWVsZEVkaXQgPSAnLmpzLWZpZWxkLWVkaXQnO1xuICAgICAgICBsZXQgJGZpZWxkRWRpdCA9ICQoZG9jdW1lbnQpLmZpbmQoJy5qcy1maWVsZC1lZGl0Jyk7XG5cbiAgICAgICAgaWYgKCRmaWVsZEVkaXQubGVuZ3RoKSB7XG4gICAgICAgICAgICAkZmllbGRFZGl0LmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgbGV0ICRmaWVsZEVkaXRJbnB1dCA9ICQodGhpcykuZmluZCgnLmZpZWxkLWVkaXRfX2lucHV0Jyk7XG4gICAgICAgICAgICAgICAgbGV0ICRmaWVsZEVkaXRCdG4gPSAkKHRoaXMpLmZpbmQoJy5maWVsZC1lZGl0X19idG4nKTtcblxuICAgICAgICAgICAgICAgICRmaWVsZEVkaXRCdG4ub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCAkZmllbGRFZGl0SW5wdXQgPSAkKHRoaXMpXG4gICAgICAgICAgICAgICAgICAgICAgICAuY2xvc2VzdChmaWVsZEVkaXQpXG4gICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnLmZpZWxkLWVkaXRfX2lucHV0Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgbGV0ICRmaWVsZEVkaXRUZXh0ID0gJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAgICAgLmNsb3Nlc3QoZmllbGRFZGl0KVxuICAgICAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy5maWVsZC1lZGl0X190ZXh0Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgbGV0IGZpZWxkRWRpdFRleHQgPSAkZmllbGRFZGl0VGV4dC50ZXh0KCk7XG5cbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgICRmaWVsZEVkaXRUZXh0LmhpZGUoKTtcblxuICAgICAgICAgICAgICAgICAgICAkZmllbGRFZGl0SW5wdXRcbiAgICAgICAgICAgICAgICAgICAgICAgIC52YWwoZmllbGRFZGl0VGV4dClcbiAgICAgICAgICAgICAgICAgICAgICAgIC5zaG93KClcbiAgICAgICAgICAgICAgICAgICAgICAgIC5zZWxlY3QoKTtcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICRmaWVsZEVkaXRJbnB1dFxuICAgICAgICAgICAgICAgICAgICAuYmx1cihmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGxldCAkZmllbGRFZGl0VGV4dCA9ICQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuY2xvc2VzdChmaWVsZEVkaXQpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy5maWVsZC1lZGl0X190ZXh0Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICgkLnRyaW0odGhpcy52YWx1ZSkgPT0gJycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLnZhbHVlID0gdGhpcy5kZWZhdWx0VmFsdWVcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPyB0aGlzLmRlZmF1bHRWYWx1ZVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA6ICcnO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkZmllbGRFZGl0VGV4dC5odG1sKHRoaXMudmFsdWUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICRmaWVsZEVkaXRCdG4uc2hvdygpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJGZpZWxkRWRpdFRleHQuc2hvdygpO1xuICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgICAgICAua2V5cHJlc3MoZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGxldCAkZmllbGRFZGl0VGV4dCA9ICQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuY2xvc2VzdChmaWVsZEVkaXQpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy5maWVsZC1lZGl0X190ZXh0Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChldmVudC5rZXlDb2RlID09ICcxMycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoJC50cmltKHRoaXMudmFsdWUpID09ICcnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMudmFsdWUgPSB0aGlzLmRlZmF1bHRWYWx1ZVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPyB0aGlzLmRlZmF1bHRWYWx1ZVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgOiAnJztcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkZmllbGRFZGl0VGV4dC5odG1sKHRoaXMudmFsdWUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRmaWVsZEVkaXRCdG4ucmVtb3ZlQXR0cignc3R5bGUnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkZmllbGRFZGl0VGV4dC5zaG93KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICQoZG9jdW1lbnQpLm9uKCd0b3VjaHN0YXJ0JywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgIGlmICgkKGUudGFyZ2V0KS5jbG9zZXN0KGZpZWxkRWRpdCkubGVuZ3RoKSByZXR1cm47XG4gICAgICAgICAgICAgICAgbGV0ICRpbnB1dCA9ICRmaWVsZEVkaXQuZmluZCgnaW5wdXQnKTtcbiAgICAgICAgICAgICAgICBsZXQgJHRleHQgPSAkZmllbGRFZGl0LmZpbmQoJy5maWVsZC1lZGl0X190ZXh0Jyk7XG4gICAgICAgICAgICAgICAgbGV0ICRidG4gPSAkZmllbGRFZGl0LmZpbmQoJy5maWVsZC1lZGl0X19idG4nKTtcblxuICAgICAgICAgICAgICAgIGlmICgkLnRyaW0oJGlucHV0LnZhbHVlKSA9PSAnJykge1xuICAgICAgICAgICAgICAgICAgICAkaW5wdXQudmFsdWUgPSAkaW5wdXQuZGVmYXVsdFZhbHVlXG4gICAgICAgICAgICAgICAgICAgICAgICA/ICRpbnB1dC5kZWZhdWx0VmFsdWVcbiAgICAgICAgICAgICAgICAgICAgICAgIDogJyc7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgJHRleHQuaHRtbCgkaW5wdXQudmFsdWUpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICRmaWVsZEVkaXQuZmluZCgnaW5wdXQnKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgJGZpZWxkRWRpdC5maW5kKCcuZmllbGQtZWRpdF9fdGV4dCcpLnNob3coKTtcbiAgICAgICAgICAgICAgICAkZmllbGRFZGl0LmZpbmQoJy5maWVsZC1lZGl0X19idG4nKS5zaG93KCk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIF90b2dnbGVDbGFzc0F0QmxvY2soYmxvY2ssIGNsKSB7XG4gICAgICAgICQoYmxvY2sgKyAnLS1vcGVuJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAkKGJsb2NrKS5hZGRDbGFzcyhjbCk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgICQoYmxvY2sgKyAnLS1jbG9zZScpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgJChibG9jaykucmVtb3ZlQ2xhc3MoY2wpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfdG9nZ2xlQ2xhc3NBdEJsb2NrQ2xpY2tPdXRzaWRlKGJsb2NrLCBjbCkge1xuICAgICAgICAkKGJsb2NrKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICQodGhpcykudG9nZ2xlQ2xhc3MoY2wpO1xuICAgICAgICB9KTtcblxuICAgICAgICAkKGRvY3VtZW50KS5vbignY2xpY2sgdG91Y2hzdGFydCcsIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgIGlmICgkKGUudGFyZ2V0KS5jbG9zZXN0KGJsb2NrKS5sZW5ndGgpIHJldHVybjtcbiAgICAgICAgICAgICQoYmxvY2spLnJlbW92ZUNsYXNzKGNsKTtcbiAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIHJldHVybiB7XG4gICAgICAgIGluaXQ6IF9pbml0LFxuICAgICAgICBpbml0TGF6eUxvYWRJbWFnZTogX2luaXRMYXp5TG9hZEltYWdlLFxuICAgICAgICBzbGlkZVVwOiBfc2xpZGVVcCxcbiAgICAgICAgc2xpZGVUb2dnbGU6IF9zbGlkZVRvZ2dsZSxcbiAgICAgICAgY29weUJveDogX2NvcHlCb3gsXG4gICAgICAgIGFuaW1hdGVXaWR0aDogX2FuaW1hdGVXaWR0aCxcbiAgICAgICAgZmllbGRFZGl0OiBfZmllbGRFZGl0LFxuICAgICAgICB0b2dnbGVDbGFzc0F0QmxvY2s6IF90b2dnbGVDbGFzc0F0QmxvY2ssXG4gICAgICAgIHRvZ2dsZUNsYXNzQXRCbG9ja0NsaWNrT3V0c2lkZTogX3RvZ2dsZUNsYXNzQXRCbG9ja0NsaWNrT3V0c2lkZSxcbiAgICAgICAgdGVzdFNsb3dDb25uZWN0aW9uOiBfdGVzdFNsb3dDb25uZWN0aW9uLFxuICAgIH07XG59KSgpO1xuXG4vKipcbiAqIEJhc2UuQ29tcG9uZW50cy5qc1xuICpcbiAqIEBhdXRob3IgQW50b24gVXN0aW5vZmYgPGEuYS51c3Rpbm9mZkBnbWFpbC5jb20+XG4gKi9cblxuQmFzZS5kZWZpbmUoJ0NvbXBvbmVudHMnKTtcblxuQmFzZS5Db21wb25lbnRzID0gKGZ1bmN0aW9uKCkge1xuICAgIGZ1bmN0aW9uIF9pbml0KCkge1xuICAgICAgICBfaW5pdENoZWNrQm94KCk7XG4gICAgICAgIF9pbml0Q2hlY2tCb3hTZWxlY3RBbGwoKTtcbiAgICAgICAgX2luaXRUYWIoKTtcbiAgICAgICAgX2luaXRDaGFuZ2VyKCk7XG4gICAgICAgIF9pbml0Qm94Q29sb3IoKTtcbiAgICAgICAgX2luaXRQYXJhbGxheCgpO1xuICAgICAgICBfaW5pdEFjY29yZGVvbigpO1xuICAgICAgICBfaW5pdFByb2dyZXNzTGluZSgpO1xuICAgICAgICBfaW5pdENvcHlCb3goKTtcbiAgICAgICAgX2luaXRWaWV3ZXIoKTtcbiAgICAgICAgX2luaXRWaWRlb0xvYWRlcigpO1xuICAgICAgICBfaW5pdFN0aWNreUJsb2NrKCk7XG4gICAgICAgIF9pbml0U2VydmljZXNCdG5JbmZvKCk7XG4gICAgICAgIF9pbml0QWRkRm9ybSgpO1xuXG4gICAgICAgIEJhc2UuQ29tcG9uZW50cy5CdXR0b24uaW5pdCgpO1xuICAgICAgICBCYXNlLkNvbXBvbmVudHMuSW5wdXQuaW5pdCgpO1xuICAgICAgICBCYXNlLkNvbXBvbmVudHMuUG9wdXAuaW5pdCgpO1xuICAgICAgICBCYXNlLkNvbXBvbmVudHMuU2VsZWN0LmluaXQoKTtcbiAgICAgICAgQmFzZS5Db21wb25lbnRzLlNsaWRlci5pbml0KCk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gX2luaXRDaGVja0JveCgpIHtcbiAgICAgICAgbmV3IENoZWNrQm94KHsgc2VsZWN0b3I6ICcuanMtYmItY2hlY2tib3gnIH0pLmluaXQoKTtcbiAgICAgICAgbmV3IFJhZGlvKHsgc2VsZWN0b3I6ICcuanMtYmItcmFkaW8nIH0pO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9pbml0Q2hlY2tCb3hTZWxlY3RBbGwoKSB7XG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtYmItY2hlY2tib3gtLXNlbGVjdC1hbGwnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGlmICgkKHRoaXMpLmhhc0NsYXNzKCdpcy1zZWxlY3RlZCcpKSB7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmVDbGFzcygnaXMtc2VsZWN0ZWQnKTtcbiAgICAgICAgICAgICAgICBsZXQgJGNoZWNrYm94cyA9ICQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgLmNsb3Nlc3QoJ2RpdicpXG4gICAgICAgICAgICAgICAgICAgIC5maW5kKCcuanMtYmItY2hlY2tib3gnKTtcbiAgICAgICAgICAgICAgICAkY2hlY2tib3hzLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmICghJCh0aGlzKS5oYXNDbGFzcygnanMtbm8tYWxsLWNoZWNrJykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ2lzLWNoZWNrZWQnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5maW5kKCdpbnB1dCcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUF0dHIoJ2NoZWNrZWQnKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdpcy1zZWxlY3RlZCcpO1xuICAgICAgICAgICAgICAgIGxldCAkY2hlY2tib3hzID0gJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAuY2xvc2VzdCgnZGl2JylcbiAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy5qcy1iYi1jaGVja2JveCcpO1xuICAgICAgICAgICAgICAgICRjaGVja2JveHMuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCEkKHRoaXMpLmhhc0NsYXNzKCdqcy1uby1hbGwtY2hlY2snKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnaXMtY2hlY2tlZCcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmZpbmQoJ2lucHV0JylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAucHJvcCgnY2hlY2tlZCcsICdjaGVja2VkJylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYXR0cignY2hlY2tlZCcsIHRydWUpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9pbml0VGFiKCkge1xuICAgICAgICBsZXQgJHRhYnMgPSAkKCcuanMtYmItdGFiJyk7XG5cbiAgICAgICAgaWYgKCR0YWJzLmxlbmd0aCkge1xuICAgICAgICAgICAgY29uc29sZS5sb2coJy0tLScsICdpbml0Jyk7XG4gICAgICAgICAgICAkdGFicy50YWJzKCk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdENoYW5nZXIoKSB7XG4gICAgICAgIGxldCAkY2hhbmdlciA9ICRkb2N1bWVudC5maW5kKCcuanMtY2hhbmdlcicpO1xuICAgICAgICBsZXQgQ0hFQ0tFRF9DTEFTUyA9ICdpcy1jaGVja2VkJztcblxuICAgICAgICBpZiAoJGNoYW5nZXIubGVuZ3RoKSB7XG4gICAgICAgICAgICAkY2hhbmdlci5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGxldCAkaXRlbSA9ICQodGhpcykuZmluZCgnLmNoYW5nZXJfX2l0ZW0nKTtcbiAgICAgICAgICAgICAgICBsZXQgJGJ0blJlc2V0ID0gJCh0aGlzKS5maW5kKCcuY2hhbmdlcl9fcmVzZXQnKTtcblxuICAgICAgICAgICAgICAgICRpdGVtLm9uKCdjbGljaycsIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgICAgICAgICAgJGl0ZW0ubm90KCQodGhpcykpLnJlbW92ZUNsYXNzKENIRUNLRURfQ0xBU1MpO1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKENIRUNLRURfQ0xBU1MpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vIGUuc3RvcFByb3BhZ2F0aW9uKCk7INCd0LUg0YDQsNGB0LrQvtC80LXQvdGC0LjRgNC+0LLQsNGC0YxcbiAgICAgICAgICAgICAgICAgICAgLy8gZS5wcmV2ZW50RGVmYXVsdCgpOyDQndC1INGA0LDRgdC60L7QvNC10L3RgtC40YDQvtCy0LDRgtGMXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAkYnRuUmVzZXQub24oJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgICAgICBsZXQgJHBhcmVudCA9ICQodGhpcykuY2xvc2VzdCgnLmNoYW5nZXJfX2l0ZW0nKTtcblxuICAgICAgICAgICAgICAgICAgICAkcGFyZW50LnJlbW92ZUNsYXNzKENIRUNLRURfQ0xBU1MpO1xuICAgICAgICAgICAgICAgICAgICAvLyBlLnN0b3BQcm9wYWdhdGlvbigpOyDQndC1INGA0LDRgdC60L7QvNC10L3RgtC40YDQvtCy0LDRgtGMXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9pbml0Qm94Q29sb3IoKSB7XG4gICAgICAgIG5ldyBCb3hDb2xvcigpO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9pbml0UGFyYWxsYXgoKSB7XG4gICAgICAgIG5ldyBQYXJhbGxheEJsb2NrKHsgc2VsZWN0b3I6ICcuanMtcGFyYWxsYXgnLCB0cmFuc2Zvcm06IHRydWUgfSkuaW5pdCgpO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9pbml0UHJvZ3Jlc3NMaW5lKCkge1xuICAgICAgICBsZXQgZWxlbWVudHMgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCdbZGF0YS1wcm9ncmVzc10nKTtcbiAgICAgICAgaWYgKGVsZW1lbnRzLmxlbmd0aCkge1xuICAgICAgICAgICAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAgICAgZWxlbWVudHMuZm9yRWFjaChlbGVtID0+IHtcbiAgICAgICAgICAgICAgICAgICAgbGV0IGRhdGEgPSBlbGVtLmdldEF0dHJpYnV0ZSgnZGF0YS1wcm9ncmVzcycpO1xuICAgICAgICAgICAgICAgICAgICBpZiAoZGF0YSA+IDEwMCkgcmV0dXJuO1xuICAgICAgICAgICAgICAgICAgICBpZiAoZGF0YSA8IDkwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBlbGVtLnN0eWxlLndpZHRoID0gcGFyc2VJbnQoZGF0YSkgKyA1ICsgJyUnO1xuICAgICAgICAgICAgICAgICAgICAgICAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZWxlbS5zdHlsZS53aWR0aCA9IHBhcnNlSW50KGRhdGEpICsgJyUnO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSwgMTAwMCk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBlbGVtLnN0eWxlLndpZHRoID0gcGFyc2VJbnQoZGF0YSkgKyAnJSc7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0sIDEwMDApO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gX2luaXRBY2NvcmRlb24oKSB7XG4gICAgICAgIG5ldyBBY2NvcmRlb24oeyBzZWxlY3RvcjogJy5qcy1iYi1hY2NvcmRlb24nIH0pLmluaXQoKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdENvcHlCb3goKSB7XG4gICAgICAgIG5ldyBDb3B5Qm94KHsgc2VsZWN0b3I6ICcuanMtY29weS1ib3gnIH0pLmluaXQoKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdFZpZXdlcigpIHtcbiAgICAgICAgbmV3IFZpZXdlcih7IHNlbGVjdG9yOiAnLmpzLXZpZXdlcicgfSkuaW5pdCgpO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9pbml0VmlkZW9Mb2FkZXIoKSB7XG4gICAgICAgIG5ldyBWaWRlb0xvYWRlcigpLmluaXQoKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdFN0aWNreUJsb2NrKCkge1xuICAgICAgICBsZXQgc3RpY2t5QmxvY2sgPSAnW2RhdGEtc3RpY2t5XSc7XG4gICAgICAgIGxldCAkc3RpY2t5QmxvY2sgPSAkKHN0aWNreUJsb2NrKTtcbiAgICAgICAgbGV0IHN0aWNreUJsb2NrQ29udGFpbmVyID0gJ1tkYXRhLXN0aWNreS1jb250YWluZXJdJztcbiAgICAgICAgbGV0IHN0aWNreUJsb2NrSW5uZXIgPSAnW2RhdGEtc3RpY2t5LWlubmVyXSc7XG4gICAgICAgIGxldCBtYXhXaW5kb3dXaWR0aCA9ICRzdGlja3lCbG9jay5kYXRhKCdzdGlja3ktd2lkdGgnKSB8fCA3Njg7XG4gICAgICAgIGxldCBzdGlja3lCbG9ja1RvcFNwYXNpbmcgPVxuICAgICAgICAgICAgJHN0aWNreUJsb2NrLmRhdGEoJ3N0aWNreS10b3Atc3BhY2luZycpIHx8IDEwO1xuICAgICAgICBsZXQgc3RpY2t5QmxvY2tCb3R0b21TcGFzaW5nID1cbiAgICAgICAgICAgICRzdGlja3lCbG9jay5kYXRhKCdzdGlja3ktYm90dG9tLXNwYWNpbmcnKSB8fCAxMDtcblxuICAgICAgICBpZiAoJHN0aWNreUJsb2NrLmxlbmd0aCAmJiAkKHdpbmRvdykud2lkdGgoKSA+IG1heFdpbmRvd1dpZHRoKSB7XG4gICAgICAgICAgICBuZXcgU3RpY2t5U2lkZWJhcihzdGlja3lCbG9jaywge1xuICAgICAgICAgICAgICAgIHRvcFNwYWNpbmc6IHN0aWNreUJsb2NrVG9wU3Bhc2luZyxcbiAgICAgICAgICAgICAgICBib3R0b21TcGFjaW5nOiBzdGlja3lCbG9ja0JvdHRvbVNwYXNpbmcsXG4gICAgICAgICAgICAgICAgY29udGFpbmVyU2VsZWN0b3I6IHN0aWNreUJsb2NrQ29udGFpbmVyLFxuICAgICAgICAgICAgICAgIGlubmVyV3JhcHBlclNlbGVjdG9yOiBzdGlja3lCbG9ja0lubmVyLFxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdFNlcnZpY2VzQnRuSW5mbygpIHtcbiAgICAgICAgbGV0IGluZm9CdG4gPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCcuanMtY2FyZC1zZXJ2aWNlcy1pdGVtLS1pbmZvJyk7XG4gICAgICAgIGlmIChpbmZvQnRuLmxlbmd0aCkge1xuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBpbmZvQnRuLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICAgICAgbGV0IHBhcmVudCA9IGluZm9CdG5baV0uY2xvc2VzdCgnLmNhcmQtc2VydmljZXMtaXRlbScpO1xuICAgICAgICAgICAgICAgIGxldCBpdGVtRGVzY3JpcHRpb24gPSBwYXJlbnQucXVlcnlTZWxlY3RvcihcbiAgICAgICAgICAgICAgICAgICAgJy5jYXJkLXNlcnZpY2VzLWl0ZW1fX2Rlc2MnXG4gICAgICAgICAgICAgICAgKTtcbiAgICAgICAgICAgICAgICBsZXQgYnRuQ2xvc2UgPSBpdGVtRGVzY3JpcHRpb24ucXVlcnlTZWxlY3RvcihcbiAgICAgICAgICAgICAgICAgICAgJy5jYXJkLXNlcnZpY2VzLWl0ZW1fX2Rlc2MtaGlkZSdcbiAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgIGluZm9CdG5baV0uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBlID0+IHtcbiAgICAgICAgICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICAgICBCYXNlLlV0aWxzLnNsaWRlVG9nZ2xlLmNhbGwoaW5mb0J0bltpXSk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgYnRuQ2xvc2UuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBlID0+IHtcbiAgICAgICAgICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICAgICBCYXNlLlV0aWxzLnNsaWRlVG9nZ2xlLmNhbGwoaW5mb0J0bltpXSk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdEFkZEZvcm0oKSB7XG4gICAgICAgIG5ldyBBZGRGb3JtKHsgc2VsZWN0b3I6ICdqcy1hZGQtZm9ybScgfSkuaW5pdCgpO1xuICAgIH1cblxuICAgIHJldHVybiB7XG4gICAgICAgIGluaXQ6IF9pbml0LFxuICAgICAgICBpbml0Q2hlY2tCb3g6IF9pbml0Q2hlY2tCb3gsXG4gICAgICAgIGluaXRDaGVja0JveFNlbGVjdEFsbDogX2luaXRDaGVja0JveFNlbGVjdEFsbCxcbiAgICAgICAgaW5pdFRhYjogX2luaXRUYWIsXG4gICAgICAgIGluaXRDaGFuZ2VyOiBfaW5pdENoYW5nZXIsXG4gICAgICAgIGluaXRCb3hDb2xvcjogX2luaXRCb3hDb2xvcixcbiAgICAgICAgaW5pdFBhcmFsbGF4OiBfaW5pdFBhcmFsbGF4LFxuICAgICAgICBpbml0UHJvZ3Jlc3NMaW5lOiBfaW5pdFByb2dyZXNzTGluZSxcbiAgICAgICAgaW5pdEFjY29yZGVvbjogX2luaXRBY2NvcmRlb24sXG4gICAgICAgIGluaXRDb3B5Qm94OiBfaW5pdENvcHlCb3gsXG4gICAgICAgIGluaXRWaWV3ZXI6IF9pbml0Vmlld2VyLFxuICAgICAgICBpbml0VmlkZW9Mb2FkZXI6IF9pbml0VmlkZW9Mb2FkZXIsXG4gICAgICAgIGluaXRTdGlja3lCbG9jazogX2luaXRTdGlja3lCbG9jayxcbiAgICAgICAgaW5pdFNlcnZpY2VzQnRuSW5mbzogX2luaXRTZXJ2aWNlc0J0bkluZm8sXG4gICAgICAgIGluaXRBZGRGb3JtOiBfaW5pdEFkZEZvcm0sXG4gICAgfTtcbn0pKCk7XG5cbi8qIGh0bWwgZXhhbXBsZSBBY2NvcmRlb25cbjxkaXYgY2xhc3M9XCJiYi1hY2NvcmRlb24ganMtYmItYWNjb3JkZW9uXCI+XG5cdDxkaXYgY2xhc3M9XCJiYi1hY2NvcmRlb25fX2l0ZW1cIj5cblx0XHQ8ZGl2IGNsYXNzPVwiYmItYWNjb3JkZW9uX190aXRsZVwiPjwvZGl2PlxuXHRcdDxkaXYgY2xhc3M9XCJiYi1hY2NvcmRlb25fX2NvbnRlbnRcIj48L2Rpdj5cbiAgICA8L2Rpdj5cbjwvZGl2PlxuKi9cblxuY2xhc3MgQWNjb3JkZW9uIHtcbiAgICBjb25zdHJ1Y3RvcihhcmdzKSB7XG4gICAgICAgIHRoaXMuc2VsZWN0b3IgPSBhcmdzLnNlbGVjdG9yO1xuICAgICAgICB0aGlzLnRpdGxlID0gJCh0aGlzLnNlbGVjdG9yKS5maW5kKCdbZGF0YS1hY2NvcmRlb24tdGl0bGVdJyk7XG4gICAgICAgIHRoaXMuY29udGVudCA9ICQodGhpcy5zZWxlY3RvcikuZmluZCgnW2RhdGEtYWNjb3JkZW9uLWNvbnRlbnRdJyk7XG4gICAgICAgIC8vIHRoaXMuYWxsID0gJCh0aGlzLnNlbGVjdG9yKS5hdHRyKCdkYXRhLWFjY29yZGVvbi1hbGwnKTtcbiAgICAgICAgdGhpcy5pdGVtID0gJCh0aGlzLnNlbGVjdG9yKS5maW5kKCcuYmItYWNjb3JkZW9uX19pdGVtJyk7XG4gICAgICAgIHRoaXMucnVuID0gdHJ1ZTtcbiAgICB9XG5cbiAgICBpbml0KCkge1xuICAgICAgICBpZiAodHlwZW9mIHRoaXMuc2VsZWN0b3IgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICB0aGlzLml0ZW0uZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBsZXQgJGNvbnRlbnQgPSAkKHRoaXMpLmZpbmQoJy5iYi1hY2NvcmRlb25fX2NvbnRlbnQnKTtcblxuICAgICAgICAgICAgICAgIGlmICgkKHRoaXMpLmhhc0NsYXNzKCdpcy1vcGVuJykpIHtcbiAgICAgICAgICAgICAgICAgICAgJGNvbnRlbnQuc2xpZGVEb3duKCk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgJGNvbnRlbnQuc2xpZGVVcCgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB0aGlzLl9zYW1lSW5pdCgpO1xuICAgICAgICAgICAgdGhpcy5fYWRkSXZlbnRMaXN0ZW5lcigpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgLy9SZW5kZXIgYXJyb3cgaWNvbiBpbiBhY2NvcmRlb24gdGl0bGVcbiAgICBfcmVuZGVySWNvbihpbnNlcnRJbnRvKSB7XG4gICAgICAgIGxldCBpY29uID1cbiAgICAgICAgICAgICc8c3ZnIGNsYXNzPVwiaWNvbiBpY29uLWFuZ2xlLS1ib2xkIFwiIHhtbG5zPVwiaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmdcIiB2aWV3Qm94PVwiMCAwIDIzOC4wMDMgMjM4LjAwM1wiPjxwYXRoIGQ9XCJNMTgxLjc3NiAxMDcuNzE5TDc4LjcwNSA0LjY0OGMtNi4xOTgtNi4xOTgtMTYuMjczLTYuMTk4LTIyLjQ3IDBzLTYuMTk4IDE2LjI3MyAwIDIyLjQ3bDkxLjg4MyA5MS44ODMtOTEuODgzIDkxLjg4M2MtNi4xOTggNi4xOTgtNi4xOTggMTYuMjczIDAgMjIuNDdzMTYuMjczIDYuMTk4IDIyLjQ3IDBsMTAzLjA3MS0xMDMuMDM5YTE1Ljc0MSAxNS43NDEgMCAwIDAgNC42NC0xMS4yODNjMC00LjEzLTEuNTI2LTguMTk5LTQuNjQtMTEuMzEzelwiPjwvcGF0aD48L3N2Zz4nO1xuXG4gICAgICAgICQoaWNvbikuYXBwZW5kVG8oaW5zZXJ0SW50byk7XG4gICAgfVxuXG4gICAgLy/QmNC90LjRhtC40LDQu9C40LfQsNGG0LjRjyDQsNC60LrQvtGA0LTQtdC+0L3QsCDQv9C+INC00LDRgtCwINCw0YLRgNC40LHRg9GC0LDQvCDQvdCwINGA0LDQt9GA0LXRiNC10L3QuNC4IDwgNDgwXG4gICAgX2luaXREYXRhQWNjb3JkZW9uKCkge1xuICAgICAgICBsZXQgbWFpblNjb3BlID0gdGhpcztcbiAgICAgICAgbGV0ICRpdGVtID0gJChkb2N1bWVudCkuZmluZCgnW2RhdGEtYWNjb3JkZW9uLWl0ZW1dJyk7XG5cbiAgICAgICAgJCgnW2RhdGEtYWNjb3JkZW9uXScpLmFkZENsYXNzKCdiYi1hY2NvcmRlb24ganMtYmItYWNjb3JkZW9uJyk7XG5cbiAgICAgICAgJCgnW2RhdGEtYWNjb3JkZW9uXScpLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBsZXQgY3VzdG9tQ2xhc3MgPSAkKHRoaXMpLmRhdGEoJ2FjY29yZGVvbi1jbGFzcycpO1xuICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcyhjdXN0b21DbGFzcyk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgICQoJ1tkYXRhLWFjY29yZGVvbi10aXRsZV0nKS5hZGRDbGFzcygnYmItYWNjb3JkZW9uX190aXRsZScpO1xuXG4gICAgICAgICQoJ1tkYXRhLWFjY29yZGVvbi10aXRsZV0nKS5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgbWFpblNjb3BlLl9yZW5kZXJJY29uKCQodGhpcykpO1xuICAgICAgICB9KTtcblxuICAgICAgICAkKCdbZGF0YS1hY2NvcmRlb24tY29udGVudF0nKVxuICAgICAgICAgICAgLmFkZENsYXNzKCdiYi1hY2NvcmRlb25fX2NvbnRlbnQnKVxuICAgICAgICAgICAgLmhpZGUoKTtcblxuICAgICAgICAkaXRlbS5hZGRDbGFzcygnYmItYWNjb3JkZW9uX19pdGVtJyk7XG5cbiAgICAgICAgJGl0ZW0uZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGlmICgkKHRoaXMpLmF0dHIoJ2RhdGEtYWNjb3JkZW9uLWl0ZW0nKSA9PT0gJ29wZW4nKSB7XG4gICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2lzLW9wZW4nKVxuICAgICAgICAgICAgICAgICAgICAuZmluZCgnW2RhdGEtYWNjb3JkZW9uLWNvbnRlbnRdJylcbiAgICAgICAgICAgICAgICAgICAgLnNob3coKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgX3NhbWVJbml0KCkge1xuICAgICAgICBpZiAoJCh3aW5kb3cpLndpZHRoKCkgPD0gNDgwKSB7XG4gICAgICAgICAgICB0aGlzLl9pbml0RGF0YUFjY29yZGVvbigpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgLy8gaWYgKHR5cGVvZiB0aGlzLmFsbCAhPSAndW5kZWZpbmVkJykge1xuICAgICAgICAgICAgLy8gICAgIHRoaXMuX2luaXREYXRhQWNjb3JkZW9uKCk7XG4gICAgICAgICAgICAvLyB9IGVsc2Uge1xuICAgICAgICAgICAgLy8gICAgIHRoaXMuZGVzdHJveSgpO1xuICAgICAgICAgICAgLy8gfVxuICAgICAgICAgICAgdGhpcy5kZXN0cm95KCk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBfYWRkSXZlbnRMaXN0ZW5lcigpIHtcbiAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5qcy1iYi1hY2NvcmRlb24gLmJiLWFjY29yZGVvbl9fdGl0bGUnLCBlID0+IHtcbiAgICAgICAgICAgIGlmIChcbiAgICAgICAgICAgICAgICAhJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAucGFyZW50KClcbiAgICAgICAgICAgICAgICAgICAgLmhhc0NsYXNzKCdqc0NybUFkZFNlcnZpY2VzVG9DYXRlZ29yeUZvckNhcmQnKVxuICAgICAgICAgICAgKSB7XG4gICAgICAgICAgICAgICAgaWYgKHRoaXMucnVuKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMucnVuID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuX3RvZ2dsZShlKTtcblxuICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMucnVuID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgfSwgNTAwKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgICQod2luZG93KS5vbigncmVzaXplJywgKCkgPT4ge1xuICAgICAgICAgICAgdGhpcy5fc2FtZUluaXQoKTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgX3RvZ2dsZShlKSB7XG4gICAgICAgIGxldCBtYWluU2NvcGUgPSB0aGlzO1xuICAgICAgICBsZXQgJHRhcmdldCA9ICQoZS50YXJnZXQpO1xuICAgICAgICBsZXQgJHBhcmVudCA9ICR0YXJnZXQuY2xvc2VzdCgnLmpzLWJiLWFjY29yZGVvbicpO1xuICAgICAgICBsZXQgJGl0ZW0gPSAkdGFyZ2V0LnBhcmVudCgnLmJiLWFjY29yZGVvbl9faXRlbScpO1xuICAgICAgICBsZXQgT1BFTl9DTEFTUyA9ICdpcy1vcGVuJztcblxuICAgICAgICBpZiAoJHBhcmVudC5kYXRhKCdhY2NvcmRlb24nKSA9PT0gJ2NvbGxhcHNlJykge1xuICAgICAgICAgICAgaWYgKCRpdGVtLmhhc0NsYXNzKE9QRU5fQ0xBU1MpKSB7XG4gICAgICAgICAgICAgICAgJGl0ZW1cbiAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKE9QRU5fQ0xBU1MpXG4gICAgICAgICAgICAgICAgICAgIC5maW5kKCcuYmItYWNjb3JkZW9uX19jb250ZW50JylcbiAgICAgICAgICAgICAgICAgICAgLnNsaWRlVXAoKTtcbiAgICAgICAgICAgICAgICAvLyBjb25zb2xlLmxvZygnLS0tIHJ1bicsIG1haW5TY29wZS5ydW4pO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkcGFyZW50XG4gICAgICAgICAgICAgICAgICAgIC5maW5kKCcuYmItYWNjb3JkZW9uX19pdGVtJylcbiAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKE9QRU5fQ0xBU1MpXG4gICAgICAgICAgICAgICAgICAgIC5maW5kKCcuYmItYWNjb3JkZW9uX19jb250ZW50JylcbiAgICAgICAgICAgICAgICAgICAgLnNsaWRlVXAoKTtcbiAgICAgICAgICAgICAgICAkaXRlbVxuICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoT1BFTl9DTEFTUylcbiAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy5iYi1hY2NvcmRlb25fX2NvbnRlbnQnKVxuICAgICAgICAgICAgICAgICAgICAuc2xpZGVEb3duKCk7XG5cbiAgICAgICAgICAgICAgICAvLyBtYWluU2NvcGUucnVuID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAvLyBjb25zb2xlLmxvZygnLS0tIHJ1bicsIG1haW5TY29wZS5ydW4pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgaWYgKCRpdGVtLmhhc0NsYXNzKE9QRU5fQ0xBU1MpKSB7XG4gICAgICAgICAgICAgICAgJGl0ZW1cbiAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKE9QRU5fQ0xBU1MpXG4gICAgICAgICAgICAgICAgICAgIC5maW5kKCcuYmItYWNjb3JkZW9uX19jb250ZW50JylcbiAgICAgICAgICAgICAgICAgICAgLnNsaWRlVXAoKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJGl0ZW1cbiAgICAgICAgICAgICAgICAgICAgLmFkZENsYXNzKE9QRU5fQ0xBU1MpXG4gICAgICAgICAgICAgICAgICAgIC5maW5kKCcuYmItYWNjb3JkZW9uX19jb250ZW50JylcbiAgICAgICAgICAgICAgICAgICAgLnNsaWRlRG93bigpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgIH1cblxuICAgIGRlc3Ryb3koKSB7XG4gICAgICAgIGxldCBhY2NvcmQgPSAkKGRvY3VtZW50KS5maW5kKCdbZGF0YS1hY2NvcmRlb25dJyk7XG4gICAgICAgIGFjY29yZC5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgbGV0ICR0aGlzID0gJCh0aGlzKTtcbiAgICAgICAgICAgICR0aGlzLnJlbW92ZUNsYXNzKCdiYi1hY2NvcmRlb24gYmItYWNjb3JkZW9uLS1jdXN0b20nKTtcbiAgICAgICAgICAgICR0aGlzXG4gICAgICAgICAgICAgICAgLmZpbmQoJ1tkYXRhLWFjY29yZGVvbi1pdGVtXScpXG4gICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCdpcy1vcGVuJylcbiAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ2JiLWFjY29yZGVvbl9faXRlbScpO1xuICAgICAgICAgICAgJHRoaXNcbiAgICAgICAgICAgICAgICAuZmluZCgnW2RhdGEtYWNjb3JkZW9uLXRpdGxlXScpXG4gICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCdiYi1hY2NvcmRlb25fX3RpdGxlJylcbiAgICAgICAgICAgICAgICAuZmluZCgnLmljb24nKVxuICAgICAgICAgICAgICAgIC5yZW1vdmUoKTtcbiAgICAgICAgICAgICR0aGlzXG4gICAgICAgICAgICAgICAgLmZpbmQoJ1tkYXRhLWFjY29yZGVvbi1jb250ZW50XScpXG4gICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCdiYi1hY2NvcmRlb25fX2NvbnRlbnQnKVxuICAgICAgICAgICAgICAgIC5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgICAgICB9KTtcbiAgICB9XG59XG5cbi8qIGh0bWwgZXhhbXBsZVxuPGxhYmVsIGNsYXNzPVwiYmItY2hlY2tib3gganMtYmItY2hlY2tib3hcIj5cbiAgICA8aW5wdXQgdHlwZT1cImNoZWNrYm94XCI+XG4gICAgPHNwYW4gY2xhc3M9XCJiYi1jaGVja2JveF9fYm94XCI+PC9zcGFuPlxuICAgIDxzcGFuIGNsYXNzPVwiYmItY2hlY2tib3hfX3RpdGxlXCI+V2hhdHNBcHA8L3NwYW4+XG48L2xhYmVsPlxuKi9cblxuY2xhc3MgQ2hlY2tCb3gge1xuICAgIGNvbnN0cnVjdG9yKGFyZ3MpIHtcbiAgICAgICAgdGhpcy5zZWxlY3RvciA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoYXJncy5zZWxlY3Rvcik7XG5cbiAgICAgICAgaWYgKHR5cGVvZiB0aGlzLnNlbGVjdG9yID09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICAvLyB0aGlzLmluaXQoKTtcbiAgICAgICAgdGhpcy5jaGVja1N0YXR1cygpO1xuICAgIH1cblxuICAgIGluaXQoKSB7XG4gICAgICAgIGxldCBjaGVja2JveCA9IHRoaXMuc2VsZWN0b3I7XG4gICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgY2hlY2tib3gubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgICAgIGNoZWNrYm94W2ldLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgIGxldCBlbGVtID0gdGhpcztcbiAgICAgICAgICAgICAgICBsZXQgZWxlbWVudFRvZ2dsZSA9IGVsZW0ucXVlcnlTZWxlY3RvcignaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJyk7XG4gICAgICAgICAgICAgICAgaWYgKGVsZW1lbnRUb2dnbGUuY2hlY2tlZCkge1xuICAgICAgICAgICAgICAgICAgICBlbGVtLmNsYXNzTGlzdC5yZW1vdmUoJ2lzLWNoZWNrZWQnKTtcbiAgICAgICAgICAgICAgICAgICAgZWxlbWVudFRvZ2dsZS5jaGVja2VkID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgIGVsZW1lbnRUb2dnbGUucmVtb3ZlQXR0cmlidXRlKCdjaGVja2VkJyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgZWxlbS5jbGFzc0xpc3QuYWRkKCdpcy1jaGVja2VkJyk7XG4gICAgICAgICAgICAgICAgICAgIGVsZW1lbnRUb2dnbGUuY2hlY2tlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIGVsZW1lbnRUb2dnbGUuc2V0QXR0cmlidXRlKCdjaGVja2VkJywgJ2NoZWNrZWQnKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBjaGVja1N0YXR1cygpIHtcbiAgICAgICAgbGV0IGNoZWNrID0gW107XG4gICAgICAgIGxldCBub0NoZWNrID0gW107XG4gICAgICAgIGxldCB0b2dDaGVjayA9IFtdO1xuICAgICAgICBsZXQgdG9nTm9DaGVjayA9IFtdO1xuICAgICAgICBsZXQgY2hlY2tib3ggPSB0aGlzLnNlbGVjdG9yO1xuXG4gICAgICAgIGxldCBpO1xuICAgICAgICBsZXQgbGVuO1xuXG4gICAgICAgIGZvciAoaSA9IDAsIGxlbiA9IGNoZWNrYm94Lmxlbmd0aDsgaSA8IGxlbjsgaSsrKSB7XG4gICAgICAgICAgICBsZXQgZWxlbSA9IGNoZWNrYm94W2ldO1xuICAgICAgICAgICAgbGV0IGVsZW1lbnRUb2dnbGUgPSBlbGVtLnF1ZXJ5U2VsZWN0b3IoJ2lucHV0W3R5cGU9XCJjaGVja2JveFwiXScpO1xuICAgICAgICAgICAgaWYgKHR5cGVvZiBlbGVtZW50VG9nZ2xlID09ICdudWxsJykge1xuICAgICAgICAgICAgICAgIGlmICghZWxlbWVudFRvZ2dsZS5jaGVja2VkKSB7XG4gICAgICAgICAgICAgICAgICAgIG5vQ2hlY2sucHVzaChlbGVtKTtcbiAgICAgICAgICAgICAgICAgICAgdG9nTm9DaGVjay5wdXNoKGVsZW1lbnRUb2dnbGUpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGNoZWNrLnB1c2goZWxlbSk7XG4gICAgICAgICAgICAgICAgICAgIHRvZ0NoZWNrLnB1c2goZWxlbWVudFRvZ2dsZSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICAgIGZvciAoaSA9IDAsIGxlbiA9IGNoZWNrLmxlbmd0aDsgaSA8IGxlbjsgaSsrKSB7XG4gICAgICAgICAgICBjaGVja1tpXS5jbGFzc0xpc3QuYWRkKCdpcy1jaGVja2VkJyk7XG4gICAgICAgIH1cblxuICAgICAgICBmb3IgKGkgPSAwLCBsZW4gPSBub0NoZWNrLmxlbmd0aDsgaSA8IGxlbjsgaSsrKSB7XG4gICAgICAgICAgICBub0NoZWNrW2ldLmNsYXNzTGlzdC5yZW1vdmUoJ2lzLWNoZWNrZWQnKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZvciAoaSA9IDAsIGxlbiA9IHRvZ05vQ2hlY2subGVuZ3RoOyBpIDwgbGVuOyBpKyspIHtcbiAgICAgICAgICAgIHRvZ05vQ2hlY2tbaV0ucmVtb3ZlQXR0cmlidXRlKCdjaGVja2VkJyk7XG4gICAgICAgICAgICB0b2dOb0NoZWNrW2ldLmNoZWNrZWQgPSBmYWxzZTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZvciAoaSA9IDAsIGxlbiA9IHRvZ0NoZWNrLmxlbmd0aDsgaSA8IGxlbjsgaSsrKSB7XG4gICAgICAgICAgICB0b2dDaGVja1tpXS5zZXRBdHRyaWJ1dGUoJ2NoZWNrZWQnLCAnY2hlY2tlZCcpO1xuICAgICAgICAgICAgdG9nQ2hlY2tbaV0uY2hlY2tlZCA9IHRydWU7XG4gICAgICAgIH1cbiAgICB9XG59XG5cbi8vIGh0bWwgZXhhbXBsZVxuLy8gPGxhYmVsIGNsYXNzPVwiYmItY2hlY2tib3ggYmItY2hlY2tib3gtLXJhZGlvIGpzLWJiLXJhZGlvXCI+XG4vLyAgICAgPGlucHV0IG5hbWU9XCJyb2xlXCIgdHlwZT1cInJhZGlvXCI+XG4vLyAgICAgPHNwYW4gY2xhc3M9XCJiYi1jaGVja2JveF9fYm94XCI+PC9zcGFuPlxuLy8gICAgIDxzcGFuIGNsYXNzPVwiYmItY2hlY2tib3hfX3RpdGxlXCI+0KHQsNC70L7QvTwvc3Bhbj5cbi8vIDwvbGFiZWw+XG5cbmNsYXNzIFJhZGlvIHtcbiAgICBjb25zdHJ1Y3RvcihhcmdzKSB7XG4gICAgICAgIHRoaXMuc2VsZWN0b3IgPSBhcmdzLnNlbGVjdG9yO1xuXG4gICAgICAgIGlmICh0eXBlb2YgdGhpcy5zZWxlY3RvciA9PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5pbml0KCk7XG4gICAgfVxuXG4gICAgaW5pdCgpIHtcbiAgICAgICAgbGV0IG1haW5TY29wZSA9IHRoaXM7XG4gICAgICAgICQodGhpcy5zZWxlY3RvcikuY2xpY2soZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgICAgIGxldCBlbGVtZW50ID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICBlbGVtZW50VG9nZ2xlID1cbiAgICAgICAgICAgICAgICAgICAgZWxlbWVudC5maW5kKCdpbnB1dFt0eXBlPVwicmFkaW9cIl0nKSB8fCBlbGVtZW50LmZpbmQoJy5iYi1jaGVja2JveF9fdG9nZ2xlJyk7XG4gICAgICAgICAgICBsZXQgZWxlbWVudFRvZ2dsZU5hbWUgPSBlbGVtZW50VG9nZ2xlLmF0dHIoJ25hbWUnKTtcbiAgICAgICAgICAgIGxldCBhbGxFbGVtZW50cyA9ICQobWFpblNjb3BlLnNlbGVjdG9yLCAnW25hbWU9XCInICsgZWxlbWVudFRvZ2dsZU5hbWUgKyAnXCJdJylcbiAgICAgICAgICAgICAgICAucHJldk9iamVjdDtcbiAgICAgICAgICAgIGZvciAobGV0IGEgPSAwOyBhIDwgYWxsRWxlbWVudHMubGVuZ3RoOyBhKyspIHtcbiAgICAgICAgICAgICAgICBpZiAoYWxsRWxlbWVudHNbYV0gIT0gZWxlbWVudFRvZ2dsZVswXSkge1xuICAgICAgICAgICAgICAgICAgICBsZXQgb3RoZXJSYWRpbyA9ICQoXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbWFpblNjb3BlLl9nZXRDbGlja0VsZW1lbnQoXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIG1haW5TY29wZS5zZWxlY3Rvci5zcGxpdCgnLicpWzFdLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBhbGxFbGVtZW50c1thXVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIClcbiAgICAgICAgICAgICAgICAgICAgICAgICksXG4gICAgICAgICAgICAgICAgICAgICAgICBvdGhlclJhZGlvVG9nZ2xlID0gb3RoZXJSYWRpby5maW5kKCdpbnB1dFt0eXBlPVwicmFkaW9cIl0nKTtcbiAgICAgICAgICAgICAgICAgICAgb3RoZXJSYWRpb1RvZ2dsZS5yZW1vdmVBdHRyKCdjaGVja2VkJyk7XG4gICAgICAgICAgICAgICAgICAgIG90aGVyUmFkaW9Ub2dnbGUucHJvcCgnY2hlY2tlZCcsIGZhbHNlKS50cmlnZ2VyKCdjaGFuZ2UnKTtcbiAgICAgICAgICAgICAgICAgICAgb3RoZXJSYWRpby5yZW1vdmVDbGFzcygnaXMtY2hlY2tlZCcpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmICghZWxlbWVudFRvZ2dsZS5wcm9wKCdjaGVja2VkJykpIHtcbiAgICAgICAgICAgICAgICBlbGVtZW50VG9nZ2xlLmF0dHIoJ2NoZWNrZWQnLCAnY2hlY2tlZCcpO1xuICAgICAgICAgICAgICAgIGVsZW1lbnRUb2dnbGUucHJvcCgnY2hlY2tlZCcsIHRydWUpLnRyaWdnZXIoJ2NoYW5nZScpO1xuICAgICAgICAgICAgICAgIGVsZW1lbnQuYWRkQ2xhc3MoJ2lzLWNoZWNrZWQnKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgX2dldENsaWNrRWxlbWVudChlbGVtZW50Q2xhc3MsIG5ld1RhcmdldCkge1xuICAgICAgICBsZXQgdGFyZ2V0ID0gbmV3VGFyZ2V0ICE9IHVuZGVmaW5lZCA/IG5ld1RhcmdldCA6IGV2ZW50LnRhcmdldCxcbiAgICAgICAgICAgIGJvZHkgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdib2R5Jyk7XG4gICAgICAgIHdoaWxlICghdGFyZ2V0LmNsYXNzTGlzdC5jb250YWlucyhlbGVtZW50Q2xhc3MpICYmIHRhcmdldCAhPSBib2R5KSB7XG4gICAgICAgICAgICB0YXJnZXQgPSB0YXJnZXQucGFyZW50Tm9kZTtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gdGFyZ2V0ID09IGJvZHkgPyB1bmRlZmluZWQgOiB0YXJnZXQ7XG4gICAgfVxufVxuXG4vKiBodG1sIGV4YW1wbGUgUHVzaFVwXG48YnV0dG9uXG5cdGRhdGEtcHVzaFxuXHRkYXRhLXB1c2gtZGVsYXk9XCIyNTAwXCJcblx0ZGF0YS1wdXNoLW1lc3NhZ2UtZXJyb3I9XCJ7eyBtZXNzYWdlRXJyb3IgfX1cIlxuXHRkYXRhLXB1c2gtbWVzc2FnZS1zdWNjZXNzPVwie3sgbWVzc2FnZVN1Y2Nlc3MgfX1cIlxuXHRkYXRhLXB1c2gtc3RhdHVzPVwie3sgbWVzc2FnZVN0YXR1cyB8IGVzY2FwZSB9fVwiXG4+XG48L2J1dHRvbj5cbiovXG5cbmZ1bmN0aW9uIHB1c2hVcChvcHRpb25zKSB7XG4gICAgdmFyIHRleHQgPSBvcHRpb25zLnRleHQgfHwgJ9CS0LDQvCDQvdC+0LLQsNGPINC30LDRj9Cy0LrQsCc7XG4gICAgdmFyIHN0YXR1cyA9IG9wdGlvbnMuc3RhdHVzIHx8ICdzdWNjZXNzJztcbiAgICB2YXIgdGltZU91dCA9IG9wdGlvbnMudGltZU91dCB8fCAxNTAwO1xuXG4gICAgdmFyICRwdXNoQ29udGFpbmVyID0gJCgnPGRpdj4nKS5hZGRDbGFzcygncHVzaC11cCBwdXNoLXVwLS1jZW50ZXIgcHVzaC11cC0tdHJhbnNwYXJlbnQnKTtcbiAgICB2YXIgJHB1c2hJY29uU3VjY2VzcyA9ICQoXG4gICAgICAgIGA8c3ZnIHhtbG5zPVwiaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmdcIiB4bWxuczp4bGluaz1cImh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmtcIiB4PVwiMHB4XCIgeT1cIjBweFwiXG4gICAgICAgIHdpZHRoPVwiNjExLjk5NHB4XCIgaGVpZ2h0PVwiNjExLjk5NHB4XCIgdmlld0JveD1cIjAgMCA2MTEuOTk0IDYxMS45OTRcIlxuICAgICAgICB4bWw6c3BhY2U9XCJwcmVzZXJ2ZVwiIGNsYXNzPVwicHVzaC11cF9faWNvblwiPlxuICAgICAgICAgICAgPHBhdGggZD1cIk0yNDguMTcyLDQyMy45MThsLTg5LjU0NS04OS41MzRjLTUuNjM3LTUuNjM3LTUuNjM3LTE0Ljc3OCwwLTIwLjQxNmM1LjY0My01LjY0NCwxNC43OC01LjY0NCwyMC40MTcsMGw2OS4xMjgsNjkuMTIyXG4gICAgICAgICAgICAgICAgbDE4NC43OTYtMTg0LjgwMmM1LjY0NC01LjY0MywxNC43OC01LjY0MywyMC40MTcsMGM1LjY0NCw1LjYzNyw1LjY0NCwxNC43OCwwLDIwLjQxN0wyNDguMTcyLDQyMy45MTh6XCIvPlxuICAgICAgICAgICAgICAgIDxwYXRoIGQ9XCJNMzA2LjAzMSw2MTEuOTk0di0xNC40MzhsLTAuMDIyLDE0LjQzOEMxMzcuMjg2LDYxMS45OTQsMC4wMTIsNDc0LjcyNiwwLDMwNi4wMDNDMCwxMzcuMjc0LDEzNy4yNzQsMCwzMDUuOTk3LDBcbiAgICAgICAgICAgICAgICAgICAgYzE2OC43MjksMCwzMDUuOTk3LDEzNy4yNzQsMzA1Ljk5NywzMDUuOTk3QzYxMiw0NzQuNzI2LDQ3NC43NDMsNjExLjk5NCwzMDYuMDMxLDYxMS45OTR6IE0zMDUuOTk3LDI4Ljg3OFxuICAgICAgICAgICAgICAgICAgICBjLTE1Mi44MDUsMC0yNzcuMTE5LDEyNC4zMTQtMjc3LjExOSwyNzcuMTE5QzI4Ljg5LDQ1OC43OTYsMTUzLjIwOSw1ODMuMTEsMzA2LjAwOSw1ODMuMTFoMC4wMjJcbiAgICAgICAgICAgICAgICAgICAgYzE1Mi43ODgsMCwyNzcuMDkxLTEyNC4zMTQsMjc3LjA5MS0yNzcuMTEzQzU4My4xMjIsMTUzLjE5Miw0NTguODAyLDI4Ljg3OCwzMDUuOTk3LDI4Ljg3OHpcIi8+XG4gICAgICAgIDwvc3ZnPmBcbiAgICApO1xuXG4gICAgdmFyICRwdXNoSWNvbkVycm9yID0gJChcbiAgICAgICAgYDxzdmcgeG1sbnM9XCJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2Z1wiIHhtbG5zOnhsaW5rPVwiaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGlua1wiIHg9XCIwcHhcIiB5PVwiMHB4XCJcbiAgICAgICAgICAgIHZpZXdCb3g9XCIwIDAgNzguNTYxIDc4LjU2MVwiIHhtbDpzcGFjZT1cInByZXNlcnZlXCIgY2xhc3M9XCJwdXNoLXVwX19pY29uXCI+XG4gICAgICAgICAgICA8Y2lyY2xlIGN4PVwiMzkuMjhcIiBjeT1cIjU3Ljc3MlwiIHI9XCIzLjYzMlwiLz5cbiAgICAgICAgICAgIDxwYXRoIGQ9XCJNMzguNzkyLDQ4LjM0N2MxLjEwNCwwLDItMC44OTYsMi0ydi0xOWMwLTEuMTA0LTAuODk2LTItMi0ycy0yLDAuODk2LTIsMnYxOUMzNi43OTIsNDcuNDUxLDM3LjY4OCw0OC4zNDcsMzguNzkyLDQ4LjM0N3pcbiAgICAgICAgICAgICAgICBcIi8+XG4gICAgICAgICAgICA8cGF0aCBkPVwiTTQ2LjU3LDExLjU0MmwtMC4wOTEtMC4xNDFjLTEuODUyLTIuODU0LTMuNzY2LTUuODA2LTcuMTk5LTUuODA2Yy0zLjU3OCwwLTUuNDUsMi45OTQtNy4yNiw1Ljg5MVxuICAgICAgICAgICAgICAgIGMtMC4wMDksMC4wMTQtMC4wNjUsMC4xMDQtMC4wNzQsMC4xMTlMMC4yNzgsNjUuMjY2QzAuMDk2LDY1LjU3NCwwLDY1LjczNSwwLDY2LjA5MmMwLDMuODk2LDMuMTM1LDYuODc0LDYuOTg4LDYuODc0aDY0LjU4NVxuICAgICAgICAgICAgICAgIGMzLjg1NCwwLDYuOTg4LTIuOTc5LDYuOTg4LTYuODc0YzAtMC4zNTctMC4wOTYtMC42MTQtMC4yNzctMC45MjFMNDYuNTcsMTEuNTQyeiBNNzEuNTczLDY4Ljk2Nkg2Ljk4OFxuICAgICAgICAgICAgICAgIGMtMS40NjEsMC0yLjcxNy0wLjk1MS0yLjk1LTIuMzk0bDMxLjM3NC01My4wNjFjMS41NTQtMi40ODcsMi41NzItMy45NjMsMy44NjgtMy45NjNjMS4yNjEsMCwyLjQ1NywxLjg3LDMuODQzLDQuMDA2XG4gICAgICAgICAgICAgICAgbDMxLjM5OSw1My4wMDdDNzQuMjksNjguMDAzLDczLjAzNCw2OC45NjYsNzEuNTczLDY4Ljk2NnpcIi8+XG4gICAgICAgIDwvc3ZnPlxuYFxuICAgICk7XG5cbiAgICAkcHVzaENvbnRhaW5lci50ZXh0KHRleHQpLmFwcGVuZFRvKCQoJ2JvZHknKSk7XG5cbiAgICBpZiAoc3RhdHVzID09PSAnZXJyb3InKSB7XG4gICAgICAgICRwdXNoQ29udGFpbmVyLmFkZENsYXNzKCdpcy1lcnJvcicpO1xuICAgICAgICAkcHVzaEljb25FcnJvci5wcmVwZW5kVG8oJHB1c2hDb250YWluZXIpO1xuICAgIH0gZWxzZSB7XG4gICAgICAgICRwdXNoQ29udGFpbmVyLmFkZENsYXNzKCdpcy1zdWNjZXNzJyk7XG4gICAgICAgICRwdXNoSWNvblN1Y2Nlc3MucHJlcGVuZFRvKCRwdXNoQ29udGFpbmVyKTtcbiAgICB9XG5cbiAgICBfcG9zaFBvcygpO1xuXG4gICAgd2luZG93LnJlcXVlc3RBbmltYXRpb25GcmFtZShmdW5jdGlvbigpIHtcbiAgICAgICAgJHB1c2hDb250YWluZXIuYWRkQ2xhc3MoJ2lzLWFjdGl2ZScpO1xuICAgIH0pO1xuXG4gICAgc2V0VGltZW91dChmdW5jdGlvbigpIHtcbiAgICAgICAgJHB1c2hDb250YWluZXIucmVtb3ZlQ2xhc3MoJ2lzLWFjdGl2ZScpO1xuICAgICAgICBfcG9zaFBvcygpO1xuICAgIH0sIHRpbWVPdXQpO1xuXG4gICAgc2V0VGltZW91dChmdW5jdGlvbigpIHtcbiAgICAgICAgJHB1c2hDb250YWluZXIucmVtb3ZlKCk7XG4gICAgICAgIF9wb3NoUG9zKCk7XG4gICAgfSwgdGltZU91dCArIDUwMCk7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnLmpzLXB1c2gtdXAtLWNsb3NlJywgZnVuY3Rpb24oKSB7XG4gICAgICAgIGxldCAkcGFyZW50ID0gJCh0aGlzKS5jbG9zZXN0KCcucHVzaC11cCcpO1xuICAgICAgICAkcGFyZW50LnJlbW92ZUNsYXNzKCdpcy1hY3RpdmUnKTtcbiAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICRwYXJlbnQucmVtb3ZlKCk7XG4gICAgICAgIH0sIDMwMCk7XG4gICAgICAgIF9wb3NoUG9zKCk7XG4gICAgfSk7XG5cbiAgICBmdW5jdGlvbiBfcG9zaFBvcygpIHtcbiAgICAgICAgJCgnLnB1c2gtdXAnKS5lYWNoKGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgIGxldCBoZWlnaHQgPSAkKCcucHVzaC11cCcpLm91dGVySGVpZ2h0KHRydWUpO1xuICAgICAgICAgICAgJCh0aGlzKS5jc3MoJ3RvcCcsIGhlaWdodCAqIGUgKyAxMCArIGUpO1xuICAgICAgICB9KTtcbiAgICB9XG59XG5cbmNsYXNzIFZpZXdlciB7XG4gICAgY29uc3RydWN0b3IoYXJncykge1xuICAgICAgICB0aGlzLnNlbGVjdG9yID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbChhcmdzLnNlbGVjdG9yKTtcbiAgICB9XG5cbiAgICBpbml0KCkge1xuICAgICAgICBpZiAoIUVsZW1lbnQucHJvdG90eXBlLnJlbW92ZSkge1xuICAgICAgICAgICAgRWxlbWVudC5wcm90b3R5cGUucmVtb3ZlID0gZnVuY3Rpb24gcmVtb3ZlKCkge1xuICAgICAgICAgICAgICAgIGlmICh0aGlzLnBhcmVudE5vZGUpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5wYXJlbnROb2RlLnJlbW92ZUNoaWxkKHRoaXMpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH07XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5oaWRlKCk7XG4gICAgICAgIHRoaXMuYnRuSGVybWl0KCk7XG4gICAgfVxuXG4gICAgaGlkZSgpIHtcbiAgICAgICAgbGV0IGk7XG4gICAgICAgIGxldCBsO1xuICAgICAgICBsZXQgcjtcbiAgICAgICAgbGV0IGxlbjtcbiAgICAgICAgbGV0IGl0ZW07XG4gICAgICAgIGxldCBzdGFydFZpZXdCbG9jaztcbiAgICAgICAgbGV0IGVsZW1zO1xuICAgICAgICBsZXQgYnRuO1xuICAgICAgICBsZXQgbmV3RWxlbXMgPSBbXTtcbiAgICAgICAgbGV0IGFycmF5ID0gdGhpcy5zZWxlY3RvcjtcbiAgICAgICAgbGV0IHQ7XG5cbiAgICAgICAgZm9yIChpID0gMCwgbGVuID0gYXJyYXkubGVuZ3RoOyBpIDwgbGVuOyBpKyspIHtcbiAgICAgICAgICAgIGl0ZW0gPSBhcnJheVtpXTtcbiAgICAgICAgICAgIGVsZW1zID0gaXRlbS5jaGlsZE5vZGVzO1xuICAgICAgICAgICAgbmV3RWxlbXMubGVuZ3RoID0gMDtcbiAgICAgICAgICAgIGJ0biA9IHVuZGVmaW5lZDtcbiAgICAgICAgICAgIHN0YXJ0Vmlld0Jsb2NrID0gaXRlbS5nZXRBdHRyaWJ1dGUoJ2RhdGEtdmlldy1ibG9jaycpIHx8IDQ7XG5cbiAgICAgICAgICAgIGlmIChcbiAgICAgICAgICAgICAgICBpdGVtLmxhc3RFbGVtZW50Q2hpbGQuY2xhc3NMaXN0LmNvbnRhaW5zKCdqcy12aWV3ZXItYnRuJykgfHxcbiAgICAgICAgICAgICAgICBpdGVtLmxhc3RFbGVtZW50Q2hpbGQuY2xhc3NMaXN0LmNvbnRhaW5zKCdqcy12aWV3ZXItYWxsLWJ0bicpXG4gICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICBidG4gPSBpdGVtLmxhc3RFbGVtZW50Q2hpbGQ7XG4gICAgICAgICAgICAgICAgbCA9IGVsZW1zLmxlbmd0aCAtIDI7XG4gICAgICAgICAgICAgICAgdCA9IHRydWU7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGwgPSBlbGVtcy5sZW5ndGg7XG4gICAgICAgICAgICAgICAgdCA9IGZhbHNlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAobCA+PSBzdGFydFZpZXdCbG9jaykge1xuICAgICAgICAgICAgICAgIGZvciAociA9IDA7IHIgPCBsOyByKyspIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKHIgJiAxKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBuZXdFbGVtcy5wdXNoKGVsZW1zW3JdKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmICh0KSB7XG4gICAgICAgICAgICAgICAgICAgIGZvciAociA9IDAsIGwgPSBuZXdFbGVtcy5sZW5ndGg7IHIgPCBsOyByKyspIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChyICsgMSA+IHN0YXJ0Vmlld0Jsb2NrKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbmV3RWxlbXNbcl0uc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB0aGlzLmV2ZW50cyhidG4pO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChcbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0VsZW1zWzBdLmNsYXNzTGlzdC5jb250YWlucygnanMtdmlld2VyLWJ0bicpIHx8XG4gICAgICAgICAgICAgICAgICAgICAgICBuZXdFbGVtc1swXS5jbGFzc0xpc3QuY29udGFpbnMoJ2pzLXZpZXdlci1hbGwtYnRuJylcbiAgICAgICAgICAgICAgICAgICAgKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBidG4gPSBuZXdFbGVtc1swXTtcbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0VsZW1zLnNsaWNlKDAsIDEpO1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5ldmVudHMoYnRuKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIGZvciAociA9IDAsIGwgPSBuZXdFbGVtcy5sZW5ndGg7IHIgPCBsOyByKyspIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChyICsgMSA+IHN0YXJ0Vmlld0Jsb2NrKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbmV3RWxlbXNbcl0uc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgaXRlbS5yZW1vdmVDaGlsZChidG4pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgZXZlbnRzKGJ0bikge1xuICAgICAgICAvLyDQv9GA0L7QstC10YDQtdGP0LwsINC10YHRgtGMINC70Lgg0YMg0Y3Qu9C10LzQtdC90YLQvtCyINC80LXRgtC+0LQg0YPQtNCw0LvQtdC90LjQtdGPLCDQtdGB0LvQuCDQvdC10YIsINGC0L4g0LTQvtCx0LDQstC70Y/QtdC8INC10LPQvlxuXG4gICAgICAgIGlmIChidG4uY2xhc3NMaXN0LmNvbnRhaW5zKCdqcy12aWV3ZXItYnRuJykpIHtcbiAgICAgICAgICAgIGJ0bi5yZW1vdmVFdmVudExpc3RlbmVyKCdjbGljaycsIHNob3dOZWlnaGJvcnMsIGZhbHNlKTtcbiAgICAgICAgICAgIGJ0bi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHNob3dOZWlnaGJvcnMsIGZhbHNlKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChidG4uY2xhc3NMaXN0LmNvbnRhaW5zKCdqcy12aWV3ZXItYWxsLWJ0bicpKSB7XG4gICAgICAgICAgICBidG4ucmVtb3ZlRXZlbnRMaXN0ZW5lcignY2xpY2snLCBzaG93QWxsLCBmYWxzZSk7XG4gICAgICAgICAgICBidG4uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBzaG93QWxsLCBmYWxzZSk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyDRhNGD0L3QutGG0LjRjyDQv9C+0LrQsNC30LAg0YLQvtC70YzQutC+INGB0L7RgdC10LTQtdC5XG4gICAgICAgIGZ1bmN0aW9uIHNob3dOZWlnaGJvcnMoKSB7XG4gICAgICAgICAgICBsZXQgZWxlbXMgPSB0aGlzLnBhcmVudEVsZW1lbnQuY2hpbGROb2RlcztcbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAwLCBsZW4gPSBlbGVtcy5sZW5ndGg7IGkgPCBsZW47IGkrKykge1xuICAgICAgICAgICAgICAgIGlmIChpICYgMSkge1xuICAgICAgICAgICAgICAgICAgICBlbGVtc1tpXS5yZW1vdmVBdHRyaWJ1dGUoJ3N0eWxlJyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgdGhpcy5yZW1vdmUoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8v0YTRg9C90LrRhtC40Y8g0L/QvtC60LDQt9CwINCy0YHQtdGFINC00L7Rh9C10YDQvdC40YUg0Y3Qu9C10LzQtdC90YLQvtCyXG4gICAgICAgIGZ1bmN0aW9uIHNob3dBbGwoKSB7XG4gICAgICAgICAgICBsZXQgZWxlbXMgPSB0aGlzLnBhcmVudEVsZW1lbnQucXVlcnlTZWxlY3RvckFsbCgnW3N0eWxlPVwiZGlzcGxheTogbm9uZTtcIl0nKTtcbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAwLCBsZW4gPSBlbGVtcy5sZW5ndGg7IGkgPCBsZW47IGkrKykge1xuICAgICAgICAgICAgICAgIGVsZW1zW2ldLnJlbW92ZUF0dHJpYnV0ZSgnc3R5bGUnKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHRoaXMucmVtb3ZlKCk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICAvLyDQstC10YjQsNC10Lwg0YHQvtCx0YvRgtC40LUg0L3QsCDQutC90L7Qv9C60YMsINC10YHQu9C4INC+0L3QsCDQu9C10LbQuNGCINC90LAg0L7QtNC90L7QvCDRg9GA0L7QstC90LUg0YEg0LHQu9C+0LrQvtC8IGpzLXZpZXdlclxuICAgIGJ0bkhlcm1pdCgpIHtcbiAgICAgICAgbGV0IGJ0biA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJy5qcy12aWV3ZXItaGVybWl0LWJ0bicpO1xuICAgICAgICBpZiAoYnRuLmxlbmd0aCkge1xuICAgICAgICAgICAgbGV0IGk7IC8v0YHRh9C10YLRh9C40LpcbiAgICAgICAgICAgIGxldCBsZW4gPSBidG4ubGVuZ3RoO1xuICAgICAgICAgICAgZm9yIChpID0gMDsgaSA8IGxlbjsgaSsrKSB7XG4gICAgICAgICAgICAgICAgYnRuW2ldLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCBlbGVtID0gdGhpcy5wYXJlbnRFbGVtZW50LnF1ZXJ5U2VsZWN0b3IoJy5qcy12aWV3ZXInKS5jaGlsZE5vZGVzO1xuICAgICAgICAgICAgICAgICAgICBsZXQgcjsgLy/RgdGH0LXRgtGH0LjQulxuICAgICAgICAgICAgICAgICAgICBsZXQgbDsgLy/RgdGH0LXRgtGH0LjQulxuICAgICAgICAgICAgICAgICAgICBmb3IgKHIgPSAwLCBsID0gZWxlbS5sZW5ndGg7IHIgPCBsOyByKyspIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChyICYgMSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVsZW1bcl0ucmVtb3ZlQXR0cmlidXRlKCdzdHlsZScpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5yZW1vdmUoKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cbn1cblxuLyogIGh0bWwgZXhhbXBsZVxuICAgIDxkaXYgY2xhc3M9XCJqcy1jb3B5LWJveFwiPlxuICAgICAgICBAY29udGVudFxuICAgICAgICA8YnV0dG9uIGNsYXNzPVwianMtY29weS1ib3gtYnRuXCI+PC9idXR0b24+XG4gICAgPC9kaXY+XG4qL1xuXG5jbGFzcyBDb3B5Qm94IHtcbiAgICBjb25zdHJ1Y3RvcihhcmdzKSB7XG4gICAgICAgIHRoaXMuc2VsZWN0b3IgPSBhcmdzLnNlbGVjdG9yO1xuXG4gICAgICAgIGlmICh0eXBlb2YgdGhpcy5zZWxlY3RvciA9PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5fb25DbGljayA9IHRoaXMuX29uQ2xpY2suYmluZCh0aGlzKTtcbiAgICB9XG5cbiAgICBpbml0KCkge1xuICAgICAgICB0aGlzLl9hZGRFdmVudExpc3RlbmVycygpO1xuICAgIH1cblxuICAgIF9hZGRFdmVudExpc3RlbmVycygpIHtcbiAgICAgICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBlID0+IHtcbiAgICAgICAgICAgIHRoaXMuX29uQ2xpY2soZSk7XG4gICAgICAgIH0pO1xuICAgICAgICBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIGUgPT4ge1xuICAgICAgICAgICAgdGhpcy5fcmVtb3ZlRWxlbWVudChlKTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLy/QmtC+0L/QuNGA0YPQtdC8INGN0LvQtdC80LXQvdGCINC+0YfQuNGJ0LDRjyDQtdCz0L4g0L/QvtC70Y8g0LjQvdC/0YPRgtC+0LIsINGB0LXQu9C10LrRgtC+0LIg0Lgg0LLRgdGC0LDQstC70Y/QtdC8INC60LvQvtC9INCy0YvRiNC1XG4gICAgX29uQ2xpY2soZSkge1xuICAgICAgICBsZXQgJHRhcmdldCA9ICQoZS50YXJnZXQpO1xuXG4gICAgICAgIGlmICgkdGFyZ2V0LmNsb3Nlc3QodGhpcy5zZWxlY3RvciArICctYnRuLS1hZGQnKS5sZW5ndGgpIHtcbiAgICAgICAgICAgIGxldCAkcGFyZW50ID0gJHRhcmdldC5jbG9zZXN0KHRoaXMuc2VsZWN0b3IpO1xuICAgICAgICAgICAgbGV0ICRjbG9uZTtcblxuICAgICAgICAgICAgJGNsb25lID0gJHBhcmVudC5jbG9uZSgpLmFkZENsYXNzKCdpcy1jbG9uZWQnKTtcbiAgICAgICAgICAgICRjbG9uZS5maW5kKCdpbnB1dCwgc2VsZWN0LCB0ZXh0YXJlYScpLnZhbCgnJyk7XG4gICAgICAgICAgICAkY2xvbmUuZmluZCgnW2RhdGEtY29weS1yZW1vdmVdJykucmVtb3ZlKCk7XG4gICAgICAgICAgICAkY2xvbmUuaW5zZXJ0QmVmb3JlKCRwYXJlbnQpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgX3JlbW92ZUVsZW1lbnQoZSkge1xuICAgICAgICBsZXQgJHRhcmdldCA9ICQoZS50YXJnZXQpO1xuXG4gICAgICAgIGlmICgkdGFyZ2V0LmNsb3Nlc3QodGhpcy5zZWxlY3RvciArICctYnRuLS1yZW1vdmUnKS5sZW5ndGgpIHtcbiAgICAgICAgICAgICR0YXJnZXQuY2xvc2VzdCh0aGlzLnNlbGVjdG9yKS5yZW1vdmUoKTtcbiAgICAgICAgfVxuICAgIH1cbn1cblxuLyogPGRpdiBjbGFzcz1cInZpZGVvX193cmFwXCI+XG48ZGl2IGNsYXNzPVwidmlkZW9cIj5cbiAgICA8YSBjbGFzcz1cInZpZGVvX19saW5rXCIgaHJlZj1cImh0dHBzOi8veW91dHUuYmUvdlUzZE5fcWNMTG9cIj5cbiAgICAgICAgPHBpY3R1cmU+XG4gICAgICAgICAgICA8c291cmNlXG4gICAgICAgICAgICAgICAgc3Jjc2V0PVwiaHR0cHM6Ly9pLnl0aW1nLmNvbS92aV93ZWJwL3ZVM2ROX3FjTExvL21heHJlc2RlZmF1bHQud2VicFwiXG4gICAgICAgICAgICAgICAgdHlwZT1cImltYWdlL3dlYnBcIlxuICAgICAgICAgICAgLz4gICAgICAgICAgICA8aW1nXG4gICAgICAgICAgICAgICAgY2xhc3M9XCJ2aWRlb19fbWVkaWFcIlxuICAgICAgICAgICAgICAgIHNyYz1cImh0dHBzOi8vaS55dGltZy5jb20vdmkvdlUzZE5fcWNMTG8vbWF4cmVzZGVmYXVsdC5qcGdcIlxuICAgICAgICAgICAgICAgIGFsdD1cItCS0LXQtNGD0YnQsNGPINCi0LDRgtGM0Y/QvdCwINCh0LrQvtGG0LrQsNGPXCJcbiAgICAgICAgICAgIC8+XG4gICAgICAgIDwvcGljdHVyZT5cbiAgICA8L2E+XG4gICAgPGJ1dHRvblxuICAgICAgICBjbGFzcz1cInZpZGVvX19idXR0b25cIlxuICAgICAgICB0eXBlPVwiYnV0dG9uXCJcbiAgICAgICAgYXJpYS1sYWJlbD1cItCX0LDQv9GD0YHRgtC40YLRjCDQstC40LTQtdC+XCJcbiAgICA+XG4gICAgICAgIDxzdmcgd2lkdGg9XCI2OFwiIGhlaWdodD1cIjQ4XCIgdmlld0JveD1cIjAgMCA2OCA0OFwiPlxuICAgICAgICAgICAgPHBhdGhcbiAgICAgICAgICAgICAgICBjbGFzcz1cInZpZGVvX19idXR0b24tc2hhcGVcIlxuICAgICAgICAgICAgICAgIGQ9XCJNNjYuNTIsNy43NGMtMC43OC0yLjkzLTIuNDktNS40MS01LjQyLTYuMTlDNTUuNzksLjEzLDM0LDAsMzQsMFMxMi4yMSwuMTMsNi45LDEuNTUgQzMuOTcsMi4zMywyLjI3LDQuODEsMS40OCw3Ljc0QzAuMDYsMTMuMDUsMCwyNCwwLDI0czAuMDYsMTAuOTUsMS40OCwxNi4yNmMwLjc4LDIuOTMsMi40OSw1LjQxLDUuNDIsNi4xOSBDMTIuMjEsNDcuODcsMzQsNDgsMzQsNDhzMjEuNzktMC4xMywyNy4xLTEuNTVjMi45My0wLjc4LDQuNjQtMy4yNiw1LjQyLTYuMTlDNjcuOTQsMzQuOTUsNjgsMjQsNjgsMjRTNjcuOTQsMTMuMDUsNjYuNTIsNy43NHpcIlxuICAgICAgICAgICAgPjwvcGF0aD5cbiAgICAgICAgICAgIDxwYXRoIGNsYXNzPVwidmlkZW9fX2J1dHRvbi1pY29uXCIgZD1cIk0gNDUsMjQgMjcsMTQgMjcsMzRcIj48L3BhdGg+XG4gICAgICAgIDwvc3ZnPlxuICAgIDwvYnV0dG9uPlxuPC9kaXY+XG48L2Rpdj4gKi9cblxuLy8gLnZpZGVvIHtcbi8vICAgICBwb3NpdGlvbjogcmVsYXRpdmU7XG4vLyAgICAgd2lkdGg6IDEwMCU7XG4vLyAgICAgaGVpZ2h0OiAwO1xuLy8gICAgIHBhZGRpbmctYm90dG9tOiA1Ni4yNSU7XG4vLyAgICAgYmFja2dyb3VuZC1jb2xvcjogIzAwMDAwMDtcblxuLy8gICAgICYtLWVuYWJsZWQge1xuLy8gICAgICAgICBjdXJzb3I6IHBvaW50ZXI7XG5cbi8vICAgICAgICAgJiAudmlkZW9fX2J1dHRvbiB7XG4vLyAgICAgICAgICAgICBkaXNwbGF5OiBibG9jaztcbi8vICAgICAgICAgfVxuLy8gICAgIH1cblxuLy8gICAgICZfX2xpbmsge1xuLy8gICAgICAgICBwb3NpdGlvbjogYWJzb2x1dGU7XG4vLyAgICAgICAgIHRvcDogMDtcbi8vICAgICAgICAgbGVmdDogMDtcbi8vICAgICAgICAgd2lkdGg6IDEwMCU7XG4vLyAgICAgICAgIGhlaWdodDogMTAwJTtcbi8vICAgICB9XG5cbi8vICAgICAmX19tZWRpYSB7XG4vLyAgICAgICAgIHBvc2l0aW9uOiBhYnNvbHV0ZTtcbi8vICAgICAgICAgdG9wOiAwO1xuLy8gICAgICAgICBsZWZ0OiAwO1xuLy8gICAgICAgICB3aWR0aDogMTAwJTtcbi8vICAgICAgICAgaGVpZ2h0OiAxMDAlO1xuLy8gICAgICAgICBib3JkZXI6IG5vbmU7XG4vLyAgICAgfVxuXG4vLyAgICAgJl9fYnV0dG9uIHtcbi8vICAgICAgICAgcG9zaXRpb246IGFic29sdXRlO1xuLy8gICAgICAgICB0b3A6IDUwJTtcbi8vICAgICAgICAgbGVmdDogNTAlO1xuLy8gICAgICAgICB6LWluZGV4OiAxO1xuLy8gICAgICAgICBkaXNwbGF5OiBub25lO1xuLy8gICAgICAgICBwYWRkaW5nOiAwO1xuLy8gICAgICAgICB3aWR0aDogNjhweDtcbi8vICAgICAgICAgaGVpZ2h0OiA0OHB4O1xuLy8gICAgICAgICBib3JkZXI6IG5vbmU7XG4vLyAgICAgICAgIGJhY2tncm91bmQtY29sb3I6IHRyYW5zcGFyZW50O1xuLy8gICAgICAgICB0cmFuc2Zvcm06IHRyYW5zbGF0ZSgtNTAlLCAtNTAlKTtcbi8vICAgICAgICAgY3Vyc29yOiBwb2ludGVyO1xuXG4vLyAgICAgICAgICY6Zm9jdXMge1xuLy8gICAgICAgICAgICAgb3V0bGluZTogbm9uZTtcbi8vICAgICAgICAgfVxuLy8gICAgIH1cblxuLy8gICAgICZfX2J1dHRvbi1zaGFwZSB7XG4vLyAgICAgICAgIGZpbGw6ICMyMTIxMjE7XG4vLyAgICAgICAgIGZpbGwtb3BhY2l0eTogMC44O1xuLy8gICAgIH1cblxuLy8gICAgICZfX2J1dHRvbi1pY29uIHtcbi8vICAgICAgICAgZmlsbDogI2ZmZmZmZjtcbi8vICAgICB9XG5cbi8vICAgICAmX193cmFwIHtcbi8vICAgICAgICAgbWFyZ2luOiAxMDBweCBhdXRvO1xuLy8gICAgICAgICB3aWR0aDogNTB2dztcbi8vICAgICAgICAgZGlzcGxheTogZmxleDtcbi8vICAgICAgICAganVzdGlmeS1jb250ZW50OiBjZW50ZXI7XG4vLyAgICAgICAgIGFsaWduLWl0ZW1zOiBjZW50ZXI7XG5cbi8vICAgICAgICAgQGluY2x1ZGUgeHMtYmxvY2sge1xuLy8gICAgICAgICAgICAgd2lkdGg6IDkwdnc7XG4vLyAgICAgICAgIH1cbi8vICAgICB9XG5cbi8vICAgICAudmlkZW86aG92ZXIgLnZpZGVvX19idXR0b24tc2hhcGUsXG4vLyAgICAgLnZpZGVvX19idXR0b246Zm9jdXMgLnZpZGVvX19idXR0b24tc2hhcGUge1xuLy8gICAgICAgICBmaWxsOiAjZmYwMDAwO1xuLy8gICAgICAgICBmaWxsLW9wYWNpdHk6IDE7XG4vLyAgICAgfVxuLy8gfVxuXG5jbGFzcyBWaWRlb0xvYWRlciB7XG4gICAgY29uc3RydWN0b3IoKSB7XG4gICAgICAgIHRoaXMudmlkZW9zID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLnZpZGVvJyk7XG4gICAgfVxuXG4gICAgaW5pdCgpIHtcbiAgICAgICAgaWYgKHRoaXMudmlkZW9zLmxlbmd0aCkgdGhpcy5maW5kVmlkZW9zKCk7XG4gICAgfVxuXG4gICAgZmluZFZpZGVvcygpIHtcbiAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCB0aGlzLnZpZGVvcy5sZW5ndGg7IGkrKykge1xuICAgICAgICAgICAgdGhpcy5zZXR1cFZpZGVvKHRoaXMudmlkZW9zW2ldKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIHNldHVwVmlkZW8odmlkZW8pIHtcbiAgICAgICAgbGV0IGxpbmsgPSB2aWRlby5xdWVyeVNlbGVjdG9yKCcudmlkZW9fX2xpbmsnKTtcbiAgICAgICAgbGV0IG1lZGlhID0gdmlkZW8ucXVlcnlTZWxlY3RvcignLnZpZGVvX19tZWRpYScpO1xuICAgICAgICBsZXQgYnV0dG9uID0gdmlkZW8ucXVlcnlTZWxlY3RvcignLnZpZGVvX19idXR0b24nKTtcbiAgICAgICAgbGV0IGlkID0gdGhpcy5wYXJzZU1lZGlhVVJMKG1lZGlhKTtcblxuICAgICAgICB2aWRlby5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsICgpID0+IHtcbiAgICAgICAgICAgIGxldCBpZnJhbWUgPSB0aGlzLmNyZWF0ZUlmcmFtZShpZCk7XG5cbiAgICAgICAgICAgIGxpbmsucmVtb3ZlKCk7XG4gICAgICAgICAgICBidXR0b24ucmVtb3ZlKCk7XG4gICAgICAgICAgICB2aWRlby5hcHBlbmRDaGlsZChpZnJhbWUpO1xuICAgICAgICB9KTtcblxuICAgICAgICBsaW5rLnJlbW92ZUF0dHJpYnV0ZSgnaHJlZicpO1xuICAgICAgICB2aWRlby5jbGFzc0xpc3QuYWRkKCd2aWRlby0tZW5hYmxlZCcpO1xuICAgIH1cblxuICAgIHBhcnNlTWVkaWFVUkwobWVkaWEpIHtcbiAgICAgICAgbGV0IHJlZ2V4cCA9IC9odHRwczpcXC9cXC9pXFwueXRpbWdcXC5jb21cXC92aVxcLyhbYS16QS1aMC05Xy1dKylcXC9tYXhyZXNkZWZhdWx0XFwuanBnL2k7XG4gICAgICAgIGxldCB1cmwgPSBtZWRpYS5zcmM7XG4gICAgICAgIGxldCBtYXRjaCA9IHVybC5tYXRjaChyZWdleHApO1xuXG4gICAgICAgIHJldHVybiBtYXRjaFsxXTtcbiAgICB9XG5cbiAgICBjcmVhdGVJZnJhbWUoaWQpIHtcbiAgICAgICAgbGV0IGlmcmFtZSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2lmcmFtZScpO1xuXG4gICAgICAgIGlmcmFtZS5zZXRBdHRyaWJ1dGUoJ2FsbG93ZnVsbHNjcmVlbicsICcnKTtcbiAgICAgICAgaWZyYW1lLnNldEF0dHJpYnV0ZSgnYWxsb3cnLCAnYXV0b3BsYXknKTtcbiAgICAgICAgaWZyYW1lLnNldEF0dHJpYnV0ZSgnc3JjJywgdGhpcy5nZW5lcmF0ZVVSTChpZCkpO1xuICAgICAgICBpZnJhbWUuY2xhc3NMaXN0LmFkZCgndmlkZW9fX21lZGlhJyk7XG5cbiAgICAgICAgcmV0dXJuIGlmcmFtZTtcbiAgICB9XG5cbiAgICBnZW5lcmF0ZVVSTChpZCkge1xuICAgICAgICBsZXQgcXVlcnkgPSAnP3JlbD0wJnNob3dpbmZvPTAmYXV0b3BsYXk9MSc7XG5cbiAgICAgICAgcmV0dXJuICdodHRwczovL3d3dy55b3V0dWJlLmNvbS9lbWJlZC8nICsgaWQgKyBxdWVyeTtcbiAgICB9XG59XG5cbmNsYXNzIEFkZEZvcm0ge1xuICAgIGNvbnN0cnVjdG9yKGFyZ3MpIHtcbiAgICAgICAgdGhpcy5zZWxlY3RvciA9IGFyZ3Muc2VsZWN0b3I7XG4gICAgICAgIHRoaXMuZm9ybXMgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCcuJyArIHRoaXMuc2VsZWN0b3IpO1xuXG4gICAgICAgIHRoaXMuX29uU3VibWl0ID0gdGhpcy5fb25TdWJtaXQuYmluZCh0aGlzKTtcbiAgICB9XG5cbiAgICBpbml0KCkge1xuICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IHRoaXMuZm9ybXMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgICAgIHRoaXMuX3NldFByb3BzKHRoaXMuZm9ybXNbaV0pO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMuX3JlbW92ZUV2ZW50TGlzdGVuZXIoKTtcbiAgICAgICAgdGhpcy5fYWRkRXZlbnRMaXN0ZW5lcigpO1xuICAgIH1cblxuICAgIF9hZGRFdmVudExpc3RlbmVyKCkge1xuICAgICAgICBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdzdWJtaXQnLCB0aGlzLl9vblN1Ym1pdCk7XG4gICAgfVxuXG4gICAgX3JlbW92ZUV2ZW50TGlzdGVuZXIoKSB7XG4gICAgICAgIGRvY3VtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoJ3N1Ym1pdCcsIHRoaXMuX29uU3VibWl0KTtcbiAgICB9XG5cbiAgICAvL9Ce0LHRgNCw0LHQvtGC0YfQuNC6INGB0L7QsdGL0YLQuNGPINC90LAgU3VibWl0XG4gICAgX29uU3VibWl0KGUpIHtcbiAgICAgICAgY29uc3QgdGFyZ2V0ID0gZS50YXJnZXQ7XG5cbiAgICAgICAgaWYgKHRhcmdldC5jbGFzc0xpc3QuY29udGFpbnModGhpcy5zZWxlY3RvcikpIHtcbiAgICAgICAgICAgIHRoaXMuX3NldFZhbHVlKHRhcmdldCk7XG4gICAgICAgIH1cbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuICAgIH1cblxuICAgIF9zZXRWYWx1ZShlbGVtZW50KSB7XG4gICAgICAgIGNvbnN0IGlucHV0ID0gZWxlbWVudC5xdWVyeVNlbGVjdG9yKCdpbnB1dCcpO1xuICAgICAgICBjb25zdCB2YWx1ZSA9IGlucHV0LnZhbHVlO1xuXG4gICAgICAgIGlmICh2YWx1ZSkge1xuICAgICAgICAgICAgdGhpcy5fYWRkUHJvcChlbGVtZW50LCB2YWx1ZSk7XG4gICAgICAgICAgICBpbnB1dC52YWx1ZSA9ICcnO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgX3NldFByb3BzKGVsZW1lbnQpIHtcbiAgICAgICAgbGV0IGNoZWNrZWQgPSBlbGVtZW50LnByZXZpb3VzRWxlbWVudFNpYmxpbmcuZ2V0QXR0cmlidXRlKFxuICAgICAgICAgICAgJ2RhdGEtY2hlY2tlZCdcbiAgICAgICAgKTtcbiAgICAgICAgaWYgKGNoZWNrZWQgIT0gbnVsbCkge1xuICAgICAgICAgICAgbGV0IGFycmF5UHJvcHMgPSBjaGVja2VkLnNwbGl0KCcsICcpO1xuICAgICAgICAgICAgZm9yIChsZXQgdCA9IDA7IHQgPCBhcnJheVByb3BzLmxlbmd0aDsgdCsrKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5fYWRkUHJvcChlbGVtZW50LCBhcnJheVByb3BzW3RdKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cblxuICAgIF9hZGRQcm9wKGVsZW1lbnQsIHZhbHVlKSB7XG4gICAgICAgIGxldCBwcm9wcyA9IGVsZW1lbnQucHJldmlvdXNFbGVtZW50U2libGluZztcbiAgICAgICAgbGV0IGNoZWNrZWQgPSBwcm9wcy5nZXRBdHRyaWJ1dGUoJ2RhdGEtY2hlY2tlZCcpO1xuICAgICAgICBsZXQgY2hpbGQgPSBwcm9wcy5jaGlsZE5vZGVzO1xuICAgICAgICBsZXQgYXJyYXlQcm9wcztcblxuICAgICAgICBpZiAoY2hlY2tlZCA9PSBudWxsKSB7XG4gICAgICAgICAgICBhcnJheVByb3BzID0gW107XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBhcnJheVByb3BzID0gcHJvcHMuZ2V0QXR0cmlidXRlKCdkYXRhLWNoZWNrZWQnKS5zcGxpdCgnLCAnKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChjaGlsZC5sZW5ndGggPT09IGFycmF5UHJvcHMubGVuZ3RoKSB7XG4gICAgICAgICAgICBsZXQgaW5kZXggPSBhcnJheVByb3BzLmluZGV4T2YodmFsdWUpO1xuXG4gICAgICAgICAgICBpZiAoaW5kZXggPT0gLTEpIHtcbiAgICAgICAgICAgICAgICBhcnJheVByb3BzLnB1c2godmFsdWUpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBwcm9wcy5zZXRBdHRyaWJ1dGUoJ2RhdGEtY2hlY2tlZCcsIGFycmF5UHJvcHMuam9pbignLCAnKSk7XG4gICAgICAgICAgICBwcm9wcy5hcHBlbmRDaGlsZCh0aGlzLl9jcmVhdGVQcm9wKHZhbHVlLCBwcm9wcykpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgbGV0IGFycmF5UHJvcHMgPSBbXTtcbiAgICAgICAgICAgIGxldCBpbmRleCA9IGFycmF5UHJvcHMuaW5kZXhPZih2YWx1ZSk7XG5cbiAgICAgICAgICAgIGlmIChpbmRleCA9PSAtMSkge1xuICAgICAgICAgICAgICAgIGFycmF5UHJvcHMucHVzaCh2YWx1ZSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHByb3BzLnNldEF0dHJpYnV0ZSgnZGF0YS1jaGVja2VkJywgYXJyYXlQcm9wcy5qb2luKCcsICcpKTtcbiAgICAgICAgICAgIHByb3BzLmFwcGVuZENoaWxkKHRoaXMuX2NyZWF0ZVByb3AodmFsdWUsIHByb3BzKSk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBfZGVsZXRlUHJvcChlbGVtZW50LCB2YWx1ZSkge1xuICAgICAgICBsZXQgYXJyYXlQcm9wcyA9IGVsZW1lbnQuZ2V0QXR0cmlidXRlKCdkYXRhLWNoZWNrZWQnKS5zcGxpdCgnLCAnKTtcbiAgICAgICAgbGV0IGluZGV4ID0gYXJyYXlQcm9wcy5pbmRleE9mKHZhbHVlKTtcbiAgICAgICAgaWYgKGluZGV4ICE9IC0xKSB7XG4gICAgICAgICAgICBhcnJheVByb3BzLnNwbGljZShpbmRleCwgMSk7XG4gICAgICAgIH1cbiAgICAgICAgZWxlbWVudC5zZXRBdHRyaWJ1dGUoJ2RhdGEtY2hlY2tlZCcsIGFycmF5UHJvcHMuam9pbignLCAnKSk7XG4gICAgfVxuXG4gICAgX2NyZWF0ZVByb3AodmFsdWUsIGVsZW1lbnQpIHtcbiAgICAgICAgbGV0IGRpdiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICAgICAgICBsZXQgc3BhbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ3NwYW4nKTtcbiAgICAgICAgbGV0IGJ0blJlc2V0ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYnV0dG9uJyk7XG4gICAgICAgIGxldCBjbG9zZUljb24gPSBgPHNwYW4+PHN2ZyBjbGFzcz1cImljb24gaWNvbi1jbG9zZS0tYm9sZCBcIj48dXNlIHhsaW5rOmhyZWY9XCJmaWxlcy9zcHJpdGUuc3ZnI2Nsb3NlLS1ib2xkXCI+PC91c2U+PC9zdmc+PC9zcGFuPmA7XG5cbiAgICAgICAgYnRuUmVzZXQuY2xhc3NMaXN0LmFkZCgnYnRuLXJlc2V0Jyk7XG5cbiAgICAgICAgYnRuUmVzZXQuaW5uZXJIVE1MID0gY2xvc2VJY29uO1xuXG4gICAgICAgIGJ0blJlc2V0LmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgKCkgPT4ge1xuICAgICAgICAgICAgdGhpcy5fZGVsZXRlUHJvcChlbGVtZW50LCB2YWx1ZSk7XG4gICAgICAgICAgICBlbGVtZW50LnJlbW92ZUNoaWxkKGJ0blJlc2V0LnBhcmVudEVsZW1lbnQpO1xuICAgICAgICB9KTtcblxuICAgICAgICBzcGFuLmlubmVyVGV4dCA9IHZhbHVlO1xuXG4gICAgICAgIGRpdi5jbGFzc0xpc3QuYWRkKCdhZGQtZm9ybV9fcHJvcCcpO1xuICAgICAgICBkaXYuc2V0QXR0cmlidXRlKCdkYXRhLWNvcHktcmVtb3ZlJywgJycpO1xuICAgICAgICBkaXYuYXBwZW5kQ2hpbGQoc3Bhbik7XG4gICAgICAgIGRpdi5hcHBlbmRDaGlsZChidG5SZXNldCk7XG5cbiAgICAgICAgcmV0dXJuIGRpdjtcbiAgICB9XG59XG5cbi8qKlxuICogQmFzZS5Db21wb25lbnRzLkJ1dHRvbi5qc1xuICpcbiAqIEBhdXRob3IgQW50b24gVXN0aW5vZmYgPGEuYS51c3Rpbm9mZkBnbWFpbC5jb20+XG4gKi9cblxuQmFzZS5kZWZpbmUoJ0NvbXBvbmVudHMuQnV0dG9uJyk7XG5cbkJhc2UuQ29tcG9uZW50cy5CdXR0b24gPSAoZnVuY3Rpb24oKSB7XG4gICAgZnVuY3Rpb24gX2luaXQoKSB7XG4gICAgICAgIF9pbml0UHVzaFVwKCk7XG4gICAgICAgIF9pbml0RXhwYW5kZWQoKTtcbiAgICAgICAgX2luaXRIb3ZlckFuaW1hdGUoKTtcbiAgICAgICAgX2luaXRGbG9hdGluZygpO1xuICAgICAgICBfaW5pdFN0YXR1c0FuaW1hdGUoKTtcbiAgICAgICAgX2luaXRUb2dnbGUoKTtcbiAgICAgICAgX2luaXRTZWxlY3QoKTtcbiAgICAgICAgX2luaXRHb1RvcCgpO1xuICAgICAgICBfaW5pdEdvVG8oKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdFB1c2hVcCgpIHtcbiAgICAgICAgJGRvY3VtZW50Lm9uKCdjbGljaycsICdbZGF0YS1wdXNoXScsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgbGV0IG1lc3NhZ2VTdWNjZXNzID0gJCh0aGlzKS5hdHRyKCdkYXRhLXB1c2gtbWVzc2FnZS1zdWNjZXNzJyk7XG4gICAgICAgICAgICBsZXQgbWVzc2FnZUVycm9yID0gJCh0aGlzKS5hdHRyKCdkYXRhLXB1c2gtbWVzc2FnZS1lcnJvcicpO1xuICAgICAgICAgICAgbGV0IGRlbGF5ID0gJCh0aGlzKS5hdHRyKCdkYXRhLXB1c2gtZGVsYXknKSB8fCAzMDA7XG4gICAgICAgICAgICBsZXQgdGltZU91dCA9ICQodGhpcykuYXR0cignZGF0YS1wdXNoLXRpbWVvdXQnKSB8fCAxNTAwO1xuICAgICAgICAgICAgbGV0IHN0YXR1cztcblxuICAgICAgICAgICAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAgICAgc3RhdHVzID0gJCh0aGlzKS5hdHRyKCdkYXRhLXB1c2gtc3RhdHVzJykgfHwgJ3N1Y2Nlc3MnO1xuICAgICAgICAgICAgfSwgMTAwKTtcblxuICAgICAgICAgICAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKHN0YXR1cyA9PT0gJ2Vycm9yJykge1xuICAgICAgICAgICAgICAgICAgICBwdXNoVXAoe1xuICAgICAgICAgICAgICAgICAgICAgICAgdGV4dDogbWVzc2FnZUVycm9yLFxuICAgICAgICAgICAgICAgICAgICAgICAgc3RhdHVzOiBzdGF0dXMsXG4gICAgICAgICAgICAgICAgICAgICAgICB0aW1lT3V0OiB0aW1lT3V0LFxuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBwdXNoVXAoe1xuICAgICAgICAgICAgICAgICAgICAgICAgdGV4dDogbWVzc2FnZVN1Y2Nlc3MsXG4gICAgICAgICAgICAgICAgICAgICAgICBzdGF0dXM6IHN0YXR1cyxcbiAgICAgICAgICAgICAgICAgICAgICAgIHRpbWVPdXQ6IHRpbWVPdXQsXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sIGRlbGF5KTtcbiAgICAgICAgfSk7XG4gICAgfVxuICAgIC8vYnRuIGV4cGFuZGVkXG4gICAgZnVuY3Rpb24gX2luaXRFeHBhbmRlZCgpIHtcbiAgICAgICAgQmFzZS5VdGlscy50b2dnbGVDbGFzc0F0QmxvY2tDbGlja091dHNpZGUoXG4gICAgICAgICAgICAnLmpzLWJ0bi1leHBhbmRlZCcsXG4gICAgICAgICAgICAnaXMtYWN0aXZlJ1xuICAgICAgICApO1xuICAgIH1cblxuICAgIC8vYnRuIGFuaW1hdGUgb24gaG92ZXJcbiAgICBmdW5jdGlvbiBfaW5pdEhvdmVyQW5pbWF0ZSgpIHtcbiAgICAgICAgJGRvY3VtZW50XG4gICAgICAgICAgICAub24oJ21vdXNlZW50ZXInLCAnLmpzLWJ0bi1hbmltYXRlJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgIHZhciBwYXJlbnRPZmZzZXQgPSAkKHRoaXMpLm9mZnNldCgpLFxuICAgICAgICAgICAgICAgICAgICByZWxYID0gZS5wYWdlWCAtIHBhcmVudE9mZnNldC5sZWZ0LFxuICAgICAgICAgICAgICAgICAgICByZWxZID0gZS5wYWdlWSAtIHBhcmVudE9mZnNldC50b3A7XG4gICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAuZmluZCgnLmJ1dHRvbi1hbmltYXRlX19ob3ZlcicpXG4gICAgICAgICAgICAgICAgICAgIC5jc3Moe1xuICAgICAgICAgICAgICAgICAgICAgICAgdG9wOiByZWxZLFxuICAgICAgICAgICAgICAgICAgICAgICAgbGVmdDogcmVsWCxcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KVxuICAgICAgICAgICAgLm9uKCdtb3VzZW91dCcsICcuanMtYnRuLWFuaW1hdGUnLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICAgICAgdmFyIHBhcmVudE9mZnNldCA9ICQodGhpcykub2Zmc2V0KCksXG4gICAgICAgICAgICAgICAgICAgIHJlbFggPSBlLnBhZ2VYIC0gcGFyZW50T2Zmc2V0LmxlZnQsXG4gICAgICAgICAgICAgICAgICAgIHJlbFkgPSBlLnBhZ2VZIC0gcGFyZW50T2Zmc2V0LnRvcDtcbiAgICAgICAgICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAgICAgICAgIC5maW5kKCcuYnV0dG9uLWFuaW1hdGVfX2hvdmVyJylcbiAgICAgICAgICAgICAgICAgICAgLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICAgICB0b3A6IHJlbFksXG4gICAgICAgICAgICAgICAgICAgICAgICBsZWZ0OiByZWxYLFxuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8vZmxvYXRpbmcgYnRuIGFuaW1hdGluXG4gICAgZnVuY3Rpb24gX2luaXRGbG9hdGluZygpIHtcbiAgICAgICAgbGV0ICRidG4gPSAkZG9jdW1lbnQuZmluZCgnLmpzLWJ0bi1mbG9hdGluZycpO1xuICAgICAgICBsZXQgcnVuID0gdHJ1ZTtcblxuICAgICAgICBpZiAoISRidG4uZmluZCgnLmJ0bi1mbG9hdGluZ19fbGlzdCcpLmxlbmd0aCkge1xuICAgICAgICAgICAgJGJ0bi5maW5kKCcuYnRuLWZsb2F0aW5nX19saW5rJykuY3NzKCdwb2ludGVyLWV2ZW50cycsICdhdXRvJyk7XG4gICAgICAgIH1cblxuICAgICAgICAvL9Ce0LHRgNCw0LHQvtGC0YfQuNC6INC00L7QsdCw0LLQu9GP0LXRgiDQutC70LDRgdGB0Ysg0LfQsNGC0LXQvCDQvtGC0L/QuNGB0YvQstCw0YLQtdGB0Y8g0L7RgiDRgdC+0LHRi9GC0LjRj1xuICAgICAgICBsZXQgaGVuZGxlciA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnZmEtZW50ZXItYWN0aXZlJylcbiAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2ZhLWxlYXZlLWFjdGl2ZScpO1xuICAgICAgICAgICAgJGJ0bi5vZmYoXG4gICAgICAgICAgICAgICAgJ3RyYW5zaXRpb25lbmQgd2Via2l0VHJhbnNpdGlvbkVuZCBvVHJhbnNpdGlvbkVuZCcsXG4gICAgICAgICAgICAgICAgaGVuZGxlclxuICAgICAgICAgICAgKTtcbiAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoJ2ZhLWxlYXZlLWFjdGl2ZScpO1xuICAgICAgICAgICAgfSwgMTAwMCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy/QkNC90LjQvNCw0YbQuNGPINC30LDQutGA0YvRgtC40Y9cbiAgICAgICAgZnVuY3Rpb24gX3JlbW92ZUFuaW1hdGlvbihlbCkge1xuICAgICAgICAgICAgZWwub24oJ3RyYW5zaXRpb25lbmQgd2Via2l0VHJhbnNpdGlvbkVuZCBvVHJhbnNpdGlvbkVuZCcsIGhlbmRsZXIpO1xuICAgICAgICAgICAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAgICAgZWwucmVtb3ZlQ2xhc3MoJ2ZhLWxlYXZlLWFjdGl2ZScpO1xuICAgICAgICAgICAgfSwgMTAwMCk7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoJCh3aW5kb3cpLndpZHRoKCkgPiA3NjgpIHtcbiAgICAgICAgICAgIGlmICghcnVuKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAkZG9jdW1lbnRcbiAgICAgICAgICAgICAgICAub24oJ21vdXNlZW50ZXInLCAnLmpzLWJ0bi1mbG9hdGluZycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICBydW4gPSBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnZmEtZW50ZXItYWN0aXZlJyk7XG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAub24oJ21vdXNlbGVhdmUnLCAnLmpzLWJ0bi1mbG9hdGluZycsIGhlbmRsZXIpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgJGRvY3VtZW50Lm9uKCdjbGljaycsICcuanMtYnRuLWZsb2F0aW5nJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgaWYgKCQodGhpcykuZmluZCgnLmJ0bi1mbG9hdGluZ19fbGlzdCcpLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2ZhLWVudGVyLWFjdGl2ZScpXG4gICAgICAgICAgICAgICAgICAgICAgICAuY3NzKCd6LWluZGV4JywgMTAwMCk7XG4gICAgICAgICAgICAgICAgICAgICRvdmVybGF5XG4gICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2lzLXZpc2libGUnKVxuICAgICAgICAgICAgICAgICAgICAgICAgLmFkZENsYXNzKCdvdmVybGF5LS1idG4tZmxvYXRpbmcnKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBsZXQgYnRuSWQgPSAkKHRoaXMpXG4gICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnLmJ0bi1mbG9hdGluZ19fbGluaycpXG4gICAgICAgICAgICAgICAgICAgICAgICAubm90KCcubWQtaGlkZScpO1xuICAgICAgICAgICAgICAgICAgICBidG5JZC50cmlnZ2VyKCdjbGljaycpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkZG9jdW1lbnQub24oXG4gICAgICAgICAgICAgICAgJ2NsaWNrJyxcbiAgICAgICAgICAgICAgICAnLmpzLWJ0bi1mbG9hdGluZyAuYnRuLWZsb2F0aW5nX19saW5rJyxcbiAgICAgICAgICAgICAgICBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICAgICAgICAgICRidG4ucmVtb3ZlQ2xhc3MoJ2ZhLWVudGVyLWFjdGl2ZScpLnJlbW92ZUF0dHIoJ3N0eWxlJyk7XG4gICAgICAgICAgICAgICAgICAgIF9yZW1vdmVBbmltYXRpb24oJCh0aGlzKSk7XG4gICAgICAgICAgICAgICAgICAgICRvdmVybGF5LnJlbW92ZUNsYXNzKCdpcy12aXNpYmxlIG92ZXJsYXktLWJ0bi1mbG9hdGluZycpO1xuICAgICAgICAgICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICk7XG5cbiAgICAgICAgICAgIC8v0JrQu9C40Log0LIg0L3QtSDQutC90L7Qv9C60Lgg0YHQutGA0YvQstCw0LXRgiDQvtCy0LXRgNC70LXQuSDQuCDQutC90L7Qv9C60LhcbiAgICAgICAgICAgICRkb2N1bWVudC5vbignY2xpY2sgdG91Y2hzdGFydCcsICcub3ZlcmxheS0tYnRuLWZsb2F0aW5nJywgZnVuY3Rpb24oXG4gICAgICAgICAgICAgICAgZVxuICAgICAgICAgICAgKSB7XG4gICAgICAgICAgICAgICAgJGJ0bi5yZW1vdmVDbGFzcygnZmEtZW50ZXItYWN0aXZlJykuYWRkQ2xhc3MoJ2ZhLWxlYXZlLWFjdGl2ZScpO1xuICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAkb3ZlcmxheVxuICAgICAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCdpcy12aXNpYmxlJylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnb3ZlcmxheS0tYnRuLWZsb2F0aW5nJyk7XG4gICAgICAgICAgICAgICAgfSwgMTAwKTtcblxuICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAkYnRuLnJlbW92ZUNsYXNzKCdmYS1sZWF2ZS1hY3RpdmUnKTtcbiAgICAgICAgICAgICAgICB9LCAxNTAwKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgLy/QldGB0LvQuCDRgdGB0YvQu9C60LAg0L7RgtC60YDRi9Cy0LDQtdGCINC80L7QtNCw0LvQutGDLCDRgtC+INC/0L4g0L7RgtC60YDRi9GC0LjRjiDQvNC+0LTQsNC70LrQuCDRgdC60YDRi9Cy0LDQtdGCINC60L3QvtC/0LrQuFxuICAgICAgICAkKCcubW9kYWwnKS5vbignc2hvdy5icy5tb2RhbCcsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgJGJ0bi5yZW1vdmVDbGFzcygnZmEtZW50ZXItYWN0aXZlJykuYWRkQ2xhc3MoJ2ZhLWxlYXZlLWFjdGl2ZScpO1xuICAgICAgICAgICAgJG92ZXJsYXkucmVtb3ZlQXR0cignc3R5bGUnKTtcbiAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgICAgICRidG4ucmVtb3ZlQ2xhc3MoJ2ZhLWxlYXZlLWFjdGl2ZScpO1xuICAgICAgICAgICAgfSwgMTUwMCk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8vYnRuIHN0YXR1cyBhbmltYXRlXG4gICAgZnVuY3Rpb24gX2luaXRTdGF0dXNBbmltYXRlKCkge1xuICAgICAgICBsZXQgcnVuID0gdHJ1ZTtcbiAgICAgICAgJGRvY3VtZW50Lm9uKCdjbGljaycsICcuYnRuLWFuaW1hdGUnLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICBpZiAocnVuKSB7XG4gICAgICAgICAgICAgICAgcnVuID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnaXMtYW5pbWF0ZSBpcy1yZWFkeScpO1xuXG4gICAgICAgICAgICAgICAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoJ2lzLWFuaW1hdGUgaXMtcmVhZHknKTtcbiAgICAgICAgICAgICAgICAgICAgcnVuID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICB9LCAyNTAwKTtcbiAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnaXMtcmVhZHknKTtcbiAgICAgICAgICAgICAgICB9LCA1MDAwKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdFRvZ2dsZSgpIHtcbiAgICAgICAgbGV0ICRidG5Ub2dnbGUgPSAkZG9jdW1lbnQuZmluZCgnLmpzLXZpZXctdG9nZ2xlJyk7XG5cbiAgICAgICAgJGJ0blRvZ2dsZS5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICBsZXQgJHRhcmdldCA9ICQoZS50YXJnZXQpO1xuXG4gICAgICAgICAgICBpZiAoXG4gICAgICAgICAgICAgICAgJHRhcmdldC5pcyhcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAgICAgLmNoaWxkcmVuKClcbiAgICAgICAgICAgICAgICAgICAgICAgIC5sYXN0KClcbiAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdpcy1hY3RpdmUnKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmVDbGFzcygnaXMtYWN0aXZlJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9pbml0U2VsZWN0KCkge1xuICAgICAgICBsZXQgJGJ0biA9ICQoZG9jdW1lbnQpLmZpbmQoJy5qcy1idG4tc2VsZWN0Jyk7XG4gICAgICAgIGxldCBBQ1RJVkVfQ0xBU1MgPSAnaXMtYWN0aXZlJztcbiAgICAgICAgJGJ0bi5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICBpZiAoJCh0aGlzKS5maW5kKCcuYnRuLXNlbGVjdF9fbGlzdCcpLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgIGlmICgkKHRoaXMpLmhhc0NsYXNzKEFDVElWRV9DTEFTUykpIHtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmVDbGFzcyhBQ1RJVkVfQ0xBU1MpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoQUNUSVZFX0NMQVNTKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8vYnRuIHNjcm9sbCB0byB0b3BcbiAgICBmdW5jdGlvbiBfaW5pdEdvVG9wKCkge1xuICAgICAgICAkKCcuanMtZ28tdG9wJykub24oJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgJCgnaHRtbCwgYm9keScpLmFuaW1hdGUoXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBzY3JvbGxUb3A6IDAsXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICA4MDBcbiAgICAgICAgICAgICk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8vYnRuIHNjcm9sbCB0byBlbGVtZW50XG4gICAgZnVuY3Rpb24gX2luaXRHb1RvKCkge1xuICAgICAgICAvL0NsaWNrIGV2ZW50IHRvIHNjcm9sbCB0byBzZWN0aW9uIHdoaXRoIGlkIGxpa2UgaHJlZlxuICAgICAgICAkKCcuanMtZ290bycpLm9uKCdjbGljaycsIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgIHZhciBlbGVtZW50Q2xpY2sgPSAkKHRoaXMpLmF0dHIoJ2hyZWYnKTtcbiAgICAgICAgICAgIHZhciBkZXN0aW5hdGlvbiA9ICQoZWxlbWVudENsaWNrKS5vZmZzZXQoKS50b3A7XG4gICAgICAgICAgICBsZXQgd3JhcFNjcm9sbCA9ICQoJy53cmFwcGVyX19pbm5lcicpLnNjcm9sbFRvcCgpO1xuICAgICAgICAgICAgbGV0IG9mZnNldDtcblxuICAgICAgICAgICAgaWYgKCQod2luZG93KS53aWR0aCgpID4gNDgwKSB7XG4gICAgICAgICAgICAgICAgb2Zmc2V0ID0gOTA7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIG9mZnNldCA9IDYwO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAkKCdodG1sLCBib2R5JykuYW5pbWF0ZSh7XG4gICAgICAgICAgICAgICAgc2Nyb2xsVG9wOiBkZXN0aW5hdGlvbiAtIG9mZnNldCArICdweCcsXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHtcbiAgICAgICAgaW5pdDogX2luaXQsXG4gICAgICAgIGluaXRQdXNoVXA6IF9pbml0UHVzaFVwLFxuICAgICAgICBpbml0RXhwYW5kZWQ6IF9pbml0RXhwYW5kZWQsXG4gICAgICAgIGluaXRIb3ZlckFuaW1hdGU6IF9pbml0SG92ZXJBbmltYXRlLFxuICAgICAgICBpbml0RmxvYXRpbmc6IF9pbml0RmxvYXRpbmcsXG4gICAgICAgIGluaXRTdGF0dXNBbmltYXRlOiBfaW5pdFN0YXR1c0FuaW1hdGUsXG4gICAgICAgIGluaXRUb2dnbGU6IF9pbml0VG9nZ2xlLFxuICAgICAgICBpbml0U2VsZWN0OiBfaW5pdFNlbGVjdCxcbiAgICAgICAgaW5pdEdvVG9wOiBfaW5pdEdvVG9wLFxuICAgICAgICBpbml0R29UbzogX2luaXRHb1RvLFxuICAgIH07XG59KSgpO1xuXG4vKipcbiAqIEJhc2UuQ29tcG9uZW50cy5JbnB1dC5qc1xuICpcbiAqIEBhdXRob3IgQW50b24gVXN0aW5vZmYgPGEuYS51c3Rpbm9mZkBnbWFpbC5jb20+XG4gKi9cblxuQmFzZS5kZWZpbmUoJ0NvbXBvbmVudHMuSW5wdXQnKTtcblxuQmFzZS5Db21wb25lbnRzLklucHV0ID0gKGZ1bmN0aW9uKCkge1xuICAgIGZ1bmN0aW9uIF9pbml0KCkge1xuICAgICAgICBfaW5pdE1hc2soKTtcbiAgICAgICAgX2FkZEV2ZW50TGlzdGVuZXIoKTtcbiAgICB9XG5cbiAgICAvL01hc2tlZCBpbnB1dG1hc2sgaHR0cHM6Ly9naXRodWIuY29tL1JvYmluSGVyYm90cy9JbnB1dG1hc2tcbiAgICBmdW5jdGlvbiBfaW5pdE1hc2soKSB7XG4gICAgICAgIGlmICgkKCcuanMtcGhvbmUtbWFzaycpLmxlbmd0aCkge1xuICAgICAgICAgICAgJCgnLmpzLXBob25lLW1hc2snKS5pbnB1dG1hc2soe1xuICAgICAgICAgICAgICAgIG1hc2s6ICcrNyAoOTk5KSA5OTktOTktOTknLFxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKCQoJy5qcy10aW1lLW1hc2snKS5sZW5ndGgpIHtcbiAgICAgICAgICAgICQoJy5qcy10aW1lLW1hc2snKS5pbnB1dG1hc2soe1xuICAgICAgICAgICAgICAgIG1hc2s6ICc5OTo5OScsXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoJCgnLmpzLWNvZGUtbWFzaycpLmxlbmd0aCkge1xuICAgICAgICAgICAgJCgnLmpzLWNvZGUtbWFzaycpLmlucHV0bWFzayh7XG4gICAgICAgICAgICAgICAgbWFzazogJzkgOSA5IDknLFxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKCQoJy5qcy1ib3JuLW1hc2snKS5sZW5ndGgpIHtcbiAgICAgICAgICAgICQoJy5qcy1ib3JuLW1hc2snKS5pbnB1dG1hc2soe1xuICAgICAgICAgICAgICAgIG1hc2s6ICc5OS45OS45OTk5JyxcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICAgIGlmICgkKCcuanMtY29uZmlybS1tYXNrJykubGVuZ3RoKSB7XG4gICAgICAgICAgICAkKCcuanMtY29uZmlybS1tYXNrJykuaW5wdXRtYXNrKHtcbiAgICAgICAgICAgICAgICBtYXNrOiAnOTk5OScsXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgICAvLyBpZiAoJCgnLmpzLWVtYWlsLW1hc2snKS5sZW5ndGgpIHtcbiAgICAgICAgLy8gICAgICQoJy5qcy1lbWFpbC1tYXNrJykuaW5wdXRtYXNrKHtcbiAgICAgICAgLy8gICAgICAgICBtYXNrOlxuICAgICAgICAvLyAgICAgICAgICAgICAnKnsxLDIwfVsuKnsxLDIwfV1bLip7MSwyMH1dWy4qezEsMjB9XUAqezEsMjB9Wy4qezIsNn1dWy4qezEsMn1dJyxcbiAgICAgICAgLy8gICAgICAgICBncmVlZHk6IGZhbHNlLFxuICAgICAgICAvLyAgICAgICAgIG9uQmVmb3JlUGFzdGU6IGZ1bmN0aW9uKHBhc3RlZFZhbHVlLCBvcHRzKSB7XG4gICAgICAgIC8vICAgICAgICAgICAgIHBhc3RlZFZhbHVlID0gcGFzdGVkVmFsdWUudG9Mb3dlckNhc2UoKTtcbiAgICAgICAgLy8gICAgICAgICAgICAgcmV0dXJuIHBhc3RlZFZhbHVlLnJlcGxhY2UoJ21haWx0bzonLCAnJyk7XG4gICAgICAgIC8vICAgICAgICAgfSxcbiAgICAgICAgLy8gICAgICAgICBkZWZpbml0aW9uczoge1xuICAgICAgICAvLyAgICAgICAgICAgICAnKic6IHtcbiAgICAgICAgLy8gICAgICAgICAgICAgICAgIHZhbGlkYXRvcjogXCJbMC05QS1aYS16ISMkJSYnKisvPT9eX2B7fH1+LV1cIixcbiAgICAgICAgLy8gICAgICAgICAgICAgICAgIGNhcmRpbmFsaXR5OiAxLFxuICAgICAgICAvLyAgICAgICAgICAgICAgICAgY2FzaW5nOiAnbG93ZXInXG4gICAgICAgIC8vICAgICAgICAgICAgIH1cbiAgICAgICAgLy8gICAgICAgICB9XG4gICAgICAgIC8vICAgICB9KTtcbiAgICAgICAgLy8gfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9hZGRFdmVudExpc3RlbmVyKCkge1xuICAgICAgICBsZXQgJGJiSW5wdXQgPSAkKGRvY3VtZW50KS5maW5kKCcuanMtYmItaW5wdXQnKTtcbiAgICAgICAgaWYgKCRiYklucHV0Lmxlbmd0aCkge1xuICAgICAgICAgICAgJGJiSW5wdXQuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBsZXQgJHBhcmVudCA9ICQodGhpcykucGFyZW50KCcuYmItaW5wdXQsIC5iYi10ZXh0Jyk7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAgICAgICAgIC5vbignZm9jdXMnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICRwYXJlbnQuYWRkQ2xhc3MoJ2lzLWZvY3VzJyk7XG4gICAgICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgIC5vbignYmx1cicsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCQodGhpcykudmFsKCkgPT09ICcnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHBhcmVudC5yZW1vdmVDbGFzcygnaXMtZm9jdXMnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICBpZiAoJCh0aGlzKS52YWwoKSAhPT0gJycpIHtcbiAgICAgICAgICAgICAgICAgICAgJHBhcmVudC5hZGRDbGFzcygnaXMtZm9jdXMnKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAkcGFyZW50LnJlbW92ZUNsYXNzKCdpcy1mb2N1cycpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgJCgnLmpzLWlucHV0LS1jb3B5Jykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICB2YXIgaW5wdXQgPSAkKHRoaXMpXG4gICAgICAgICAgICAgICAgLnBhcmVudCgpXG4gICAgICAgICAgICAgICAgLmZpbmQoJ2lucHV0Jyk7XG4gICAgICAgICAgICBpbnB1dC5zZWxlY3QoKTtcbiAgICAgICAgICAgIGRvY3VtZW50LmV4ZWNDb21tYW5kKCdDb3B5Jyk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgICQoJy5qcy1jb3B5LXRleHQnKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIHZhciBpbnB1dCA9ICQodGhpcylcbiAgICAgICAgICAgICAgICAucGFyZW50KClcbiAgICAgICAgICAgICAgICAuZmluZCgnLnVzZXItc2hhcmVfX2xpbmsnKTtcbiAgICAgICAgICAgIGlucHV0LnRleHQoKTtcbiAgICAgICAgICAgIGRvY3VtZW50LmV4ZWNDb21tYW5kKCdDb3B5Jyk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vQ2xpY2sgaW5wdXQgc2VsZWN0IHZhbHVlXG4gICAgICAgICQoJy5qcy1pbnB1dC1mb2N1cy0tY29weScpLm9uKCdmb2N1cycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgJCh0aGlzKS5zZWxlY3QoKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy9TaG93IFBhc3N3b3JkXG4gICAgICAgICQoJy5qcy1iYi1pbnB1dC1wYXNzd29yZC0tc2hvdycpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgJCh0aGlzKS5jc3MoJ2Rpc3BsYXknLCAnbm9uZScpO1xuICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgIC5uZXh0KClcbiAgICAgICAgICAgICAgICAuY3NzKCdkaXNwbGF5JywgJ2Jsb2NrJyk7XG4gICAgICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAgICAgLnBhcmVudCgpXG4gICAgICAgICAgICAgICAgLmZpbmQoJ2lucHV0W3R5cGU9XCJwYXNzd29yZFwiXScpXG4gICAgICAgICAgICAgICAgLmF0dHIoJ3R5cGUnLCAndGV4dCcpO1xuICAgICAgICB9KTtcblxuICAgICAgICAvL0hpZGUgUGFzc3dvcmRcbiAgICAgICAgJCgnLmpzLWJiLWlucHV0LXBhc3N3b3JkLS1oaWRlJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAkKHRoaXMpLmNzcygnZGlzcGxheScsICdub25lJyk7XG4gICAgICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAgICAgLnByZXYoKVxuICAgICAgICAgICAgICAgIC5jc3MoJ2Rpc3BsYXknLCAnYmxvY2snKTtcbiAgICAgICAgICAgICQodGhpcylcbiAgICAgICAgICAgICAgICAucGFyZW50KClcbiAgICAgICAgICAgICAgICAuZmluZCgnaW5wdXRbdHlwZT1cInRleHRcIl0nKVxuICAgICAgICAgICAgICAgIC5hdHRyKCd0eXBlJywgJ3Bhc3N3b3JkJyk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtYmItaW5wdXQtdGlwJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBpZiAoJCh0aGlzKS5oYXNDbGFzcygnbm8tY2xvc2UnKSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgICQodGhpcylcbiAgICAgICAgICAgICAgICAucGFyZW50KClcbiAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ2lzLWluZm8gaXMtZXJyb3IgaXMtaW52YWxpZCcpXG4gICAgICAgICAgICAgICAgLmVuZCgpXG4gICAgICAgICAgICAgICAgLmhpZGUoKTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHtcbiAgICAgICAgaW5pdDogX2luaXQsXG4gICAgfTtcbn0pKCk7XG5cbi8qKlxuICogQmFzZS5Db21wb25lbnRzLlBvcHVwLmpzXG4gKlxuICogQGF1dGhvciBBbnRvbiBVc3Rpbm9mZiA8YS5hLnVzdGlub2ZmQGdtYWlsLmNvbT5cbiAqL1xuXG5CYXNlLmRlZmluZSgnQ29tcG9uZW50cy5Qb3B1cCcpO1xuXG5CYXNlLkNvbXBvbmVudHMuUG9wdXAgPSAoZnVuY3Rpb24oKSB7XG4gICAgZnVuY3Rpb24gX2luaXQoKSB7XG4gICAgICAgIF9mYW5jeUJveCgpO1xuICAgICAgICBfd2hvSXMoKTtcbiAgICAgICAgX2NoYW5nZUZvcm1UaXRsZSgpO1xuICAgICAgICBfcmVpbml0KCk7XG4gICAgfVxuXG4gICAgLy9Nb2RhbCBGYW5jeUJveCAzIGh0dHBzOi8vZmFuY3lhcHBzLmNvbS9mYW5jeWJveC8zL1xuICAgIGZ1bmN0aW9uIF9mYW5jeUJveCgpIHtcbiAgICAgICAgbGV0ICRmYW5jeUltYWdlUG9wdXAgPSAkZG9jdW1lbnQuZmluZCgnW2RhdGEtZmFuY3lib3g9XCJpbWFnZXNcIl0nKTtcbiAgICAgICAgaWYgKCRmYW5jeUltYWdlUG9wdXAubGVuZ3RoKSB7XG4gICAgICAgICAgICAkZmFuY3lJbWFnZVBvcHVwLmZhbmN5Ym94KHtcbiAgICAgICAgICAgICAgICBiYXNlQ2xhc3M6ICdmYW5jeWJveC1jb250YWluZXItLWltYWdlJyxcbiAgICAgICAgICAgICAgICB0b29sYmFyOiB0cnVlLFxuICAgICAgICAgICAgICAgIHNtYWxsQnRuOiB0cnVlLFxuICAgICAgICAgICAgICAgIGNsb3NlQ2xpY2tPdXRzaWRlOiB0cnVlLFxuICAgICAgICAgICAgICAgIG1vYmlsZToge1xuICAgICAgICAgICAgICAgICAgICBjbGlja0NvbnRlbnQ6ICdjbG9zZScsXG4gICAgICAgICAgICAgICAgICAgIGNsaWNrU2xpZGU6ICdjbG9zZScsXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgLy9Gb3JtIFdobyBJcz9cbiAgICBmdW5jdGlvbiBfd2hvSXMoKSB7XG4gICAgICAgICQoJy5qcy13aG9pcycpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgbGV0IHdob2lzID0gJCh0aGlzKS5kYXRhKCd3aG9pcycpO1xuICAgICAgICAgICAgbGV0IGZvcm0gPSAkKCcjYXV0aC1mb3JtJykuZmluZCgnLmZvcm0nKTtcbiAgICAgICAgICAgIGlmICh3aG9pcyA9PT0gJ21hc3RlcicpIHtcbiAgICAgICAgICAgICAgICBmb3JtLmFkZENsYXNzKCdpcy1tYXN0ZXInKTtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAod2hvaXMgPT09ICdzdHVkaW8nKSB7XG4gICAgICAgICAgICAgICAgZm9ybS5hZGRDbGFzcygnaXMtc3R1ZGlvJyk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGZvcm0uYWRkQ2xhc3MoJ2lzLWNsaWVudCcpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvL0R1bmFtaWNseSBjaGFuZ2UgZm9ybSB0aXRsZVxuICAgIGZ1bmN0aW9uIF9jaGFuZ2VGb3JtVGl0bGUoKSB7XG4gICAgICAgIGxldCAkZm9ybVRpdGxlID0gJGRvY3VtZW50LmZpbmQoJy5qcy1mb3JtLXRpdGxlJyk7XG4gICAgICAgICQoJy5qcy1mb3JtLXRpdGxlJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBjb25zb2xlLmxvZygnLS0tJywgJ0NMSUNLJyk7XG4gICAgICAgICAgICB2YXIgdGV4dCA9ICQodGhpcykuZGF0YSgndGl0bGUnKTtcblxuICAgICAgICAgICAgJGZvcm1UaXRsZS5ub3QoJCh0aGlzKSkucmVtb3ZlQ2xhc3MoJ2lzLWFjdGl2ZScpO1xuXG4gICAgICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAgICAgLmFkZENsYXNzKCdpcy1hY3RpdmUnKVxuICAgICAgICAgICAgICAgIC5jbG9zZXN0KCcuZm9ybScpXG4gICAgICAgICAgICAgICAgLmZpbmQoJy5mb3JtX19idG4nKVxuICAgICAgICAgICAgICAgIC50ZXh0KHRleHQpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfcmVpbml0KCkge1xuICAgICAgICAkZG9jdW1lbnQub24oJ3Nob3cuYnMubW9kYWwnLCAnLm1vZGFsJywgZSA9PiB7XG4gICAgICAgICAgICBCYXNlLkNvbXBvbmVudHMuU2VsZWN0LmluaXRTZWxlY3RDb2xvcigpO1xuICAgICAgICAgICAgQmFzZS5Db21wb25lbnRzLlBvcHVwLmNoYW5nZUZvcm1UaXRsZSgpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICByZXR1cm4ge1xuICAgICAgICBpbml0OiBfaW5pdCxcbiAgICAgICAgZmFuY3lCb3g6IF9mYW5jeUJveCxcbiAgICAgICAgd2hvSXM6IF93aG9JcyxcbiAgICAgICAgY2hhbmdlRm9ybVRpdGxlOiBfY2hhbmdlRm9ybVRpdGxlLFxuICAgICAgICByZWluaXQ6IF9yZWluaXQsXG4gICAgfTtcbn0pKCk7XG5cbi8qKlxuICogQmFzZS5Db21wb25lbnRzLlNlbGVjdC5qc1xuICpcbiAqIEBhdXRob3IgQW50b24gVXN0aW5vZmYgPGEuYS51c3Rpbm9mZkBnbWFpbC5jb20+XG4gKiBDdXN0b20gU2VsZWN0IGh0dHBzOi8vc2VsZWN0Mi5vcmcvXG4gKi9cblxuQmFzZS5kZWZpbmUoJ0NvbXBvbmVudHMuU2VsZWN0Jyk7XG5cbkJhc2UuQ29tcG9uZW50cy5TZWxlY3QgPSAoZnVuY3Rpb24oKSB7XG4gICAgZnVuY3Rpb24gX2luaXQoKSB7XG4gICAgICAgICQoJy5qcy1zZWxlY3QnKS5zZWxlY3QyKHtcbiAgICAgICAgICAgIGxhbmd1YWdlOiAncnUnLFxuICAgICAgICB9KTtcblxuICAgICAgICAkKCcuanMtc2VsZWN0LS1tdWx0aXBsZScpLnNlbGVjdDIoe1xuICAgICAgICAgICAgbGFuZ3VhZ2U6ICdydScsXG4gICAgICAgICAgICB0YWdzOiB0cnVlLFxuICAgICAgICB9KTtcblxuICAgICAgICAkKCcuanMtc2VsZWN0LmJiLXNlbGVjdC0tbWV0cm8nKS5zZWxlY3QyKHtcbiAgICAgICAgICAgIGxhbmd1YWdlOiAncnUnLFxuICAgICAgICAgICAgdGVtcGxhdGVSZXN1bHQ6IGFkZFVzZXJQaWMsXG4gICAgICAgIH0pO1xuXG4gICAgICAgICQoJy5qcy1zZWxlY3Qubm8tc2VhcmNoJykuc2VsZWN0Mih7XG4gICAgICAgICAgICBsYW5ndWFnZTogJ3J1JyxcbiAgICAgICAgICAgIG1pbmltdW1SZXN1bHRzRm9yU2VhcmNoOiAtMSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy/QlNC+0LHQsNCy0LvRj9C10Lwg0LjQutC+0L3QutGDINC80LXRgtGA0L4g0LIg0YHQtdC70LXQutGCXG4gICAgICAgIGZ1bmN0aW9uIGFkZFVzZXJQaWMob3B0KSB7XG4gICAgICAgICAgICBpZiAoIW9wdC5pZCkge1xuICAgICAgICAgICAgICAgIHJldHVybiBvcHQudGV4dDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHZhciBvcHRpbWFnZSA9ICQob3B0LmVsZW1lbnQpLmRhdGEoJ2ltYWdlJyk7XG4gICAgICAgICAgICBpZiAoIW9wdGltYWdlKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIG9wdC50ZXh0O1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB2YXIgJG9wdCA9ICQoXG4gICAgICAgICAgICAgICAgICAgICc8c3BhbiBjbGFzcz1cIm1ldHJvLWljb24gbWV0cm8taWNvbi0tJyArXG4gICAgICAgICAgICAgICAgICAgICAgICBvcHRpbWFnZSArXG4gICAgICAgICAgICAgICAgICAgICAgICAnXCI+JyArXG4gICAgICAgICAgICAgICAgICAgICAgICAkKG9wdC5lbGVtZW50KS50ZXh0KCkgK1xuICAgICAgICAgICAgICAgICAgICAgICAgJzwvc3Bhbj4nXG4gICAgICAgICAgICAgICAgKTtcbiAgICAgICAgICAgICAgICByZXR1cm4gJG9wdDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIF9pbml0U2VydmljZXMoKTtcbiAgICAgICAgX2luaXROYXRpdmUoKTtcbiAgICAgICAgX2luaXRNdWx0aXBsZSgpO1xuICAgICAgICBfaW5pdENvbG9yKCk7XG4gICAgICAgIF9pbml0SWNvbigpO1xuICAgICAgICBfaW5pdEJvcm4oKTtcbiAgICAgICAgX2luaXRTaG93WWVhcigpO1xuICAgICAgICBfaW5pdEhpZGVZZWFyKCk7XG4gICAgICAgIF9pbml0UGhvbmVDb2RlKCk7XG4gICAgICAgIF9pbml0TW9iaWxlKCk7XG4gICAgICAgIF9pbml0QWRkRXZlbnRMaXN0ZW5lcigpO1xuICAgICAgICBfaW5pdElucHV0Q3VzdG9tKCk7XG4gICAgICAgIF9pbml0SW5wdXRDdXN0b21Db250cm9sKCk7XG4gICAgICAgIF9pbml0U2VsZWN0Q29sb3IoKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdFNlcnZpY2VzKCkge1xuICAgICAgICBsZXQgJHNlbGVjdFNlcnZpY2VzID0gJCgnLmpzLXNlbGVjdC0tc2VydmljZXMnKTtcblxuICAgICAgICAkc2VsZWN0U2VydmljZXMuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGxldCAkcGFyZW50ID0gJCh0aGlzKS5jbG9zZXN0KCcuYmItaW5wdXQtLXNlbGVjdCcpO1xuICAgICAgICAgICAgJCh0aGlzKS5zZWxlY3QyKHtcbiAgICAgICAgICAgICAgICBsYW5ndWFnZTogJ3J1JyxcbiAgICAgICAgICAgICAgICBkcm9wZG93blBhcmVudDogJHBhcmVudCxcbiAgICAgICAgICAgICAgICB0ZW1wbGF0ZVNlbGVjdGlvbjogdGltZUFuZFByaWNlLFxuICAgICAgICAgICAgICAgIHRlbXBsYXRlUmVzdWx0OiB0aW1lQW5kUHJpY2UsXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy9TZWxlY3QgQWRkIFByaWNlIFRpbWUgJiBQcmljZVxuICAgICAgICBmdW5jdGlvbiB0aW1lQW5kUHJpY2Uob3B0KSB7XG4gICAgICAgICAgICB2YXIgb3JpZ2luYWxUaW1lID0gJChvcHQuZWxlbWVudCkuZGF0YSgndGltZScpO1xuICAgICAgICAgICAgdmFyIG9yaWdpbmFsUHJpY2UgPSAkKG9wdC5lbGVtZW50KS5kYXRhKCdwcmljZScpO1xuXG4gICAgICAgICAgICByZXR1cm4gJChcbiAgICAgICAgICAgICAgICAnPGRpdiBjbGFzcz1jdXN0b20tc2VsZWN0X19yZXN1bHRzPicgK1xuICAgICAgICAgICAgICAgICAgICAnPHNwYW4+JyArXG4gICAgICAgICAgICAgICAgICAgIG9wdC50ZXh0ICtcbiAgICAgICAgICAgICAgICAgICAgJzwvc3Bhbj4nICtcbiAgICAgICAgICAgICAgICAgICAgJzxzcGFuPicgK1xuICAgICAgICAgICAgICAgICAgICBvcmlnaW5hbFRpbWUgK1xuICAgICAgICAgICAgICAgICAgICAnPC9zcGFuPicgK1xuICAgICAgICAgICAgICAgICAgICAnPHNwYW4+JyArXG4gICAgICAgICAgICAgICAgICAgIG9yaWdpbmFsUHJpY2UgK1xuICAgICAgICAgICAgICAgICAgICAnPC9zcGFuPicgK1xuICAgICAgICAgICAgICAgICAgICAnPC9kaXY+J1xuICAgICAgICAgICAgKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9pbml0TmF0aXZlKCkge1xuICAgICAgICB2YXIgJHNlbGVjdE5hdGl2ZSA9ICRkb2N1bWVudC5maW5kKCcuanMtc2VsZWN0LW5hdGl2ZScpO1xuICAgICAgICBpZiAoJHNlbGVjdE5hdGl2ZS5sZW5ndGgpIHtcbiAgICAgICAgICAgIGlmICgkd2luZG93LndpZHRoKCkgPiA0ODApIHtcbiAgICAgICAgICAgICAgICAkc2VsZWN0TmF0aXZlLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmICghJCh0aGlzKS5oYXNDbGFzcygnc2VsZWN0Mi1oaWRkZW4tYWNjZXNzaWJsZScpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBsZXQgJHBhcmVudCA9ICQodGhpcykuY2xvc2VzdChcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAnLmJiLWlucHV0LS1zZWxlY3QsIC5wcm9maWxlLWVkaXRvcl9fc2VsZWN0J1xuICAgICAgICAgICAgICAgICAgICAgICAgKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuc2VsZWN0Mih7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbGFuZ3VhZ2U6ICdydScsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbWluaW11bVJlc3VsdHNGb3JTZWFyY2g6IC0xLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRyb3Bkb3duUGFyZW50OiAkcGFyZW50LFxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJHNlbGVjdE5hdGl2ZS5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICBpZiAoJCh0aGlzKS5oYXNDbGFzcygnc2VsZWN0Mi1oaWRkZW4tYWNjZXNzaWJsZScpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnNlbGVjdDIoJ2Rlc3Ryb3knKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIGxldCBfdGhpcyA9ICQodGhpcyk7XG4gICAgICAgICAgICAgICAgICAgIGxldCAkcGFyZW50ID0gX3RoaXMuY2xvc2VzdCgnLmJiLWlucHV0Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgbGV0ICR0aXRsZSA9ICRwYXJlbnQuZmluZCgnLmJiLWlucHV0X190aXRsZScpO1xuICAgICAgICAgICAgICAgICAgICBsZXQgdGl0bGVUZXh0ID0gJHRpdGxlLnRleHQoKTtcblxuICAgICAgICAgICAgICAgICAgICBsZXQgcGxhY2Vob2xkZXIgPSBfdGhpcy5kYXRhKCdwbGFjZWhvbGRlcicpO1xuICAgICAgICAgICAgICAgICAgICBsZXQgJGZpcnN0T3B0aW9uID0gX3RoaXMuZmluZCgnb3B0aW9uOmZpcnN0LWNoaWxkJyk7XG4gICAgICAgICAgICAgICAgICAgIGxldCAkbmV3T3B0aW9uID0gJCgnPG9wdGlvbj4nKS5hdHRyKHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGRpc2FibGVkOiAnZGlzYWJsZWQnLFxuICAgICAgICAgICAgICAgICAgICAgICAgc2VsZWN0ZWQ6ICdzZWxlY3RlZCcsXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICBsZXQgdHlwZSA9ICRwYXJlbnQuZGF0YSgndHlwZScpO1xuXG4gICAgICAgICAgICAgICAgICAgIGxldCB0ZXh0O1xuICAgICAgICAgICAgICAgICAgICBpZiAodGl0bGVUZXh0ICE9PSAnJyB8fCB0aXRsZVRleHQgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0ZXh0ID0gdGl0bGVUZXh0O1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKFxuICAgICAgICAgICAgICAgICAgICAgICAgcGxhY2Vob2xkZXIgIT09ICcnIHx8XG4gICAgICAgICAgICAgICAgICAgICAgICBwbGFjZWhvbGRlciAhPT0gJ3VuZGVmaW5lZCdcbiAgICAgICAgICAgICAgICAgICAgKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0ZXh0ID0gcGxhY2Vob2xkZXI7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBpZiAoJHBhcmVudC5oYXNDbGFzcygnYmItaW5wdXQtLXRyYW5zZm9ybScpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoJGZpcnN0T3B0aW9uLmlzKCc6ZW1wdHknKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmICh0eXBlID09PSAnc2VsZWN0ZWQnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRmaXJzdE9wdGlvbi5yZW1vdmUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJHBhcmVudC5hZGRDbGFzcygnaXMtZm9jdXMnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkZmlyc3RPcHRpb24ucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgX3RoaXMucmVtb3ZlQXR0cignZGF0YS1wbGFjZWhvbGRlcicpLnZhbCh0ZXh0KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBfYWRkUmVzZXRCdG4oX3RoaXMpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvL2ZpcnN0T3B0aW9uIG5vdCBlbXB0eVxuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAodHlwZSA9PT0gJ3NlbGVjdGVkJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkcGFyZW50LmFkZENsYXNzKCdpcy1mb2N1cycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRuZXdPcHRpb24ucHJlcGVuZFRvKF90aGlzKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBfYWRkUmVzZXRCdG4oX3RoaXMpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICgkZmlyc3RPcHRpb24uaXMoJzplbXB0eScpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGZpcnN0T3B0aW9uXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC52YWwocGxhY2Vob2xkZXIpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC50ZXh0KHBsYWNlaG9sZGVyKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYXR0cih7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzZWxlY3RlZDogJ3NlbGVjdGVkJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRpc2FibGVkOiAnZGlzYWJsZWQnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBfdGhpc1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2hhcy1wbGFjZWhvbGRlcicpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmVBdHRyKCdkYXRhLXBsYWNlaG9sZGVyJylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLnZhbChwbGFjZWhvbGRlcik7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIF9hZGRSZXNldEJ0bihfdGhpcyk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICgkKHRoaXMpLmhhc0NsYXNzKCdoYXMtcGxhY2Vob2xkZXInKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoJ2hhcy1wbGFjZWhvbGRlcicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBsZXQgJGZpcnN0T3B0aW9uID0gX3RoaXMuZmluZCgnb3B0aW9uOmZpcnN0LWNoaWxkJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoJCh0aGlzKS52YWwoKSAhPT0gJycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkcGFyZW50LmFkZENsYXNzKCdpcy1mb2N1cycpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCRmaXJzdE9wdGlvbi5pcygnOmVtcHR5JykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJGZpcnN0T3B0aW9uLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHBhcmVudC5yZW1vdmVDbGFzcygnaXMtZm9jdXMnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS53cmFwKCc8bGFiZWwgY2xhc3M9XCJiYi1zZWxlY3RcIj4nKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9pbml0TXVsdGlwbGUoKSB7XG4gICAgICAgIHZhciAkc2VsZWN0TXVsdGlwbGUgPSAkZG9jdW1lbnQuZmluZCgnLmpzLXNlbGVjdC0tbXVsdGlwbGUnKTtcblxuICAgICAgICBpZiAoJHNlbGVjdE11bHRpcGxlLmxlbmd0aCkge1xuICAgICAgICAgICAgaWYgKCR3aW5kb3cud2lkdGgoKSA+IDQ4MCkge1xuICAgICAgICAgICAgICAgICRzZWxlY3RNdWx0aXBsZS5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICBsZXQgX3RoaXMgPSAkKHRoaXMpO1xuICAgICAgICAgICAgICAgICAgICBsZXQgJHBhcmVudCA9IF90aGlzLmNsb3Nlc3QoJy5iYi1pbnB1dCcpO1xuICAgICAgICAgICAgICAgICAgICBfdGhpcy5zZWxlY3QyKHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGxhbmd1YWdlOiAncnUnLFxuICAgICAgICAgICAgICAgICAgICAgICAgdGFnczogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIG1pbmltdW1SZXN1bHRzRm9yU2VhcmNoOiAyLFxuICAgICAgICAgICAgICAgICAgICAgICAgZHJvcGRvd25QYXJlbnQ6ICRwYXJlbnQsXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkc2VsZWN0TXVsdGlwbGUuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgbGV0IF90aGlzID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgICAgICAgICBsZXQgJHBhcmVudCA9IF90aGlzLmNsb3Nlc3QoJy5iYi1pbnB1dCcpO1xuICAgICAgICAgICAgICAgICAgICBsZXQgJHNlbGVjdDJJbnB1dCA9ICRwYXJlbnQuZmluZCgnaW5wdXQnKTtcbiAgICAgICAgICAgICAgICAgICAgbGV0IHBsYWNlaG9sZGVyID0gJHNlbGVjdDJJbnB1dC5hdHRyKCdwbGFjZWhvbGRlcicpO1xuICAgICAgICAgICAgICAgICAgICBfdGhpcy5jaGlsZHJlbigpLmFkZENsYXNzKCduZWVkc2NsaWNrJyk7XG4gICAgICAgICAgICAgICAgICAgIF90aGlzLnNlbGVjdDIoe1xuICAgICAgICAgICAgICAgICAgICAgICAgbGFuZ3VhZ2U6ICdydScsXG4gICAgICAgICAgICAgICAgICAgICAgICB0YWdzOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgbWluaW11bVJlc3VsdHNGb3JTZWFyY2g6IDIsXG4gICAgICAgICAgICAgICAgICAgICAgICBkcm9wZG93blBhcmVudDogJHBhcmVudCxcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCRwYXJlbnQuaGFzQ2xhc3MoJ2JiLWlucHV0LS10cmFuc2Zvcm0nKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJHNlbGVjdDJJbnB1dC5hdHRyKCdwbGFjZWhvbGRlcicsICcnKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIC8vINC+0YLQutCw0YIg0YLQsNC50YLQu9CwXG4gICAgICAgICAgICAgICAgICAgICQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5vbignc2VsZWN0MjpvcGVuJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCQodGhpcykuaGFzQ2xhc3MoJ2hhcy1wbGFjZWhvbGRlcicpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoJ2hhcy1wbGFjZWhvbGRlcicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKCcuc2VsZWN0Mi1zZWFyY2hfX2ZpZWxkJykuYXR0cihcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdyZWFkb25seScsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0cnVlXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCQodGhpcykudmFsKCkgIT09ICcnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRwYXJlbnQuYWRkQ2xhc3MoJ2lzLWZvY3VzJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRzZWxlY3QySW5wdXQuYXR0cigncGxhY2Vob2xkZXInLCBwbGFjZWhvbGRlcik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJHBhcmVudC5yZW1vdmVDbGFzcygnaXMtZm9jdXMnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgICAgICAgICAgLm9uKCdzZWxlY3QyOmNsb3NlJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCQodGhpcykudmFsKCkgPT0gJycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJHBhcmVudC5yZW1vdmVDbGFzcygnaXMtZm9jdXMnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJHNlbGVjdDJJbnB1dC5yZW1vdmVBdHRyKCdwbGFjZWhvbGRlcicpLmJsdXIoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkcGFyZW50LmFkZENsYXNzKCdpcy1mb2N1cycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gX2luaXRBZGRFdmVudExpc3RlbmVyKCkge1xuICAgICAgICAkZG9jdW1lbnQub24oJ2ZvY3VzJywgJy5zZWxlY3QyLXNlYXJjaF9fZmllbGQnLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICB9KTtcblxuICAgICAgICBsZXQgJGljb24gPSAkZG9jdW1lbnQuZmluZCgnLmpzLWljb24tdG9nZ2xlJyk7XG4gICAgICAgICRpY29uLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBsZXQgX3RoaXMgPSAkKHRoaXMpO1xuICAgICAgICAgICAgbGV0ICRzZWxlY3QgPSBfdGhpcy5maW5kKCcuanMtc2VsZWN0LW5hdGl2ZScpO1xuICAgICAgICAgICAgbGV0ICRvcHRpb25TZWxlY3RlZCA9IF90aGlzXG4gICAgICAgICAgICAgICAgLmZpbmQoJy5qcy1zZWxlY3QtbmF0aXZlIDpzZWxlY3RlZCcpXG4gICAgICAgICAgICAgICAgLmRhdGEoJ2ljb24nKTtcbiAgICAgICAgICAgIGxldCAkaWNvbiA9IF90aGlzLmZpbmQoJy5pY29uJyk7XG4gICAgICAgICAgICAkaWNvbi5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGxldCAkZGF0YUluSWNvbiA9ICQodGhpcykuZGF0YSgnaWNvbicpO1xuICAgICAgICAgICAgICAgIGlmICgkZGF0YUluSWNvbiAhPSAkb3B0aW9uU2VsZWN0ZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICRzZWxlY3Qub24oJ2NoYW5nZScsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGxldCAkc2VsZWN0Q2hhbmdlID0gX3RoaXNcbiAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy5qcy1zZWxlY3QtbmF0aXZlIDpzZWxlY3RlZCcpXG4gICAgICAgICAgICAgICAgICAgIC5kYXRhKCdpY29uJyk7XG4gICAgICAgICAgICAgICAgJGljb24uZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgbGV0ICRkYXRhSW5JY29uID0gJCh0aGlzKS5kYXRhKCdpY29uJyk7XG4gICAgICAgICAgICAgICAgICAgIGlmICgkZGF0YUluSWNvbiAhPSAkc2VsZWN0Q2hhbmdlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuc2hvdygpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gX2luaXRJY29uKCkge1xuICAgICAgICBsZXQgJGljb25TZWxlY3QgPSAkZG9jdW1lbnQuZmluZCgnLmpzLXNlbGVjdC0taWNvbicpO1xuXG4gICAgICAgICRpY29uU2VsZWN0LmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBsZXQgJHBhcmVudCA9ICQodGhpcykuY2xvc2VzdCgnLmJiLWlucHV0LS1zZWxlY3QnKTtcblxuICAgICAgICAgICAgJCh0aGlzKS5zZWxlY3QyKHtcbiAgICAgICAgICAgICAgICBsYW5ndWFnZTogJ3J1JyxcbiAgICAgICAgICAgICAgICB0ZW1wbGF0ZVNlbGVjdGlvbjogaWZvcm1hdCxcbiAgICAgICAgICAgICAgICB0ZW1wbGF0ZVJlc3VsdDogaWZvcm1hdCxcbiAgICAgICAgICAgICAgICBkcm9wZG93blBhcmVudDogJHBhcmVudCxcbiAgICAgICAgICAgICAgICBtaW5pbXVtUmVzdWx0c0ZvclNlYXJjaDogLTEsXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy9JY29uIGZvbnRhd2Vzb21lIGluc2lkZSBzZWxlY3RcbiAgICAgICAgZnVuY3Rpb24gaWZvcm1hdChpY29uKSB7XG4gICAgICAgICAgICB2YXIgb3JpZ2luYWxPcHRpb24gPSBpY29uLmVsZW1lbnQ7XG4gICAgICAgICAgICByZXR1cm4gJChcbiAgICAgICAgICAgICAgICAnPHNwYW4+PGkgY2xhc3M9XCJzZWxlY3QyX19pY29uJyArXG4gICAgICAgICAgICAgICAgICAgICcgJyArXG4gICAgICAgICAgICAgICAgICAgICQob3JpZ2luYWxPcHRpb24pLmRhdGEoJ2ljb24nKSArXG4gICAgICAgICAgICAgICAgICAgICdcIj48L2k+ICcgK1xuICAgICAgICAgICAgICAgICAgICBpY29uLnRleHQgK1xuICAgICAgICAgICAgICAgICAgICAnPC9zcGFuPidcbiAgICAgICAgICAgICk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdENvbG9yKCkge1xuICAgICAgICBsZXQgJGNvbG9yU2VsZWN0ID0gJGRvY3VtZW50LmZpbmQoJy5qcy1zZWxlY3QtLWNvbG9yJyk7XG4gICAgICAgIGlmICgkY29sb3JTZWxlY3QubGVuZ3RoKSB7XG4gICAgICAgICAgICAkY29sb3JTZWxlY3QuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBjb2xvckN1c3RvbSh0aGlzKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gY29sb3JOYXRpdmUoZWxlbSkge1xuICAgICAgICAgICAgbGV0IF90aGlzID0gJChlbGVtKTtcbiAgICAgICAgICAgIGxldCAkcGFyZW50ID0gX3RoaXMuY2xvc2VzdCgnLmJiLWlucHV0Jyk7XG5cbiAgICAgICAgICAgIGxldCAkdGl0bGUgPSAkcGFyZW50LmZpbmQoJy5iYi1pbnB1dF9fdGl0bGUnKTtcbiAgICAgICAgICAgIGxldCB0aXRsZVRleHQgPSAkdGl0bGUudGV4dCgpO1xuXG4gICAgICAgICAgICBsZXQgcGxhY2Vob2xkZXIgPSBfdGhpcy5kYXRhKCdwbGFjZWhvbGRlcicpO1xuICAgICAgICAgICAgbGV0ICRmaXJzdE9wdGlvbiA9IF90aGlzLmZpbmQoJ29wdGlvbjpmaXJzdC1jaGlsZCcpO1xuICAgICAgICAgICAgbGV0ICRuZXdPcHRpb24gPSAkKCc8b3B0aW9uPicpLmF0dHIoe1xuICAgICAgICAgICAgICAgIGRpc2FibGVkOiAnZGlzYWJsZWQnLFxuICAgICAgICAgICAgICAgIHNlbGVjdGVkOiAnc2VsZWN0ZWQnLFxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBsZXQgdHlwZSA9ICRwYXJlbnQuZGF0YSgndHlwZScpO1xuXG4gICAgICAgICAgICBsZXQgdGV4dDtcbiAgICAgICAgICAgIGlmICh0aXRsZVRleHQgIT09ICcnIHx8IHR5cGVvZiB0aXRsZVRleHQgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICAgICAgdGV4dCA9IHRpdGxlVGV4dDtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAoXG4gICAgICAgICAgICAgICAgcGxhY2Vob2xkZXIgIT09ICcnIHx8XG4gICAgICAgICAgICAgICAgdHlwZW9mIHBsYWNlaG9sZGVyICE9PSAndW5kZWZpbmVkJ1xuICAgICAgICAgICAgKSB7XG4gICAgICAgICAgICAgICAgdGV4dCA9IHBsYWNlaG9sZGVyO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmICgkcGFyZW50Lmhhc0NsYXNzKCdiYi1pbnB1dC0tdHJhbnNmb3JtJykpIHtcbiAgICAgICAgICAgICAgICBpZiAoJGZpcnN0T3B0aW9uLmlzKCc6ZW1wdHknKSkge1xuICAgICAgICAgICAgICAgICAgICBpZiAodHlwZSA9PT0gJ3NlbGVjdGVkJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgJGZpcnN0T3B0aW9uLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJHBhcmVudC5hZGRDbGFzcygnaXMtZm9jdXMnKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICRmaXJzdE9wdGlvbi5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgX3RoaXMucmVtb3ZlQXR0cignZGF0YS1wbGFjZWhvbGRlcicpLnZhbCh0ZXh0KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgX2FkZFJlc2V0QnRuKF90aGlzKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAvL2ZpcnN0T3B0aW9uIG5vdCBlbXB0eVxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGlmICh0eXBlID09PSAnc2VsZWN0ZWQnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkcGFyZW50LmFkZENsYXNzKCdpcy1mb2N1cycpO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgJG5ld09wdGlvbi5wcmVwZW5kVG8oX3RoaXMpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBfYWRkUmVzZXRCdG4oX3RoaXMpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBpZiAoJGZpcnN0T3B0aW9uLmlzKCc6ZW1wdHknKSkge1xuICAgICAgICAgICAgICAgICAgICAkZmlyc3RPcHRpb25cbiAgICAgICAgICAgICAgICAgICAgICAgIC52YWwocGxhY2Vob2xkZXIpXG4gICAgICAgICAgICAgICAgICAgICAgICAudGV4dChwbGFjZWhvbGRlcilcbiAgICAgICAgICAgICAgICAgICAgICAgIC5hdHRyKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzZWxlY3RlZDogJ3NlbGVjdGVkJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkaXNhYmxlZDogJ2Rpc2FibGVkJyxcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICBfdGhpc1xuICAgICAgICAgICAgICAgICAgICAgICAgLmFkZENsYXNzKCdoYXMtcGxhY2Vob2xkZXInKVxuICAgICAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUF0dHIoJ2RhdGEtcGxhY2Vob2xkZXInKVxuICAgICAgICAgICAgICAgICAgICAgICAgLnZhbChwbGFjZWhvbGRlcik7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAkKGVsZW0pLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBpZiAoJChlbGVtKS5oYXNDbGFzcygnaGFzLXBsYWNlaG9sZGVyJykpIHtcbiAgICAgICAgICAgICAgICAgICAgJChlbGVtKS5yZW1vdmVDbGFzcygnaGFzLXBsYWNlaG9sZGVyJyk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgbGV0ICRmaXJzdE9wdGlvbiA9IF90aGlzLmZpbmQoJ29wdGlvbjpmaXJzdC1jaGlsZCcpO1xuICAgICAgICAgICAgICAgIGlmICgkKGVsZW0pLnZhbCgpICE9PSAnJykge1xuICAgICAgICAgICAgICAgICAgICAkcGFyZW50LmFkZENsYXNzKCdpcy1mb2N1cycpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICgkZmlyc3RPcHRpb24uaXMoJzplbXB0eScpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkZmlyc3RPcHRpb24ucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAkcGFyZW50LnJlbW92ZUNsYXNzKCdpcy1mb2N1cycpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkKGVsZW0pLndyYXAoJzxsYWJlbCBjbGFzcz1cImJiLXNlbGVjdFwiPicpO1xuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gY29sb3JDdXN0b20oZWxlbSkge1xuICAgICAgICAgICAgbGV0ICRwYXJlbnQgPSAkKGVsZW0pLmNsb3Nlc3QoJy5zZWxlY3QtY29sb3InKTtcbiAgICAgICAgICAgIGlmICgkKGVsZW0pLmhhc0NsYXNzKCdzZWFyY2gtZW5hYmxlZCcpKSB7XG4gICAgICAgICAgICAgICAgJChlbGVtKS5zZWxlY3QyKHtcbiAgICAgICAgICAgICAgICAgICAgbGFuZ3VhZ2U6ICdydScsXG4gICAgICAgICAgICAgICAgICAgIHRlbXBsYXRlU2VsZWN0aW9uOiBpQmFsbCxcbiAgICAgICAgICAgICAgICAgICAgdGVtcGxhdGVSZXN1bHQ6IGlCYWxsLFxuICAgICAgICAgICAgICAgICAgICBkcm9wZG93blBhcmVudDogJHBhcmVudCxcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJChlbGVtKS5zZWxlY3QyKHtcbiAgICAgICAgICAgICAgICAgICAgbGFuZ3VhZ2U6ICdydScsXG4gICAgICAgICAgICAgICAgICAgIG1pbmltdW1SZXN1bHRzRm9yU2VhcmNoOiAtMSxcbiAgICAgICAgICAgICAgICAgICAgdGVtcGxhdGVTZWxlY3Rpb246IGlCYWxsLFxuICAgICAgICAgICAgICAgICAgICB0ZW1wbGF0ZVJlc3VsdDogaUJhbGwsXG4gICAgICAgICAgICAgICAgICAgIGRyb3Bkb3duUGFyZW50OiAkcGFyZW50LFxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gaUJhbGwoY29sb3IpIHtcbiAgICAgICAgICAgIGxldCAkcGFyZW50ID0gJChjb2xvci5lbGVtZW50KS5jbG9zZXN0KCcuc2VsZWN0LWNvbG9yJyk7XG4gICAgICAgICAgICBsZXQgJG9yaWdpbmFsT3B0aW9uID0gY29sb3IuZWxlbWVudDtcbiAgICAgICAgICAgIGxldCBjb2xvckJhbGwgPSAkKCRvcmlnaW5hbE9wdGlvbikuZGF0YSgnY29sb3InKTtcblxuICAgICAgICAgICAgaWYgKHR5cGVvZiBjb2xvckJhbGwgIT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgICAgICAgICBpZiAoY29sb3IudGV4dC5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgJHBhcmVudC5yZW1vdmVDbGFzcygnc2VsZWN0LWNvbG9yLS1wYWxldHRlJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuICQoXG4gICAgICAgICAgICAgICAgICAgICAgICBgPGRpdiBjbGFzcz1zZWxlY3QtY29sb3JfX2l0ZW0+IDxzcGFuIGNsYXNzPVwic2VsZWN0LWNvbG9yX19tYXJrZXJcIiBzdHlsZT1cImJhY2tncm91bmQtY29sb3I6ICR7Y29sb3JCYWxsfVwiPjwvc3Bhbj48cD4gJHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb2xvci50ZXh0XG4gICAgICAgICAgICAgICAgICAgICAgICB9IDwvcD48L2Rpdj5gXG4gICAgICAgICAgICAgICAgICAgICk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgJHBhcmVudC5hZGRDbGFzcygnc2VsZWN0LWNvbG9yLS1wYWxldHRlJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuICQoXG4gICAgICAgICAgICAgICAgICAgICAgICBgPGRpdiBjbGFzcz1zZWxlY3QtY29sb3JfX2l0ZW0+IDxzcGFuIGNsYXNzPVwic2VsZWN0LWNvbG9yX19iYWxsXCIgc3R5bGU9XCJiYWNrZ3JvdW5kLWNvbG9yOiAke2NvbG9yQmFsbH0gXCI+IDwvc3Bhbj4gPC9kaXY+YFxuICAgICAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuICQoYDxkaXYgY2xhc3M9c2VsZWN0LWNvbG9yX19pdGVtPiAke2NvbG9yLnRleHR9IDwvZGl2PmApO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gX2luaXRCb3JuKCkge1xuICAgICAgICBsZXQgJGJvcm5TZWxlY3QgPSAkZG9jdW1lbnQuZmluZCgnLmpzLXNlbGVjdC1ib3JuJyk7XG5cbiAgICAgICAgaWYgKCRib3JuU2VsZWN0Lmxlbmd0aCkge1xuICAgICAgICAgICAgaWYgKCQod2luZG93KS53aWR0aCgpID4gNDgwKSB7XG4gICAgICAgICAgICAgICAgJGJvcm5TZWxlY3QuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCEkKHRoaXMpLmhhc0NsYXNzKCdzZWxlY3QyLWhpZGRlbi1hY2Nlc3NpYmxlJykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuc2VsZWN0Mih7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbGFuZ3VhZ2U6ICdydScsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbWluaW11bVJlc3VsdHNGb3JTZWFyY2g6IC0xLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGFsbG93Q2xlYXI6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkYm9yblNlbGVjdC5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICBpZiAoJCh0aGlzKS5oYXNDbGFzcygnc2VsZWN0Mi1oaWRkZW4tYWNjZXNzaWJsZScpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnNlbGVjdDIoJ2Rlc3Ryb3knKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBsZXQgJHBhcmVudCA9ICQodGhpcykuY2xvc2VzdCgnLmJiLWlucHV0LS1ib3JuJyk7XG4gICAgICAgICAgICAgICAgICAgIGxldCAkc2VsZWN0ID0gJCh0aGlzKS5jbG9zZXN0KCcuYmItaW5wdXQtYm9ybl9fc2VsZWN0Jyk7XG4gICAgICAgICAgICAgICAgICAgIGxldCBwbGFjZWhvbGRlciA9ICQodGhpcykuZGF0YSgncGxhY2Vob2xkZXInKTtcbiAgICAgICAgICAgICAgICAgICAgbGV0ICRmaXJzdE9wdGlvbiA9ICQodGhpcykuZmluZCgnb3B0aW9uOmZpcnN0LWNoaWxkJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCRwYXJlbnQuaGFzQ2xhc3MoJ2JiLWlucHV0LS10cmFuc2Zvcm0nKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJHBhcmVudC5maW5kKCcuYmItaW5wdXRfX3RpdGxlJykuZGV0YWNoKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICRwYXJlbnRcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnLmJiLWlucHV0LWJvcm4nKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnYmItaW5wdXQtYm9ybi0tdHJhbnNmb3JtJyk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAkZmlyc3RPcHRpb25cbiAgICAgICAgICAgICAgICAgICAgICAgIC50ZXh0KHBsYWNlaG9sZGVyKVxuICAgICAgICAgICAgICAgICAgICAgICAgLnZhbChwbGFjZWhvbGRlcilcbiAgICAgICAgICAgICAgICAgICAgICAgIC5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQXR0cignZGF0YS1wbGFjZWhvbGRlcicpO1xuXG4gICAgICAgICAgICAgICAgICAgICQodGhpcykud3JhcCgnPGxhYmVsIGNsYXNzPVwiYmItc2VsZWN0XCI+Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5vbignY2hhbmdlJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoJCh0aGlzKS52YWwoKSAhPT0gJycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkc2VsZWN0LmFkZENsYXNzKCdpcy1mb2N1cycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkc2VsZWN0LnJlbW92ZUNsYXNzKCdpcy1mb2N1cycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAvLyBmdW5jdGlvbiBjaGVja0ZvY3VzKCkge1xuICAgICAgICAgICAgICAgICAgICAvLyAgICAgaWYgKCQodGhpcykudmFsKCkgIT09ICcnKSB7XG4gICAgICAgICAgICAgICAgICAgIC8vICAgICAgICAgJHNlbGVjdC5hZGRDbGFzcygnaXMtZm9jdXMnKTtcbiAgICAgICAgICAgICAgICAgICAgLy8gICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIC8vICAgICAgICAgJHNlbGVjdC5yZW1vdmVDbGFzcygnaXMtZm9jdXMnKTtcbiAgICAgICAgICAgICAgICAgICAgLy8gICAgIH1cbiAgICAgICAgICAgICAgICAgICAgLy8gfVxuICAgICAgICAgICAgICAgICAgICAvLyBjaGVja0ZvY3VzKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgX2FkZFJlc2V0QnRuKCQodGhpcykpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gX2luaXRTaG93WWVhcigpIHtcbiAgICAgICAgJGRvY3VtZW50Lm9uKCdjbGljaycsICcuanMtc2V0LXllYXInLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICQodGhpcykuaGlkZSgpO1xuICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgIC5wcmV2KClcbiAgICAgICAgICAgICAgICAuc2hvdygpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdEhpZGVZZWFyKCkge1xuICAgICAgICBsZXQgJHllYXJTZWxlY3QgPSAkKCcuanMtc2VsZWN0LWJvcm4tLWNsZWFyJyk7XG5cbiAgICAgICAgJHllYXJTZWxlY3RcbiAgICAgICAgICAgIC5vbignc2VsZWN0Mjp1bnNlbGVjdGluZycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICQodGhpcykub24oJ3NlbGVjdDI6b3BlbmluZycsIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSlcbiAgICAgICAgICAgIC5vbignc2VsZWN0Mjp1bnNlbGVjdCcsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLm9mZignc2VsZWN0MjpvcGVuaW5nJyk7XG4gICAgICAgICAgICAgICAgfSwgMTAwKTtcbiAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAub24oJ2NoYW5nZScsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGlmIChcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS52YWwoKSA9PSAnJyAmJlxuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmF0dHIoJ2RhdGEtYm9ybicpID09PSAneWVhcidcbiAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAgICAgJCgnLmpzLXNldC15ZWFyJykuc2hvdygpO1xuICAgICAgICAgICAgICAgICAgICAkKCcuanMtc2V0LXllYXInKVxuICAgICAgICAgICAgICAgICAgICAgICAgLnByZXYoKVxuICAgICAgICAgICAgICAgICAgICAgICAgLmhpZGUoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfYWRkUmVzZXRCdG4oZWwpIHtcbiAgICAgICAgbGV0IHJlbmRlckljb24gPSB0cnVlO1xuICAgICAgICBsZXQgJHNlbGVjdCA9IGVsO1xuICAgICAgICBsZXQgJHBhcmVudCA9ICRzZWxlY3QuY2xvc2VzdCgnLmJiLWlucHV0Jyk7XG4gICAgICAgIGxldCByZXNldEJ0biA9IGA8YnV0dG9uIGNsYXNzPVwiYmItc2VsZWN0X19yZXNldCBqcy1zZWxlY3QtLXJlc2V0XCI+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPHNwYW4+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxzdmcgdmlld0JveD1cIjAgMCA0Ny45NzEgNDcuOTcxXCI+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9XCJNMjguMjI4LDIzLjk4Nkw0Ny4wOTIsNS4xMjJjMS4xNzItMS4xNzEsMS4xNzItMy4wNzEsMC00LjI0MmMtMS4xNzItMS4xNzItMy4wNy0xLjE3Mi00LjI0MiwwTDIzLjk4NiwxOS43NDRMNS4xMjEsMC44OFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYy0xLjE3Mi0xLjE3Mi0zLjA3LTEuMTcyLTQuMjQyLDBjLTEuMTcyLDEuMTcxLTEuMTcyLDMuMDcxLDAsNC4yNDJsMTguODY1LDE4Ljg2NEwwLjg3OSw0Mi44NWMtMS4xNzIsMS4xNzEtMS4xNzIsMy4wNzEsMCw0LjI0MlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgQzEuNDY1LDQ3LjY3NywyLjIzMyw0Ny45NywzLDQ3Ljk3czEuNTM1LTAuMjkzLDIuMTIxLTAuODc5bDE4Ljg2NS0xOC44NjRMNDIuODUsNDcuMDkxYzAuNTg2LDAuNTg2LDEuMzU0LDAuODc5LDIuMTIxLDAuODc5XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzMS41MzUtMC4yOTMsMi4xMjEtMC44NzljMS4xNzItMS4xNzEsMS4xNzItMy4wNzEsMC00LjI0MkwyOC4yMjgsMjMuOTg2elwiLz5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9zdmc+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9zcGFuPlxuICAgICAgICAgICAgICAgICAgICAgICAgPC9idXR0b24+YDtcblxuICAgICAgICBsZXQgJG5ld09wdGlvbiA9ICQoJzxvcHRpb24+JykuYXR0cih7XG4gICAgICAgICAgICBkaXNhYmxlZDogJ2Rpc2FibGVkJyxcbiAgICAgICAgICAgIHNlbGVjdGVkOiAnc2VsZWN0ZWQnLFxuICAgICAgICB9KTtcblxuICAgICAgICAkc2VsZWN0Lm9uKCdjaGFuZ2UnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGxldCAkcGFyZW50ID0gJCh0aGlzKS5wYXJlbnQoJy5iYi1zZWxlY3QsIC5iYi1pbnB1dC1ib3JuX19zZWxlY3QnKTtcblxuICAgICAgICAgICAgaWYgKHJlbmRlckljb24pIHtcbiAgICAgICAgICAgICAgICBpZiAoJCh3aW5kb3cpLndpZHRoKCkgPiA0ODApIHtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAgICAgLnBhcmVudCgpXG4gICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnLnNlbGVjdDItc2VsZWN0aW9uX19jbGVhcicpXG4gICAgICAgICAgICAgICAgICAgICAgICAudGV4dCgnJylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5hcHBlbmQocmVzZXRCdG4pO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICRwYXJlbnQuYXBwZW5kKHJlc2V0QnRuKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgcmVuZGVySWNvbiA9IGZhbHNlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICAkZG9jdW1lbnQub24oJ2NsaWNrJywgJy5qcy1zZWxlY3QtLXJlc2V0JywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgbGV0ICRwYXJlbnQsICRzZWxlY3Q7XG5cbiAgICAgICAgICAgIGlmICgkKHRoaXMpLnNpYmxpbmdzKCcuanMtc2VsZWN0LWJvcm4nKS5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAkc2VsZWN0ID0gJCh0aGlzKS5zaWJsaW5ncygnLmpzLXNlbGVjdC1ib3JuJyk7XG4gICAgICAgICAgICAgICAgJHBhcmVudCA9ICQodGhpcykuY2xvc2VzdCgnLmJiLWlucHV0LWJvcm5fX3NlbGVjdCcpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkc2VsZWN0ID0gJCh0aGlzKS5zaWJsaW5ncygnLmpzLXNlbGVjdC1uYXRpdmUnKTtcbiAgICAgICAgICAgICAgICAkcGFyZW50ID0gJCh0aGlzKS5jbG9zZXN0KCcuYmItaW5wdXQnKTtcblxuICAgICAgICAgICAgICAgIGlmICgkcGFyZW50Lmhhc0NsYXNzKCdiYi1pbnB1dC0tdHJhbnNmb3JtJykpIHtcbiAgICAgICAgICAgICAgICAgICAgJG5ld09wdGlvbi5wcmVwZW5kVG8oJHNlbGVjdCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAkc2VsZWN0LnZhbCgkcGFyZW50LmZpbmQoJ29wdGlvbjpmaXJzdC1jaGlsZCcpLnZhbCgpKS5ibHVyKCk7XG5cbiAgICAgICAgICAgICRwYXJlbnQucmVtb3ZlQ2xhc3MoJ2lzLWZvY3VzJyk7XG5cbiAgICAgICAgICAgICQodGhpcykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgIGlmICgkcGFyZW50Lmhhc0NsYXNzKCdiYi1pbnB1dC1ib3JuX19zZWxlY3QtLXllYXInKSkge1xuICAgICAgICAgICAgICAgICRwYXJlbnQubmV4dCgnLmJiLWlucHV0LWJvcm5fX2J0bicpLnNob3coKTtcbiAgICAgICAgICAgICAgICAkcGFyZW50LmhpZGUoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmVuZGVySWNvbiA9IHRydWU7XG5cbiAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIF9pbml0UGhvbmVDb2RlKCkge1xuICAgICAgICAvL0NoYW5nZSBzZWxlY3QgcmVzdWx0cyB0byBvcHRpb24gdmFsdWVcbiAgICAgICAgZnVuY3Rpb24gc2VsZWN0Q29kZVNlbGVjdGlvbihvcHQpIHtcbiAgICAgICAgICAgIHZhciBvcHRWYWwgPSAkKG9wdC5lbGVtZW50KS52YWwoKTtcblxuICAgICAgICAgICAgcmV0dXJuICQoXG4gICAgICAgICAgICAgICAgJzxzcGFuIGNsYXNzPWN1c3RvbS1zZWxlY3RfX3Jlc3VsdHM+JyArIG9wdFZhbCArICc8L3NwYW4+J1xuICAgICAgICAgICAgKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vQWRkIGNpdHkgbmFtZSB0byBvcHRpb25cbiAgICAgICAgZnVuY3Rpb24gc2VsZWN0Q29kZVJlc3VsdChvcHQpIHtcbiAgICAgICAgICAgIHZhciBjb3VudHJ5ID0gJChvcHQuZWxlbWVudCkuZGF0YSgnY291bnRyeScpLFxuICAgICAgICAgICAgICAgIG9wdFZhbCA9ICQob3B0LmVsZW1lbnQpLnZhbCgpO1xuXG4gICAgICAgICAgICByZXR1cm4gJChcbiAgICAgICAgICAgICAgICAnPGRpdiBjbGFzcz1jdXN0b20tc2VsZWN0X19yZXN1bHRzPicgK1xuICAgICAgICAgICAgICAgICAgICAnPHNwYW4+JyArXG4gICAgICAgICAgICAgICAgICAgIGNvdW50cnkgK1xuICAgICAgICAgICAgICAgICAgICAnPC9zcGFuPicgK1xuICAgICAgICAgICAgICAgICAgICAnPHNwYW4+JyArXG4gICAgICAgICAgICAgICAgICAgIG9wdFZhbCArXG4gICAgICAgICAgICAgICAgICAgICc8L3NwYW4+JyArXG4gICAgICAgICAgICAgICAgICAgICc8L2Rpdj4nXG4gICAgICAgICAgICApO1xuICAgICAgICB9XG5cbiAgICAgICAgbGV0ICRwaG9uZUNvZGVCb3ggPSAkZG9jdW1lbnQuZmluZCgnLmpzLWlucHV0LXBob25lLWNvZGUnKTtcblxuICAgICAgICBpZiAoJHBob25lQ29kZUJveC5sZW5ndGgpIHtcbiAgICAgICAgICAgICRwaG9uZUNvZGVCb3guZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBsZXQgJHNlbGVjdCA9ICQodGhpcykuZmluZCgnLnNlbGVjdC12YWx1ZScpO1xuICAgICAgICAgICAgICAgIGxldCAkcGFyZW50ID0gJCh0aGlzKS5wYXJlbnQoKTtcbiAgICAgICAgICAgICAgICBsZXQgJGlucHV0ID0gJCh0aGlzKS5maW5kKCcuYmItaW5wdXRfX2lucHV0Jyk7XG5cbiAgICAgICAgICAgICAgICBpZiAoJHdpbmRvdy53aWR0aCgpID49IDc2OCkge1xuICAgICAgICAgICAgICAgICAgICAkc2VsZWN0XG4gICAgICAgICAgICAgICAgICAgICAgICAuc2VsZWN0Mih7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbGFuZ3VhZ2U6ICdydScsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGVtcGxhdGVSZXN1bHQ6IHNlbGVjdENvZGVSZXN1bHQsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGVtcGxhdGVTZWxlY3Rpb246IHNlbGVjdENvZGVTZWxlY3Rpb24sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZHJvcGRvd25QYXJlbnQ6ICQodGhpcyksXG4gICAgICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgICAgICAgICAgLm9uKCdzZWxlY3QyOnNlbGVjdCcsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLnBhcmVudCgpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5wYXJlbnQoKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnaW5wdXQnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZm9jdXMoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICRwYXJlbnRcbiAgICAgICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnYmItc2VsZWN0JylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5hcHBlbmQoJzxkaXYgY2xhc3M9XCJiYi1pbnB1dC0tc2VsZWN0LXZhbHVlXCI+PC9kaXY+Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgbGV0IG9wdGlvblNlbGVjdCA9ICRwYXJlbnQuZmluZCgnb3B0aW9uJyk7XG4gICAgICAgICAgICAgICAgICAgIGxldCBzZWxlY3RWYWx1ZSA9ICRwYXJlbnQuZmluZCgnLmJiLWlucHV0LS1zZWxlY3QtdmFsdWUnKTtcblxuICAgICAgICAgICAgICAgICAgICBzZWxlY3RWYWx1ZS50ZXh0KG9wdGlvblNlbGVjdC5lcSgwKS52YWwoKSk7XG5cbiAgICAgICAgICAgICAgICAgICAgJHNlbGVjdC5jaGFuZ2UoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBsZXQgY291bnRlciA9ICQodGhpcylbMF0uc2VsZWN0ZWRJbmRleDtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNlbGVjdFZhbHVlLnRleHQob3B0aW9uU2VsZWN0LmVxKGNvdW50ZXIpLnZhbCgpKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5wYXJlbnQoKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5wYXJlbnQoKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5maW5kKCdpbnB1dCcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmZvY3VzKCk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICRpbnB1dC5pbnB1dG1hc2soe1xuICAgICAgICAgICAgICAgICAgICBtYXNrOiAnKDk5OSkgOTk5LTk5LTk5JyxcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICRpbnB1dC5vbignZm9jdXMnLCBhZGRGb2N1cykub24oJ2JsdXInLCByZW1vdmVGb2N1cyk7XG4gICAgICAgICAgICAgICAgJHNlbGVjdFxuICAgICAgICAgICAgICAgICAgICAub24oJ3NlbGVjdDI6b3BlbicsIGFkZEZvY3VzKVxuICAgICAgICAgICAgICAgICAgICAub24oJ3NlbGVjdDI6Y2xvc2UnLCByZW1vdmVGb2N1cyk7XG5cbiAgICAgICAgICAgICAgICBmdW5jdGlvbiBhZGRGb2N1cygpIHtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAgICAgLmNsb3Nlc3QoJy5qcy1pbnB1dC1waG9uZS1jb2RlJylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnaXMtZm9jdXMnKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBmdW5jdGlvbiByZW1vdmVGb2N1cygpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCQodGhpcykudmFsKCkgPT0gJycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuY2xvc2VzdCgnLmpzLWlucHV0LXBob25lLWNvZGUnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnaXMtZm9jdXMnKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gX2luaXRNb2JpbGUoKSB7XG4gICAgICAgIGxldCAkc2VsZWN0ID0gJGRvY3VtZW50LmZpbmQoJy5qcy1tb3ZlLXNlbGVjdCcpO1xuXG4gICAgICAgICRzZWxlY3QuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGxldCAkaW5wdXRTZWFyY2ggPSAkKHRoaXMpLmZpbmQoJy5tb3ZlLXNlbGVjdF9fZmllbGQnKTtcbiAgICAgICAgICAgIGxldCAkcmVzdWx0SXRlbSA9ICQodGhpcykuZmluZCgnLm1vdmUtc2VsZWN0X19yZXN1bHQnKTtcbiAgICAgICAgICAgIGxldCAkaXRlbSA9ICQodGhpcykuZmluZCgnLm1vdmUtc2VsZWN0X19yZXN1bHQnKTtcbiAgICAgICAgICAgIGxldCAkYnRuQ2xvc2UgPSAkKHRoaXMpLmZpbmQoJy5qcy1tb3ZlLXNlbGVjdC0tY2xvc2UnKTtcblxuICAgICAgICAgICAgJCh0aGlzKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdpcy1hY3RpdmUnKTtcbiAgICAgICAgICAgICAgICAkaW5wdXRTZWFyY2guYmx1cigpO1xuICAgICAgICAgICAgICAgICQoJ2h0bWwsIGJvZHknKS5hbmltYXRlKHtcbiAgICAgICAgICAgICAgICAgICAgc2Nyb2xsVG9wOiAwLFxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICRpdGVtLm9uKCdjbGljaycsIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgICAgICBsZXQgJG5hbWUgPSAkKHRoaXMpXG4gICAgICAgICAgICAgICAgICAgIC5maW5kKCcudXNlcl9fbmFtZScpXG4gICAgICAgICAgICAgICAgICAgIC50ZXh0KClcbiAgICAgICAgICAgICAgICAgICAgLnRyaW0oKTtcblxuICAgICAgICAgICAgICAgIGxldCAkc2VydmljZSA9ICQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy5pdGVtLWluZm9fX3RpdGxlIHNwYW4nKVxuICAgICAgICAgICAgICAgICAgICAudGV4dCgpXG4gICAgICAgICAgICAgICAgICAgIC50cmltKClcbiAgICAgICAgICAgICAgICAgICAgLnNwbGl0KCcgJylcbiAgICAgICAgICAgICAgICAgICAgLmpvaW4oJyArICcpO1xuXG4gICAgICAgICAgICAgICAgJGlucHV0U2VhcmNoLnZhbCgkbmFtZSB8fCAkc2VydmljZSk7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAgICAgICAgIC5jbG9zZXN0KCcuanMtbW92ZS1zZWxlY3QnKVxuICAgICAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ2lzLWFjdGl2ZScpXG4gICAgICAgICAgICAgICAgICAgIC5jbG9zZXN0KCcuYmItaW5wdXQtLXRyYW5zZm9ybScpXG4gICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnaXMtZm9jdXMnKTtcblxuICAgICAgICAgICAgICAgIC8vIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICRidG5DbG9zZS5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAgICAgICAgIC5jbG9zZXN0KCcuanMtbW92ZS1zZWxlY3QnKVxuICAgICAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ2lzLWFjdGl2ZScpO1xuICAgICAgICAgICAgICAgICRpbnB1dFNlYXJjaC5ibHVyKCk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5tb3ZlLXNlbGVjdF9fcmVzdWx0JywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgJHJlc3VsdEl0ZW0ucmVtb3ZlQ2xhc3MoJ2lzLXNlbGVjdGVkJyk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnaXMtc2VsZWN0ZWQnKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdElucHV0Q3VzdG9tKCkge1xuICAgICAgICBsZXQgaW5wdXRDdXN0b20gPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCcuanMtYmItaW5wdXQtY3VzdG9tJyk7XG5cbiAgICAgICAgaWYgKGlucHV0Q3VzdG9tLmxlbmd0aCkge1xuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBpbnB1dEN1c3RvbS5sZW5ndGg7IGkrKykge1xuICAgICAgICAgICAgICAgIGxldCBpbnB1dFZhbHVlID0gaW5wdXRDdXN0b21baV0ucXVlcnlTZWxlY3RvcihcbiAgICAgICAgICAgICAgICAgICAgJy5iYi1pbnB1dC1jdXN0b21fX3ZhbHVlJ1xuICAgICAgICAgICAgICAgICk7XG5cbiAgICAgICAgICAgICAgICBpbnB1dEN1c3RvbVtpXS5vbmNsaWNrID0gZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgICAgIGlucHV0VmFsdWUub25jbGljayA9IGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCBlID0gZXZlbnQudGFyZ2V0O1xuICAgICAgICAgICAgICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChcbiAgICAgICAgICAgICAgICAgICAgICAgICFlXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmNsb3Nlc3QoJy5iYi1pbnB1dC1jdXN0b20nKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5jbGFzc0xpc3QuY29udGFpbnMoJ2lzLW9wZW4nKVxuICAgICAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgaW5wdXRDdXN0b20ubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpbnB1dEN1c3RvbVtpXS5jbGFzc0xpc3QucmVtb3ZlKCdpcy1vcGVuJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICBpbnB1dEN1c3RvbVtpXS5jbGFzc0xpc3QuYWRkKCdpcy1vcGVuJyk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpbnB1dEN1c3RvbVtpXS5jbGFzc0xpc3QucmVtb3ZlKCdpcy1vcGVuJyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAgICAgZG9jdW1lbnQub25jbGljayA9IGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCB0YXJnZXQgPSBldmVudC50YXJnZXQ7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKFxuICAgICAgICAgICAgICAgICAgICAgICAgdGFyZ2V0LnF1ZXJ5U2VsZWN0b3JBbGwoJy5qcy1iYi1pbnB1dC1jdXN0b20nKS5sZW5ndGggfHxcbiAgICAgICAgICAgICAgICAgICAgICAgICF0YXJnZXQuY2xvc2VzdCgnLmJiLWlucHV0LWN1c3RvbV9fdmFsdWUnKVxuICAgICAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgaW5wdXRDdXN0b20ubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpbnB1dEN1c3RvbVtpXS5jbGFzc0xpc3QucmVtb3ZlKCdpcy1vcGVuJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gX2luaXRJbnB1dEN1c3RvbUNvbnRyb2woKSB7XG4gICAgICAgIGxldCBpbnB1dEN1c3RvbSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJy5qcy1iYi1pbnB1dC1jdXN0b20nKTtcblxuICAgICAgICBpZiAoaW5wdXRDdXN0b20ubGVuZ3RoKSB7XG4gICAgICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IGlucHV0Q3VzdG9tLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICAgICAgbGV0IG1pbnVzID0gaW5wdXRDdXN0b21baV0ucXVlcnlTZWxlY3RvcihcbiAgICAgICAgICAgICAgICAgICAgJy5iYi1pbnB1dC1jdXN0b21fX21pbnVzJ1xuICAgICAgICAgICAgICAgICk7XG4gICAgICAgICAgICAgICAgbGV0IHBsdXMgPSBpbnB1dEN1c3RvbVtpXS5xdWVyeVNlbGVjdG9yKFxuICAgICAgICAgICAgICAgICAgICAnLmJiLWlucHV0LWN1c3RvbV9fcGx1cydcbiAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgIGxldCBpbnB1dCA9IGlucHV0Q3VzdG9tW2ldLnF1ZXJ5U2VsZWN0b3IoXG4gICAgICAgICAgICAgICAgICAgICcuYmItaW5wdXQtY3VzdG9tX19pbnB1dCdcbiAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgIGxldCBpdGVtID0gaW5wdXRDdXN0b21baV0ucXVlcnlTZWxlY3RvckFsbChcbiAgICAgICAgICAgICAgICAgICAgJy5iYi1pbnB1dC1jdXN0b21fX2l0ZW0nXG4gICAgICAgICAgICAgICAgKTtcblxuICAgICAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgaXRlbS5sZW5ndGg7IGkrKykge1xuICAgICAgICAgICAgICAgICAgICBsZXQgb2ZmZXIgPSBpdGVtW2ldLnF1ZXJ5U2VsZWN0b3IoXG4gICAgICAgICAgICAgICAgICAgICAgICAnLmJiLWlucHV0LWN1c3RvbV9fb2ZmZXInXG4gICAgICAgICAgICAgICAgICAgICk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKG9mZmVyICE9IG51bGwpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGl0ZW1baV0ub25jbGljayA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlucHV0LnZhbHVlID0gb2ZmZXIuaW5uZXJIVE1MO1xuICAgICAgICAgICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmIChtaW51cyAhPSBudWxsICYmIHBsdXMgIT0gbnVsbCkge1xuICAgICAgICAgICAgICAgICAgICBtaW51cy5vbmNsaWNrID0gZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgbGV0IHRhcmdldCA9IGUudGFyZ2V0O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB0YXJnZXRcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuY2xvc2VzdCgnLmJiLWlucHV0LWN1c3RvbScpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmNsYXNzTGlzdC5yZW1vdmUoJ2lzLW9wZW4nKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGlucHV0LnZhbHVlID4gMSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlucHV0LnZhbHVlLS07XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlucHV0LnZhbHVlID0gMTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgICAgICAgICAgcGx1cy5vbmNsaWNrID0gZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgbGV0IHRhcmdldCA9IGUudGFyZ2V0O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB0YXJnZXRcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuY2xvc2VzdCgnLmJiLWlucHV0LWN1c3RvbScpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmNsYXNzTGlzdC5yZW1vdmUoJ2lzLW9wZW4nKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlucHV0LnZhbHVlKys7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gX2luaXRTZWxlY3RDb2xvcigpIHtcbiAgICAgICAgbGV0IGlucHV0Q3VzdG9tID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLmpzLWJiLWlucHV0LWN1c3RvbScpO1xuICAgICAgICBpZiAoaW5wdXRDdXN0b20ubGVuZ3RoKSB7XG4gICAgICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IGlucHV0Q3VzdG9tLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICAgICAgbGV0IHZhbHVlID0gaW5wdXRDdXN0b21baV0ucXVlcnlTZWxlY3RvcihcbiAgICAgICAgICAgICAgICAgICAgJy5iYi1pbnB1dC1jdXN0b21fX2NvbG9yJ1xuICAgICAgICAgICAgICAgICk7XG4gICAgICAgICAgICAgICAgbGV0IHRpdGxlID0gaW5wdXRDdXN0b21baV0ucXVlcnlTZWxlY3RvcihcbiAgICAgICAgICAgICAgICAgICAgJy5iYi1pbnB1dC1jdXN0b21fX3RpdGxlJ1xuICAgICAgICAgICAgICAgICk7XG5cbiAgICAgICAgICAgICAgICBpZiAodmFsdWUgIT0gbnVsbCAmJiB0aXRsZSAhPSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCBpdGVtID0gaW5wdXRDdXN0b21baV0ucXVlcnlTZWxlY3RvckFsbChcbiAgICAgICAgICAgICAgICAgICAgICAgICcuYmItaW5wdXQtY3VzdG9tX19pdGVtJ1xuICAgICAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IGl0ZW0ubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGxldCBjb2xvciA9IGl0ZW1baV0ucXVlcnlTZWxlY3RvcihcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAnLmJiLWlucHV0LWN1c3RvbV9fY29sb3InXG4gICAgICAgICAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgICAgICAgICAgbGV0IHRpdGxlUGljayA9IGl0ZW1baV0ucXVlcnlTZWxlY3RvcihcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAnLmJiLWlucHV0LWN1c3RvbV9fdGl0bGUnXG4gICAgICAgICAgICAgICAgICAgICAgICApLmlubmVySFRNTDtcbiAgICAgICAgICAgICAgICAgICAgICAgIGxldCBkYXRhID0gY29sb3IuZ2V0QXR0cmlidXRlKCdkYXRhLWNvbG9yLWlucHV0Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGl0ZW1baV0ub25jbGljayA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRpdGxlLmlubmVySFRNTCA9IHRpdGxlUGljaztcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZS5zdHlsZS5iYWNrZ3JvdW5kQ29sb3IgPSBkYXRhO1xuICAgICAgICAgICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cblxuICAgIHJldHVybiB7XG4gICAgICAgIGluaXQ6IF9pbml0LFxuICAgICAgICBpbml0U2VydmljZXM6IF9pbml0U2VydmljZXMsXG4gICAgICAgIGluaXROYXRpdmU6IF9pbml0TmF0aXZlLFxuICAgICAgICBpbml0TXVsdGlwbGU6IF9pbml0TXVsdGlwbGUsXG4gICAgICAgIGluaXRDb2xvcjogX2luaXRDb2xvcixcbiAgICAgICAgaW5pdEljb246IF9pbml0SWNvbixcbiAgICAgICAgaW5pdEJvcm46IF9pbml0Qm9ybixcbiAgICAgICAgaW5pdFNob3dZZWFyOiBfaW5pdFNob3dZZWFyLFxuICAgICAgICBpbml0SGlkZVllYXI6IF9pbml0SGlkZVllYXIsXG4gICAgICAgIGluaXRQaG9uZUNvZGU6IF9pbml0UGhvbmVDb2RlLFxuICAgICAgICBpbml0TW9iaWxlOiBfaW5pdE1vYmlsZSxcbiAgICAgICAgaW5pdEFkZEV2ZW50TGlzdGVuZXI6IF9pbml0QWRkRXZlbnRMaXN0ZW5lcixcbiAgICAgICAgaW5pdElucHV0Q3VzdG9tOiBfaW5pdElucHV0Q3VzdG9tLFxuICAgICAgICBpbml0SW5wdXRDdXN0b21Db250cm9sOiBfaW5pdElucHV0Q3VzdG9tQ29udHJvbCxcbiAgICAgICAgaW5pdFNlbGVjdENvbG9yOiBfaW5pdFNlbGVjdENvbG9yLFxuICAgIH07XG59KSgpO1xuXG4vKipcbiAqIEJhc2UuQ29tcG9uZW50cy5TbGlkZXIuanNcbiAqXG4gKiBAYXV0aG9yIEFudG9uIFVzdGlub2ZmIDxhLmEudXN0aW5vZmZAZ21haWwuY29tPlxuICovXG5cbkJhc2UuZGVmaW5lKCdDb21wb25lbnRzLlNsaWRlcicpO1xuXG5CYXNlLkNvbXBvbmVudHMuU2xpZGVyID0gKGZ1bmN0aW9uKCkge1xuICAgIGZ1bmN0aW9uIF9pbml0KCkge1xuICAgICAgICBfaW5pdFNsaWNrKCk7XG4gICAgICAgIF9pbml0U3dpcGVyKCk7XG4gICAgICAgIF9pbml0UHJldmlld1NsaWRlcigpO1xuICAgICAgICBfaW5pdFRyaXVtcGhTbGlkZXIoKTtcbiAgICAgICAgX2luaXRDYXRhbG9nSXRlbVNsaWRlcigpO1xuICAgICAgICBfcmVpbml0QWZ0ZXJPcGVuUG9wdXAoKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdFNsaWNrKCkge1xuICAgICAgICBsZXQgJHNsaWRlciA9ICQoJy5qcy1iYi1zbGlkZXInKTtcbiAgICAgICAgaWYgKCRzbGlkZXIubGVuZ3RoKSB7XG4gICAgICAgICAgICAkc2xpZGVyLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgbGV0ICRzbGlkcyA9ICQodGhpcykuZmluZCgnLmJiLXNsaWRlcl9fc2xpZGVzJyk7XG4gICAgICAgICAgICAgICAgbGV0ICRzbGlkZSA9ICQodGhpcykuZmluZCgnLmJiLXNsaWRlcl9fc2xpZGUnKTtcbiAgICAgICAgICAgICAgICBsZXQgJHByZXZBcnJvdyA9ICQodGhpcykuZmluZCgnLmJiLXNsaWRlcl9fYXJyb3ctLXByZXYnKTtcbiAgICAgICAgICAgICAgICBsZXQgJG5leHRBcnJvdyA9ICQodGhpcykuZmluZCgnLmJiLXNsaWRlcl9fYXJyb3ctLW5leHQnKTtcblxuICAgICAgICAgICAgICAgIGlmICgkc2xpZGUubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgICRzbGlkcy5ub3QoJy5zbGljay1pbml0aWFsaXplZCcpLnNsaWNrKHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHByZXZBcnJvdzogJHByZXZBcnJvdyxcbiAgICAgICAgICAgICAgICAgICAgICAgIG5leHRBcnJvdzogJG5leHRBcnJvdyxcbiAgICAgICAgICAgICAgICAgICAgICAgIGF1dG9wbGF5OiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgYXV0b3BsYXlTcGVlZDogMjAwMCxcbiAgICAgICAgICAgICAgICAgICAgICAgIHNwZWVkOiAxMDAwLFxuICAgICAgICAgICAgICAgICAgICAgICAgc2xpZGVzVG9TaG93OiAzLFxuICAgICAgICAgICAgICAgICAgICAgICAgc2xpZGVzVG9TY3JvbGw6IDEsXG4gICAgICAgICAgICAgICAgICAgICAgICBpbmZpbml0ZTogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGFycm93czogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGRvdHM6IGZhbHNlLFxuXG4gICAgICAgICAgICAgICAgICAgICAgICByZXNwb25zaXZlOiBbXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVha3BvaW50OiA0ODEsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNldHRpbmdzOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzbGlkZXNUb1Nob3c6IDEsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBkb3RzOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYXJyb3dzOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgXSxcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdFN3aXBlcigpIHtcbiAgICAgICAgbGV0IHNsaWRlciA9ICcuanMtc3dpcGVyLXNsaWRlcic7XG4gICAgICAgIGxldCAkc2xpZGVyID0gJChzbGlkZXIpO1xuXG4gICAgICAgIGlmICgkc2xpZGVyLmxlbmd0aCkge1xuICAgICAgICAgICAgJHNsaWRlci5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGxldCBzbGlkZXMgPSAkKHRoaXMpLmRhdGEoJ3NsaWRlci1zbGlkZXMnKTtcbiAgICAgICAgICAgICAgICBsZXQgb2Zmc2V0ID0gJCh0aGlzKS5kYXRhKCdzbGlkZXItb2Zmc2V0Jyk7XG4gICAgICAgICAgICAgICAgbGV0IGZyZWVNb2RlID0gJCh0aGlzKS5kYXRhKCdzbGlkZXItZnJlZS1tb2RlJyk7XG4gICAgICAgICAgICAgICAgbGV0IG1vdXNlV2hlZWwgPSAkKHRoaXMpLmRhdGEoJ3NsaWRlci1tb3VzZS13aGVlbCcpO1xuICAgICAgICAgICAgICAgIGxldCByYXRpbyA9ICQodGhpcykuZGF0YSgnc2xpZGVyLXJhdGlvJyk7XG4gICAgICAgICAgICAgICAgbGV0IGNlbnRlciA9ICQodGhpcykuZGF0YSgnc2xpZGVyLWNlbnRlcicpO1xuXG4gICAgICAgICAgICAgICAgbGV0IHNldHRpbmdzID0ge1xuICAgICAgICAgICAgICAgICAgICBwYWdpbmF0aW9uOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBlbDogJCh0aGlzKS5maW5kKCcuc3dpcGVyLXBhZ2luYXRpb24nKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGNsaWNrYWJsZTogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgICAgICAgICBuYXZpZ2F0aW9uOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBuZXh0RWw6ICQodGhpcykuZmluZCgnLnN3aXBlci1idXR0b24tbmV4dCcpLFxuICAgICAgICAgICAgICAgICAgICAgICAgcHJldkVsOiAkKHRoaXMpLmZpbmQoJy5zd2lwZXItYnV0dG9uLXByZXYnKSxcbiAgICAgICAgICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgICAgICAgICBjZW50ZXJJbnN1ZmZpY2llbnRTbGlkZXM6IGNlbnRlciA/IHRydWUgOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgc2xpZGVzUGVyVmlldzogc2xpZGVzID8gc2xpZGVzIDogMSxcbiAgICAgICAgICAgICAgICAgICAgc3BhY2VCZXR3ZWVuOiBvZmZzZXQgPyBvZmZzZXQgOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgZnJlZU1vZGU6IGZyZWVNb2RlID8gdHJ1ZSA6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICBtb3VzZXdoZWVsOiBtb3VzZVdoZWVsID8gdHJ1ZSA6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICBsYXp5OiB0cnVlLFxuXG4gICAgICAgICAgICAgICAgICAgIGJyZWFrcG9pbnRzOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAvLyAzMjA6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vICAgICBzbGlkZXNQZXJWaWV3OiBzbGlkZXMgPyAoc2xpZGVzIC8gcmF0aW8pICogMyA6IDEsXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgNDgwOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2xpZGVzUGVyVmlldzogc2xpZGVzID8gKHNsaWRlcyAvIHJhdGlvKSAqIDIgOiAxLFxuICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIDc2ODoge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNsaWRlc1BlclZpZXc6IHNsaWRlcyA/IHJhdGlvIC8gMi42NiA6IDEsXG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gMTAyNDoge1xuICAgICAgICAgICAgICAgICAgICAgICAgLy8gICAgIHNsaWRlc1BlclZpZXc6IHNsaWRlcyA/IChzbGlkZXMgLyByYXRpbykgKiAyIDogMSxcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIH0sXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgICAgIC8vICQodGhpcykuc3dpcGVyKHNldHRpbmdzKTtcblxuICAgICAgICAgICAgICAgIG5ldyBTd2lwZXIodGhpcywgc2V0dGluZ3MpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdFByZXZpZXdTbGlkZXIoKSB7XG4gICAgICAgIGxldCBzbGlkZXIgPSAnLmpzLXN3aXBlci1zbGlkZXItLXByZXZpZXcnO1xuICAgICAgICBsZXQgJHNsaWRlciA9ICQoc2xpZGVyKTtcblxuICAgICAgICBpZiAoJHNsaWRlci5sZW5ndGggJiYgJCh3aW5kb3cpLndpZHRoKCkgPiA0ODApIHtcbiAgICAgICAgICAgIGxldCBzZXR0aW5ncyA9IHtcbiAgICAgICAgICAgICAgICB0b3VjaFJhdGlvOiAwLjUsXG4gICAgICAgICAgICAgICAgdG91Y2hBbmdsZTogMzAsXG4gICAgICAgICAgICAgICAgc2xpZGVzUGVyVmlldzogNC43NSxcbiAgICAgICAgICAgICAgICBzcGFjZUJldHdlZW46IDIsXG4gICAgICAgICAgICAgICAgZnJlZU1vZGU6IHRydWUsXG4gICAgICAgICAgICAgICAgbW91c2V3aGVlbDogdHJ1ZSxcbiAgICAgICAgICAgICAgICBsYXp5OiB0cnVlLFxuICAgICAgICAgICAgICAgIGJyZWFrcG9pbnRzOiB7XG4gICAgICAgICAgICAgICAgICAgIDMyMDoge1xuICAgICAgICAgICAgICAgICAgICAgICAgc2xpZGVzUGVyVmlldzogMS41LFxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICA0ODA6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNsaWRlc1BlclZpZXc6IDIuNSxcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgMTAyNDoge1xuICAgICAgICAgICAgICAgICAgICAgICAgc2xpZGVzUGVyVmlldzogNC41LFxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICBuZXcgU3dpcGVyKHNsaWRlciwgc2V0dGluZ3MpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gX2luaXRUcml1bXBoU2xpZGVyKCkge1xuICAgICAgICBsZXQgJHNsaWRlciA9ICQoJy5qcy1iYi1zbGlkZXItLXRyaXVtcGgnKTtcblxuICAgICAgICBpZiAoJHNsaWRlci5sZW5ndGgpIHtcbiAgICAgICAgICAgICRzbGlkZXIuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBsZXQgJHNsaWRlcyA9ICQodGhpcykuZmluZCgnLmJiLXNsaWRlcl9fc2xpZGVzJyk7XG4gICAgICAgICAgICAgICAgbGV0ICRzbGlkZSA9ICQodGhpcykuZmluZCgnLmJiLXNsaWRlcl9fc2xpZGUnKTtcbiAgICAgICAgICAgICAgICBsZXQgJHBhcmVudCA9ICQodGhpcykuY2xvc2VzdCgnLnRyaXVtcGgnKTtcbiAgICAgICAgICAgICAgICBsZXQgJGJ0bk5leHQgPSAkcGFyZW50LmZpbmQoJy5qcy1iYi1zbGlkZXItYnRuLS1uZXh0Jyk7XG5cbiAgICAgICAgICAgICAgICBpZiAoJHNsaWRlLmxlbmd0aCA+IDEpIHtcbiAgICAgICAgICAgICAgICAgICAgJHNsaWRlcy5zbGljayh7XG4gICAgICAgICAgICAgICAgICAgICAgICBzbGlkZXNUb1Nob3c6IDEsXG4gICAgICAgICAgICAgICAgICAgICAgICBzbGlkZXNUb1Njcm9sbDogMSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGFycm93czogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgICAgICBkb3RzOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgdG91Y2hNb3ZlOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGluZmluaXRlOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5vbignYWZ0ZXJDaGFuZ2UnLCBmdW5jdGlvbihcbiAgICAgICAgICAgICAgICAgICAgZXZlbnQsXG4gICAgICAgICAgICAgICAgICAgIHNsaWNrLFxuICAgICAgICAgICAgICAgICAgICBjdXJyZW50U2xpZGUsXG4gICAgICAgICAgICAgICAgICAgIG5leHRTbGlkZVxuICAgICAgICAgICAgICAgICkge1xuICAgICAgICAgICAgICAgICAgICBpZiAoY3VycmVudFNsaWRlICsgMSA9PT0gc2xpY2suc2xpZGVDb3VudCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJGJ0bk5leHRcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAudGV4dCgn0JPQvtGC0L7QstC+JylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ2pzLWJiLXNsaWRlci1idG4tLW5leHQnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5hdHRyKCdkYXRhLWRpc21pc3MnLCAnbW9kYWwnKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICRidG5OZXh0Lm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRzbGlkZXMuc2xpY2soJ3NsaWNrTmV4dCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICRidG5OZXh0Lm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAkc2xpZGVzLnNsaWNrKCdzbGlja05leHQnKTtcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vRGlzYWJsZSBjaGFuZ2Ugc2xpZGUgb24gY2xpY2sgZG90c1xuICAgICAgICAgICAgICAgICRzbGlkZXIuZmluZCgnLnNsaWNrLWRvdHMgbGkgYnV0dG9uJykub24oJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBfaW5pdENhdGFsb2dJdGVtU2xpZGVyKCkge1xuICAgICAgICBsZXQgJGNhdGFsb2dJdGVtU2xpZGVyID0gJGRvY3VtZW50LmZpbmQoJy5qcy1jYXRhbG9nLWl0ZW0tc2xpZGVyJyk7XG5cbiAgICAgICAgaWYgKCRjYXRhbG9nSXRlbVNsaWRlci5sZW5ndGgpIHtcbiAgICAgICAgICAgICRjYXRhbG9nSXRlbVNsaWRlci5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGxldCBfdGhpcyA9ICQodGhpcyk7XG4gICAgICAgICAgICAgICAgbGV0ICRzbGlkZXMgPSAkKHRoaXMpLmZpbmQoJy5iYi1zbGlkZXJfX3NsaWRlcycpO1xuICAgICAgICAgICAgICAgIGxldCAkc2xpZGUgPSAkKHRoaXMpLmZpbmQoJy5iYi1zbGlkZXJfX3NsaWRlJyk7XG4gICAgICAgICAgICAgICAgbGV0ICRzbGlkZXJEb3RzID0gJCh0aGlzKS5maW5kKCcuYmItc2xpZGVyX19kb3RzJyk7XG4gICAgICAgICAgICAgICAgJHNsaWRlckRvdHMuaGlkZSgpO1xuXG4gICAgICAgICAgICAgICAgX3RoaXNcbiAgICAgICAgICAgICAgICAgICAgLm9uKCdpbml0JywgZnVuY3Rpb24oZXZlbnQsIHNsaWNrKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkc2xpZGVyRG90cy5wcmVwZW5kKFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIFwiPHNwYW4gY2xhc3M9J2JiLXNsaWRlcl9fcGFnZXIgYmItc2xpZGVyX19wYWdlci0tbm93Jz4xPC9zcGFuPlwiICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJy8nXG4gICAgICAgICAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgICAgICAgICAgJHNsaWRlckRvdHMuYXBwZW5kKFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIFwiPHNwYW4gY2xhc3M9J2JiLXNsaWRlcl9fcGFnZXIgYmItc2xpZGVyX19wYWdlci0tdG90YWwnPlwiICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2xpY2suc2xpZGVDb3VudCArXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICc8L3NwYW4+J1xuICAgICAgICAgICAgICAgICAgICAgICAgKTtcbiAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAgICAgLm9uKCdhZnRlckNoYW5nZScsIGZ1bmN0aW9uKFxuICAgICAgICAgICAgICAgICAgICAgICAgZXZlbnQsXG4gICAgICAgICAgICAgICAgICAgICAgICBzbGljayxcbiAgICAgICAgICAgICAgICAgICAgICAgIGN1cnJlbnRTbGlkZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIG5leHRTbGlkZVxuICAgICAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBpID0gKGN1cnJlbnRTbGlkZSA/IGN1cnJlbnRTbGlkZSA6IDApICsgMTtcbiAgICAgICAgICAgICAgICAgICAgICAgIF90aGlzLmZpbmQoJy5iYi1zbGlkZXJfX3BhZ2VyLS1ub3cnKS5odG1sKGkpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIGlmICgkc2xpZGUubGVuZ3RoID4gMSkge1xuICAgICAgICAgICAgICAgICAgICAkc2xpZGVyRG90cy5zaG93KCk7XG5cbiAgICAgICAgICAgICAgICAgICAgJHNsaWRlcy5ub3QoJy5zbGljay1pbml0aWFsaXplZCcpLnNsaWNrKHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGxhenlMb2FkOiAnb25kZW1hbmQnLFxuICAgICAgICAgICAgICAgICAgICAgICAgc3BlZWQ6IDQwMCxcbiAgICAgICAgICAgICAgICAgICAgICAgIHNsaWRlc1RvU2hvdzogMSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHNsaWRlc1RvU2Nyb2xsOiAxLFxuICAgICAgICAgICAgICAgICAgICAgICAgYXJyb3dzOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgaW5maW5pdGU6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICAgICAgZG90czogZmFsc2UsXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3BvbnNpdmU6IFtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrcG9pbnQ6IDQ4MSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2V0dGluZ3M6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGFycm93czogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIF0sXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBpZiAoJCh3aW5kb3cpLndpZHRoKCkgPiA0ODApIHtcbiAgICAgICAgICAgICAgICAkKCcuanMtY2F0YWxvZy1pdGVtJylcbiAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy5iYi1zbGlkZXJfX3NsaWRlcycpXG4gICAgICAgICAgICAgICAgICAgIC5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoJCh0aGlzKS5oYXNDbGFzcygnc2xpY2staW5pdGlhbGl6ZWQnKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8vUmVpbml0IHNsaWRlciBhZnRlciBwb3B1cCBvcGVuXG4gICAgZnVuY3Rpb24gX3JlaW5pdEFmdGVyT3BlblBvcHVwKCkge1xuICAgICAgICAkKCcubW9kYWwnKS5vbignc2hvd24uYnMubW9kYWwnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGxldCAkc2xpZGVyID0gJCh0aGlzKVxuICAgICAgICAgICAgICAgIC5maW5kKCcuYmItc2xpZGVyX19zbGlkZXMnKVxuICAgICAgICAgICAgICAgIC5maWx0ZXIoJy5zbGljay1pbml0aWFsaXplZCcpO1xuICAgICAgICAgICAgaWYgKCRzbGlkZXIubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgJHNsaWRlclswXS5zbGljay5zZXRQb3NpdGlvbigpO1xuICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAkc2xpZGVyLmFkZENsYXNzKCdpcy12aXNpYmxlJyk7XG4gICAgICAgICAgICAgICAgfSwgNTApO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICByZXR1cm4ge1xuICAgICAgICBpbml0OiBfaW5pdCxcbiAgICAgICAgaW5pdFNsaWNrOiBfaW5pdFNsaWNrLFxuICAgICAgICBpbml0U3dpcGVyOiBfaW5pdFN3aXBlcixcbiAgICAgICAgaW5pdFByZXZpZXdTbGlkZXI6IF9pbml0UHJldmlld1NsaWRlcixcbiAgICAgICAgaW5pdFRyaXVtcGhTbGlkZXI6IF9pbml0VHJpdW1waFNsaWRlcixcbiAgICAgICAgaW5pdENhdGFsb2dJdGVtU2xpZGVyOiBfaW5pdENhdGFsb2dJdGVtU2xpZGVyLFxuICAgICAgICByZWluaXRBZnRlck9wZW5Qb3B1cDogX3JlaW5pdEFmdGVyT3BlblBvcHVwLFxuICAgIH07XG59KSgpO1xuXG5cbi8qIGh0bWwgZXhhbXBsZSBib3ggY29sb3JcbiAgICA8ZGl2IGRhdGEtY29sb3I9XCIjYTVkZmY4XCI+PC9kaXY+XG4qL1xuY2xhc3MgQm94Q29sb3Ige1xuICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICB0aGlzLmVsZW1lbnQgPSAkKCdbZGF0YS1jb2xvcl0nKTtcbiAgICAgICAgdGhpcy5jb2xvcjtcbiAgICAgICAgdGhpcy5pbml0KCk7XG4gICAgfVxuICAgIGluaXQoKSB7XG4gICAgICAgIHRoaXMuZWxlbWVudC5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgbGV0IF90aGlzID0gJCh0aGlzKTtcbiAgICAgICAgICAgIGxldCBjb2xvciA9ICQodGhpcykuYXR0cignZGF0YS1jb2xvcicpO1xuICAgICAgICAgICAgbGV0IGNvbG9ySXRlbSA9ICQodGhpcykuZmluZCgnW2RhdGEtY29sb3ItaXRlbV0nKTtcblxuICAgICAgICAgICAgLy/QodC+0LfQtNCw0LXQvCDQsdC70L7QuiDRgSDRhtCy0LXRgtC+0Lwg0LrQsNGC0LXQs9C+0YDQuNC4XG4gICAgICAgICAgICBsZXQgJGNvbG9yQmxvY2sgPSAkKCc8c3Bhbj4nKVxuICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnY29sb3ItbGluZScpXG4gICAgICAgICAgICAgICAgLmNzcygnYmFja2dyb3VuZC1jb2xvcicsIGNvbG9yKTtcblxuICAgICAgICAgICAgaWYgKGNvbG9ySXRlbS5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICBpZiAoIWNvbG9ySXRlbS5maW5kKCcuY29sb3ItbGluZScpLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICAkY29sb3JCbG9jay5wcmVwZW5kVG8oY29sb3JJdGVtKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGlmICghY29sb3JJdGVtLmZpbmQoJy5jb2xvci1saW5lJykubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgICRjb2xvckJsb2NrLnByZXBlbmRUbyhfdGhpcyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9XG59XG5cbmNsYXNzIFBhcmFsbGF4QmxvY2sge1xuICAgIGNvbnN0cnVjdG9yKGFyZ3MpIHtcbiAgICAgICAgdGhpcy5zZWxlY3RvciA9ICQoYXJncy5zZWxlY3Rvcik7XG4gICAgICAgIHRoaXMudHJhbnNmb3JtID0gYXJncy50cmFuc2Zvcm07XG4gICAgfVxuXG4gICAgaW5pdCgpIHtcbiAgICAgICAgaWYgKHR5cGVvZiB0aGlzLnNlbGVjdG9yICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgICAgdGhpcy5wYXJhbGxheCgpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgcGFyYWxsYXgoKSB7XG4gICAgICAgIHRoaXMuc2VsZWN0b3IuZWFjaCgoaW5kZXgsIGVsZW0pID0+IHtcbiAgICAgICAgICAgIC8vIGxldCBpID0gaW5kZXg7XG4gICAgICAgICAgICBsZXQgX3RoaXMgPSAkKGVsZW0pO1xuICAgICAgICAgICAgbGV0IHNwZWVkID0gJChlbGVtKS5kYXRhKCdwYXJhbGxheC1zcGVlZCcpIHx8IDE1O1xuXG4gICAgICAgICAgICAkKGRvY3VtZW50KS5vbignc2Nyb2xsJywgZSA9PiB7XG4gICAgICAgICAgICAgICAgbGV0IHNjcm9sbCA9ICQoZS50YXJnZXQpLnNjcm9sbFRvcCgpO1xuICAgICAgICAgICAgICAgIGxldCBjb3JkcyA9IHNjcm9sbCAvIHNwZWVkO1xuICAgICAgICAgICAgICAgIGlmICh0aGlzLnRyYW5zZm9ybSkge1xuICAgICAgICAgICAgICAgICAgICBsZXQgdHJhbnNmb3JtID0gX3RoaXMuY3NzKCd0cmFuc2Zvcm0nKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIG1hdHJpeCA9IHRyYW5zZm9ybVxuICAgICAgICAgICAgICAgICAgICAgICAgLnNwbGl0KCcoJylbMV1cbiAgICAgICAgICAgICAgICAgICAgICAgIC5zcGxpdCgnKScpWzBdXG4gICAgICAgICAgICAgICAgICAgICAgICAuc3BsaXQoJywnKVxuICAgICAgICAgICAgICAgICAgICAgICAgLm1hcChwYXJzZUZsb2F0KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgX3RoaXMuY3NzKFxuICAgICAgICAgICAgICAgICAgICAndHJhbnNmb3JtJyxcbiAgICAgICAgICAgICAgICAgICAgYG1hdHJpeCgke21hdHJpeFswXX0sICR7bWF0cml4WzFdfSwgJHttYXRyaXhbMl19LCAke21hdHJpeFszXX0sICR7XG4gICAgICAgICAgICAgICAgICAgICAgICBtYXRyaXhbNF1cbiAgICAgICAgICAgICAgICAgICAgfSwgLSR7Y29yZHN9KWBcbiAgICAgICAgICAgICAgICApO1xuXG4gICAgICAgICAgICAgICAgLy8gaWYgKGkgJiAxKSB7XG4gICAgICAgICAgICAgICAgLy8gICAgIF90aGlzLmNzcygndHJhbnNmb3JtJywgYHRyYW5zbGF0ZVkoJHtjb3Jkc30pYCk7XG4gICAgICAgICAgICAgICAgLy8gfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAvLyAgICAgX3RoaXMuY3NzKCd0cmFuc2Zvcm0nLCBgdHJhbnNsYXRlWSgtJHtjb3Jkc30pYCk7XG4gICAgICAgICAgICAgICAgLy8gfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuICAgIH1cbn1cblxuLyogaHRtbCBleGFtcGxlXG48ZGl2IGNsYXNzPVwiYmItZHJvcGRvd24gYmItZHJvcGRvd24tLXNtYWxsIGJiLWRyb3Bkb3duLS10cmFuc2Zvcm0gYmItZHJvcGRvd24tLWhvdmVyIGpzLWJiLWRyb3Bkb3duXCIgZGF0YS1kcm9wZG93bi1wb3NpdGlvbj1cInJpZ2h0XCI+XG5cdDxkaXYgY2xhc3M9XCJiYi1kcm9wZG93bl9fbGlzdFwiPlxuXHQ8L2Rpdj5cbjwvZGl2PlxuKi9cblxuY29uc3QgRHJvcGRvd24gPSAoZnVuY3Rpb24oKSB7XG4gICAgbGV0ICRodG1sID0gJCgnaHRtbCcpO1xuICAgIGxldCAkb3ZlcmxheSA9ICQoJy5qcy1vdmVybGF5Jyk7XG5cbiAgICBsZXQgZHJvcGRvd24gPSB7fTtcbiAgICBsZXQgJGRyb3Bkb3duID0gJChkb2N1bWVudCkuZmluZCgnLmpzLWJiLWRyb3Bkb3duJyk7XG4gICAgbGV0ICRidG5Ecm9wZG93bkNsb3NlID0gJChcbiAgICAgICAgYDxidXR0b24gY2xhc3M9XCJidG4taWNvbiBidG4taWNvbi0tY2xvc2UganMtYmItZHJvcGRvd24tLWNsb3NlXCI+PHN2ZyB4bWxucz1cImh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnXCIgeG1sbnM6eGxpbms9XCJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rXCIgY2xhc3M9XCJpY29uIGljb24tY2xvc2VcIiB2aWV3Qm94PVwiMCAwIDUxMiA1MTJcIj48cGF0aCBkPVwiTTUwNS45NDMsNi4wNThjLTguMDc3LTguMDc3LTIxLjE3Mi04LjA3Ny0yOS4yNDksMEw2LjA1OCw0NzYuNjkzYy04LjA3Nyw4LjA3Ny04LjA3NywyMS4xNzIsMCwyOS4yNDlcbiAgICAgICAgQzEwLjA5Niw1MDkuOTgyLDE1LjM5LDUxMiwyMC42ODMsNTEyYzUuMjkzLDAsMTAuNTg2LTIuMDE5LDE0LjYyNS02LjA1OUw1MDUuOTQzLDM1LjMwNlxuICAgICAgICBDNTE0LjAxOSwyNy4yMyw1MTQuMDE5LDE0LjEzNSw1MDUuOTQzLDYuMDU4elwiLz5cbiAgICAgICAgICAgIDxwYXRoIGQ9XCJNNTA1Ljk0Miw0NzYuNjk0TDM1LjMwNiw2LjA1OWMtOC4wNzYtOC4wNzctMjEuMTcyLTguMDc3LTI5LjI0OCwwYy04LjA3Nyw4LjA3Ni04LjA3NywyMS4xNzEsMCwyOS4yNDhsNDcwLjYzNiw0NzAuNjM2XG4gICAgICAgIGM0LjAzOCw0LjAzOSw5LjMzMiw2LjA1OCwxNC42MjUsNi4wNThjNS4yOTMsMCwxMC41ODctMi4wMTksMTQuNjI0LTYuMDU3QzUxNC4wMTgsNDk3Ljg2Niw1MTQuMDE4LDQ4NC43NzEsNTA1Ljk0Miw0NzYuNjk0elwiLz48L3N2Zz48L2J1dHRvbj5gXG4gICAgKTtcbiAgICBsZXQgJGJ0bkZsb2F0aW5nID0gJChkb2N1bWVudCkuZmluZCgnLmpzLWJ0bi1mbG9hdGluZycpO1xuICAgIGxldCBBQ1RJVkVfQ0xBU1MgPSAnaXMtYWN0aXZlJztcbiAgICBsZXQgVklTSUJMRV9DTEFTUyA9ICdpcy12aXNpYmxlJztcbiAgICBsZXQgRFJPUERPV05fT1dFUkxBWV9DTEFTUyA9ICdvdmVybGF5LS1kcm9wZG93bic7XG4gICAgbGV0IF90aGlzLCAkbGlzdDtcbiAgICBsZXQgb3BlbiA9IGZhbHNlO1xuXG4gICAgbGV0IHN0eWxlRGVzY3RvcCA9IHtcbiAgICAgICAgZGlzcGxheTogJ2Jsb2NrJyxcbiAgICAgICAgcG9zaXRpb246ICdmaXhlZCcsXG4gICAgICAgIGZpbHRlcjogJ25vbmUnLFxuICAgICAgICBvcGFjaXR5OiAxLFxuICAgICAgICAncG9pbnRlci1ldmVudHMnOiAnYXV0bycsXG4gICAgICAgIHZpc2liaWxpdHk6ICd2aXNpYmxlJyxcbiAgICB9O1xuXG4gICAgbGV0IHN0eWxlVHJhbnNmb3JtTWVudSA9IHtcbiAgICAgICAgZGlzcGxheTogJ2Jsb2NrJyxcbiAgICAgICAgcG9zaXRpb246ICdmaXhlZCcsXG4gICAgICAgIHRvcDogJ2F1dG8nLFxuICAgICAgICBib3R0b206IDEwLFxuICAgICAgICBsZWZ0OiAxMCxcbiAgICAgICAgcmlnaHQ6IDEwLFxuICAgICAgICB6SW5kZXg6IDk5OTksXG4gICAgfTtcblxuICAgIGxldCBzdHlsZVRyYW5zZm9ybUluZm8gPSB7XG4gICAgICAgIGRpc3BsYXk6ICdibG9jaycsXG4gICAgICAgIHBvc2l0aW9uOiAnZml4ZWQnLFxuICAgICAgICBib3R0b206IDAsXG4gICAgICAgIGxlZnQ6IDAsXG4gICAgICAgIHJpZ2h0OiAwLFxuICAgICAgICB6SW5kZXg6IDk5OTksXG4gICAgfTtcblxuICAgIGRyb3Bkb3duLmluaXQgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgaWYgKCRkcm9wZG93bi5sZW5ndGgpIHtcbiAgICAgICAgICAgIGlmICgkKHdpbmRvdykud2lkdGgoKSA8PSA3NjgpIHtcbiAgICAgICAgICAgICAgICAkZHJvcGRvd24ucmVtb3ZlQ2xhc3MoJ2JiLWRyb3Bkb3duLS1ob3ZlcicpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZHJvcGRvd24uZXZlbnRzKCk7XG4gICAgICAgICAgICBkcm9wZG93bi50cmFuc2Zvcm0oKTtcbiAgICAgICAgICAgIGRyb3Bkb3duLnJlbmRlcigpO1xuICAgICAgICB9XG4gICAgfTtcblxuICAgIC8v0JTQvtCx0LDQstC70Y/QtdC8INC4INC+0YLRgNC40YHQvtCy0YvQstCw0LXQvCDQvNC+0LHQuNC70YzQvdGL0Lkg0LTRgNC+0L/QtNCw0YPQvVxuICAgIGRyb3Bkb3duLnJlbmRlciA9IGZ1bmN0aW9uKCkge1xuICAgICAgICBpZiAoJCh3aW5kb3cpLndpZHRoKCkgPD0gNzY4KSB7XG4gICAgICAgICAgICBsZXQgJGRyb3Bkb3duID0gJChkb2N1bWVudCkuZmluZChcbiAgICAgICAgICAgICAgICAnLmpzLWJiLWRyb3Bkb3duLmJiLWRyb3Bkb3duLS10cmFuc2Zvcm0nXG4gICAgICAgICAgICApO1xuICAgICAgICAgICAgJGRyb3Bkb3duLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgbGV0ICRidG5DbG9zZSA9ICQoXG4gICAgICAgICAgICAgICAgICAgICc8YnV0dG9uIGNsYXNzPVwiYmItZHJvcGRvd25fX2Nsb3NlIGpzLWJiLWRyb3Bkb3duLS1jbG9zZVwiPtCX0LDQutGA0YvRgtGMPC9idXR0b24+J1xuICAgICAgICAgICAgICAgICk7XG4gICAgICAgICAgICAgICAgbGV0ICRkcm9wZG93bk92ZXJsYXkgPSAkKCc8ZGl2IGNsYXNzPVwiYmItZHJvcGRvd25fX292ZXJsYXlcIj4nKTtcblxuICAgICAgICAgICAgICAgIGxldCAkZHJvcGRvd25MaXN0ID0gJCh0aGlzKS5maW5kKCcuYmItZHJvcGRvd25fX2xpc3QnKTtcblxuICAgICAgICAgICAgICAgICRidG5DbG9zZS5hcHBlbmRUbygkZHJvcGRvd25MaXN0KTtcbiAgICAgICAgICAgICAgICAkZHJvcGRvd25PdmVybGF5Lmluc2VydEFmdGVyKCRkcm9wZG93bkxpc3QpO1xuICAgICAgICAgICAgICAgICRkcm9wZG93bkxpc3QuZmluZCgnLmluZm8tYmxvY2tfX2ljb24nKS5yZW1vdmUoKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgfTtcblxuICAgIC8v0K3QstC10L3RgtGLINC60LvQuNC60L7QsiDQv9C+INC+0YHQvdC+0LLQvdGL0Lwg0Y3Qu9C10LzQtdC90YLQsNC8XG4gICAgZHJvcGRvd24uZXZlbnRzID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtYmItZHJvcGRvd24nLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICBfdGhpcyA9ICQodGhpcyk7XG4gICAgICAgICAgICAkbGlzdCA9ICQodGhpcykuZmluZCgnLmJiLWRyb3Bkb3duX19saXN0Jyk7XG5cbiAgICAgICAgICAgIC8v0JDQutGC0LjQstC40YDRg9C10Lwg0L7QstC10YDQu9C10LksINC10YHQu9C4INC60LvQuNC6INC90LUg0L/QvtC60LvQsNGB0YHRgyBiYi1kcm9wZG93bi0tYW5vdGhlclxuICAgICAgICAgICAgaWYgKCEkKHRoaXMpLmhhc0NsYXNzKCdiYi1kcm9wZG93bi0tYW5vdGhlcicpKSB7XG4gICAgICAgICAgICAgICAgJG92ZXJsYXkuYWRkQ2xhc3MoRFJPUERPV05fT1dFUkxBWV9DTEFTUyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmICgkKHdpbmRvdykud2lkdGgoKSA+IDc2OCkge1xuICAgICAgICAgICAgICAgIGRyb3Bkb3duLl90b2dnbGUoJCh0aGlzKSk7XG4gICAgICAgICAgICAgICAgLy8gZHJvcGRvd24uX3NldFN0eWxlKCQodGhpcykpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBpZiAoISQodGhpcykuaGFzQ2xhc3MoJ2JiLWRyb3Bkb3duLS1hbm90aGVyJykpIHtcbiAgICAgICAgICAgICAgICAgICAgJGh0bWwuYWRkQ2xhc3MoJ25vLXRvdWNoLWV2ZW50cycpO1xuICAgICAgICAgICAgICAgICAgICAkYnRuRmxvYXRpbmcuZmFkZU91dCgpO1xuICAgICAgICAgICAgICAgICAgICBvcGVuID0gdHJ1ZTtcblxuICAgICAgICAgICAgICAgICAgICAvL9CQ0L3QuNC80LDRhtC40Y8g0LLRi9C/0LDQtNCw0Y7RidC10LPQviDRgdC/0LjRgdC60LBcbiAgICAgICAgICAgICAgICAgICAgJGxpc3QuaW5zZXJ0QWZ0ZXIoJy53cmFwcGVyJyk7XG4gICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgJGxpc3QuYWRkQ2xhc3MoVklTSUJMRV9DTEFTUyk7XG4gICAgICAgICAgICAgICAgICAgIH0sIDIwMCk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCQodGhpcykuaGFzQ2xhc3MoJ2JiLWRyb3Bkb3duLS10cmFuc2Zvcm0nKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJGxpc3QuY3NzKHN0eWxlVHJhbnNmb3JtTWVudSkuYWRkQ2xhc3MoJ190cmFuc2Zvcm0nKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICRidG5Ecm9wZG93bkNsb3NlLnByZXBlbmRUbygkbGlzdCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkbGlzdFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5jc3Moc3R5bGVUcmFuc2Zvcm1JbmZvKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnX3RyYW5zZm9ybV9pbmZvJyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBkcm9wZG93bi5fdG9nZ2xlKCQodGhpcykpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy9Ub2dnbGUgZml4ZWQgY2xhc3MgZnJvbSBib2R5XG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtYmItZHJvcGRvd24ucmVxdWVzdC1pbmZvJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgaWYgKCQod2luZG93KS53aWR0aCgpIDw9IDQ4MCkge1xuICAgICAgICAgICAgICAgIGlmICghb3Blbikge1xuICAgICAgICAgICAgICAgICAgICAkKCdodG1sJykuYWRkQ2xhc3MoJ2lzLWZpeGVkJyk7XG4gICAgICAgICAgICAgICAgICAgIG9wZW4gPSB0cnVlO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICQoJ2h0bWwnKS5yZW1vdmVDbGFzcygnaXMtZml4ZWQnKTtcbiAgICAgICAgICAgICAgICAgICAgb3BlbiA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy/QodC60YDRi9Cy0LDQtdC8INC00YDQvtC/0LTQsNGD0L0g0L/QviDQutC70LjQutGDINCyINC90LUg0LHQu9C+0LrQsFxuICAgICAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICBpZiAoJChlLnRhcmdldCkuY2xvc2VzdCgnLmpzLWJiLWRyb3Bkb3duJykubGVuZ3RoKSByZXR1cm47XG4gICAgICAgICAgICAkZHJvcGRvd24ucmVtb3ZlQ2xhc3MoQUNUSVZFX0NMQVNTKTtcbiAgICAgICAgICAgIG9wZW4gPSBmYWxzZTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy/Qn9C+INC60LvQuNC60YMg0L3QsCDQvtCy0LXRgNC70LXQuSDRgdC60YDRi9Cy0LDQtdC8INC00YDQvtC/0LTQsNGD0L1cbiAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5vdmVybGF5LS1kcm9wZG93bicsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgb3BlbiA9IGZhbHNlO1xuICAgICAgICAgICAgJGRyb3Bkb3duLnJlbW92ZUNsYXNzKEFDVElWRV9DTEFTUyk7XG4gICAgICAgICAgICBkcm9wZG93bi5fY2xvc2UoKTtcbiAgICAgICAgICAgICRidG5GbG9hdGluZy5mYWRlSW4oKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgbGV0IGxpbmsgPVxuICAgICAgICAgICAgJy5iYi1kcm9wZG93bl9fbGlzdCAuaW5mby1ibG9ja19faXRlbSwgLmJiLWRyb3Bkb3duX19saXN0IC5pbmZvLWJsb2NrX19saW5rJztcblxuICAgICAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCBsaW5rLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICAkZHJvcGRvd24ucmVtb3ZlQ2xhc3MoQUNUSVZFX0NMQVNTKTtcbiAgICAgICAgICAgIGRyb3Bkb3duLl9jbG9zZSgpO1xuICAgICAgICAgICAgJGJ0bkZsb2F0aW5nLmZhZGVJbigpO1xuICAgICAgICB9KTtcblxuICAgICAgICAvL9Cf0L4g0LrQu9C40LrRgyDQvdCwINC60L3QvtC/0LrRgyDQt9Cw0LrRgNGL0YLRjCDRgdC60YDRi9Cy0LDQtdC8INC00YDQvtC/0LTQsNGD0L1cbiAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5qcy1iYi1kcm9wZG93bi0tY2xvc2UnLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICAkYnRuRmxvYXRpbmcuZmFkZUluKCk7XG4gICAgICAgICAgICBkcm9wZG93bi5fY2xvc2UoKTtcbiAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICBkcm9wZG93bi50cmFuc2Zvcm0gPSBmdW5jdGlvbigpIHtcbiAgICAgICAgaWYgKCQod2luZG93KS53aWR0aCgpIDw9IDQ4MCkge1xuICAgICAgICAgICAgJChkb2N1bWVudClcbiAgICAgICAgICAgICAgICAuZmluZCgnLmpzLWJiLWRyb3Bkb3duJylcbiAgICAgICAgICAgICAgICAuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCQodGhpcykuaXMoJ1tkYXRhLWRyb3Bkb3duLXRyYW5zZm9ybV0nKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgbGV0ICRsaXN0ID0gJCh0aGlzKS5maW5kKCcuYmItZHJvcGRvd25fX2xpc3QnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ2JiLWRyb3Bkb3duLS1hbm90aGVyJylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2JiLWRyb3Bkb3duLS10cmFuc2Zvcm0nKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICRsaXN0LmZpbmQoJ3VsJykuYWRkQ2xhc3MoJ2luZm8tYmxvY2tfX2xpc3QnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICRsaXN0LmZpbmQoJ2xpJykuYWRkQ2xhc3MoJ2luZm8tYmxvY2tfX2l0ZW0nKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICRsaXN0LmZpbmQoJ2EnKS5hZGRDbGFzcygnaW5mby1ibG9ja19fbGluaycpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgLy8gZHJvcGRvd24uX3NldFN0eWxlID0gZnVuY3Rpb24oZWwpIHtcbiAgICAvLyAgICAgbGV0IGRQb3NpdGlvbiA9IGVsLmRhdGEoJ3Bvc2l0aW9uJyk7XG4gICAgLy8gICAgIGxldCB3V2lkdGggPSAkKHdpbmRvdykud2lkdGgoKTtcbiAgICAvLyAgICAgbGV0IHdIZWlnaHQgPSAkKHdpbmRvdykuaGVpZ2h0KCk7XG4gICAgLy8gICAgIGxldCBwb3NpdGlvbiA9IGVsLm9mZnNldCgpO1xuXG4gICAgLy8gICAgIGxldCB0b3AgPSBwb3NpdGlvbi50b3AgKyBlbC5vdXRlckhlaWdodCh0cnVlKTtcbiAgICAvLyAgICAgbGV0IGxlZnQ7XG5cbiAgICAvLyAgICAgbGVmdCA9IHdXaWR0aCAtICRsaXN0LndpZHRoKCkgKiAyO1xuXG4gICAgLy8gICAgIGlmIChwb3NpdGlvbi5sZWZ0ID4gd1dpZHRoIC8gMikge1xuICAgIC8vICAgICAgICAgc3R5bGVEZXNjdG9wLmxlZnQgPSBsZWZ0ICsgJ3B4JztcbiAgICAvLyAgICAgfSBlbHNlIHtcbiAgICAvLyAgICAgICAgIHN0eWxlRGVzY3RvcC5sZWZ0ID0gcG9zaXRpb24ubGVmdDtcbiAgICAvLyAgICAgfVxuXG4gICAgLy8gICAgIHN0eWxlRGVzY3RvcC50b3AgPSB0b3AgKyAncHgnO1xuICAgIC8vICAgICBzdHlsZURlc2N0b3AucmlnaHQgPSAnYXV0byc7XG5cbiAgICAvLyAgICAgY29uc29sZS5sb2coJy0tLSBzdHlsZURlc2N0b3AnLCBzdHlsZURlc2N0b3ApO1xuXG4gICAgLy8gICAgIGlmICghb3Blbikge1xuICAgIC8vICAgICAgICAgJGxpc3QuY3NzKHN0eWxlRGVzY3RvcCk7XG4gICAgLy8gICAgICAgICBjb25zb2xlLmxvZygnLS0tJywgMSk7XG4gICAgLy8gICAgIH0gZWxzZSB7XG4gICAgLy8gICAgICAgICAkbGlzdC5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgIC8vICAgICAgICAgY29uc29sZS5sb2coJy0tLScsIDIpO1xuICAgIC8vICAgICB9XG4gICAgLy8gfTtcblxuICAgIGRyb3Bkb3duLl90b2dnbGUgPSBmdW5jdGlvbihlbCkge1xuICAgICAgICBpZiAoIWVsLmhhc0NsYXNzKEFDVElWRV9DTEFTUykpIHtcbiAgICAgICAgICAgIGVsLmFkZENsYXNzKEFDVElWRV9DTEFTUyk7XG4gICAgICAgICAgICAkYnRuRmxvYXRpbmcuZmFkZUluKCk7XG4gICAgICAgICAgICBvcGVuID0gdHJ1ZTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICRkcm9wZG93bi5yZW1vdmVDbGFzcyhBQ1RJVkVfQ0xBU1MpO1xuICAgICAgICAgICAgZWwucmVtb3ZlQ2xhc3MoQUNUSVZFX0NMQVNTKTtcbiAgICAgICAgICAgIG9wZW4gPSBmYWxzZTtcblxuICAgICAgICAgICAgaWYgKFxuICAgICAgICAgICAgICAgIGVsLmhhc0NsYXNzKCdiYi1kcm9wZG93bi0tdHJhbnNmb3JtJykgJiZcbiAgICAgICAgICAgICAgICAkKHdpbmRvdykud2lkdGgoKSA8PSA0ODBcbiAgICAgICAgICAgICkge1xuICAgICAgICAgICAgICAgICRidG5GbG9hdGluZy5mYWRlT3V0KCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgZHJvcGRvd24uX2Nsb3NlID0gZnVuY3Rpb24oKSB7XG4gICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgJGxpc3QucmVtb3ZlQ2xhc3MoVklTSUJMRV9DTEFTUyk7XG4gICAgICAgICAgICBfdGhpcy5yZW1vdmVDbGFzcyhBQ1RJVkVfQ0xBU1MpO1xuICAgICAgICAgICAgJGJ0bkZsb2F0aW5nLmZhZGVJbigpO1xuICAgICAgICAgICAgJGh0bWwucmVtb3ZlQ2xhc3MoJ25vLXRvdWNoLWV2ZW50cycpO1xuICAgICAgICB9LCAxMDApO1xuXG4gICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgJGxpc3RcbiAgICAgICAgICAgICAgICAucmVtb3ZlQXR0cignc3R5bGUnKVxuICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnX3RyYW5zZm9ybScpXG4gICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCdfdHJhbnNmb3JtX2luZm8nKVxuICAgICAgICAgICAgICAgIC5hcHBlbmRUbyhfdGhpcyk7XG4gICAgICAgICAgICAkb3ZlcmxheS5yZW1vdmVDbGFzcygnb3ZlcmxheS0tZHJvcGRvd24nKTtcbiAgICAgICAgICAgICRodG1sLnJlbW92ZUNsYXNzKCduby10b3VjaC1ldmVudHMnKTtcbiAgICAgICAgfSwgMzAwKTtcbiAgICB9O1xuXG4gICAgcmV0dXJuIGRyb3Bkb3duO1xufSkoKTtcblxuXG5cbmNvbnN0IE1lbnUgPSAoZnVuY3Rpb24oKSB7XG4gICAgbGV0IG1lbnUgPSB7fTtcbiAgICBsZXQgJGh0bWwgPSAkKCdodG1sJyk7XG4gICAgbGV0ICR3cmFwcGVyID0gJCgnLndyYXBwZXInKTtcbiAgICBsZXQgJGhlYWRlciA9ICQoJy5oZWFkZXInKTtcbiAgICBsZXQgJG92ZXJsYXkgPSAkKCcuanMtb3ZlcmxheScpO1xuICAgIGxldCAkbWVudSA9ICQoJy5qcy1tZW51Jyk7XG4gICAgbGV0IGhhbWJ1cmdlciA9ICcuanMtbWFpbi1uYXYtYnRuJztcbiAgICBsZXQgJGhhbWJ1cmdlciA9ICQoaGFtYnVyZ2VyKTtcbiAgICBsZXQgJGhhbWJ1cmdlckNybSA9ICQoJy5qcy1oYW1idXJnZXInKTtcbiAgICBsZXQgaGFtYnVyZ2VyQ3JtID0gJy5qcy1oYW1idXJnZXInO1xuICAgIGxldCAkbWVudUl0ZW0gPSAkKCcuanMtbWVudScpLmZpbmQoJy5tZW51X19pdGVtJyk7XG4gICAgbGV0ICRtZW51T3ZlbGF5ID0gJCgnLmpzLW1lbnUtb3ZlcmxheScpO1xuICAgIGxldCAkbWVudUl0ZW1Ecm9wZG93biA9ICQoZG9jdW1lbnQpLmZpbmQoJy5qcy1tZW51LWl0ZW0tZHJvcGRvd24nKTtcbiAgICBsZXQgJGJ0bkZsb2F0ID0gJChkb2N1bWVudCkuZmluZCgnLmpzLWJ0bi1mbG9hdGluZycpO1xuICAgIGxldCBBQ1RJVkVfQ0xBU1MgPSAnaXMtYWN0aXZlJztcbiAgICBsZXQgZHJvcGRvd25BY3RpdmVDbGFzcyA9ICdtZW51LWRyb3Bkb3duLS1vcGVuJztcblxuICAgIG1lbnUuaW5pdCA9IGZ1bmN0aW9uKCkge1xuICAgICAgICB0aGlzLmV2ZW50cygpO1xuICAgICAgICB0aGlzLm1lbnVJdGVtRHJvcGRvd25FdmVudCgpO1xuXG4gICAgICAgIC8v0JTQu9GPINCx0LXQt9C/0YDQvtCx0LvQtdC80L3QvtC5INGC0YDQsNC90YHRhNC+0YDQvNCw0YbQuNC4INC80LXQvdGOINC/0YDQuCDQv9C10YDQtdCy0L7RgNC+0YLQtSDRgtC10LvQtdGE0L7QvdCwXG4gICAgICAgICQod2luZG93KS5vbigncmVzaXplJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgbWVudS5fY2xvc2UoZSk7XG4gICAgICAgICAgICAkd3JhcHBlci5yZW1vdmVDbGFzcygnbWVudS1vcGVuJyk7XG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICBtZW51LmV2ZW50cyA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCBoYW1idXJnZXIsIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgIGlmICgkKHRoaXMpLmhhc0NsYXNzKCdvbicpKSB7XG4gICAgICAgICAgICAgICAgbWVudS5fY2xvc2UoKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgbWVudS5fb3BlbigpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgaGFtYnVyZ2VyQ3JtLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICBpZiAoJCh0aGlzKS5oYXNDbGFzcygnb24nKSkge1xuICAgICAgICAgICAgICAgIG1lbnUuX2Nsb3NlKGUpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBtZW51Ll9vcGVuKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgICRtZW51SXRlbS5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICBsZXQgJHRhcmdldCA9ICQoZS50YXJnZXQpO1xuICAgICAgICAgICAgLy/QldGB0LvQuCDQvdC10YIg0LLQu9C+0LbQtdC90L3QvtCz0L4g0LzQtdC90Y5cbiAgICAgICAgICAgIGlmICghJCh0aGlzKS5oYXNDbGFzcygnanMtbWVudS1pdGVtLWRyb3Bkb3duJykpIHtcbiAgICAgICAgICAgICAgICAkbWVudUl0ZW1cbiAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKEFDVElWRV9DTEFTUylcbiAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKGRyb3Bkb3duQWN0aXZlQ2xhc3MpO1xuICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoQUNUSVZFX0NMQVNTKTtcbiAgICAgICAgICAgICAgICAvLyBlLnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAvL9CV0YHQu9C4INC10YHRgtGMINCy0LvQvtC20LXQvdC90L7QtSDQvNC10L3RjlxuICAgICAgICAgICAgICAgIC8v0JXRgdC70Lgg0YLQsNGA0LPQtdGCINGB0YHRi9C70LrQsCDQvdC+INC90LUg0LrQvdC+0LrQsCDQntGC0LzQtdC90LjRgtGMXG4gICAgICAgICAgICAgICAgaWYgKFxuICAgICAgICAgICAgICAgICAgICAkdGFyZ2V0Lmhhc0NsYXNzKCdtZW51LWRyb3Bkb3duX19saW5rJykgJiZcbiAgICAgICAgICAgICAgICAgICAgISR0YXJnZXQuaGFzQ2xhc3MoJ21lbnUtZHJvcGRvd25fX2xpbmstLWFib3J0JylcbiAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAgICAgbGV0ICRwYXJlbnQgPSAkdGFyZ2V0LnBhcmVudCgnLm1lbnUtZHJvcGRvd25fX2l0ZW0nKTtcblxuICAgICAgICAgICAgICAgICAgICAvL9Cf0LXRgNC10LrQu9GO0YfQsNC10Lwg0LDQutGC0LjQstC90YvQuSDQutC70LDRgdGBINGDINCz0LvQsNCy0L3QvtC5INGB0YHRi9C70LrQuCDQvNC10L3RjiDQuCDQvtGC0LrRgNGL0LLQsNC10Lwg0LLQu9C+0LbQtdC90L3QvtC1INC80LXQvdGOXG4gICAgICAgICAgICAgICAgICAgICRtZW51SXRlbS5yZW1vdmVDbGFzcyhhY3RpdmVDbGFzcyk7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcyhkcm9wZG93bkFjdGl2ZUNsYXNzKVxuICAgICAgICAgICAgICAgICAgICAgICAgLmFkZENsYXNzKGFjdGl2ZUNsYXNzKTtcblxuICAgICAgICAgICAgICAgICAgICAvL9Cf0LXRgNC10LrQu9GO0YfQsNC10Lwg0LDQutGC0LjQstC90YvQuSDQutC70LDRgdGBINGDINCy0LvQvtC20LXQvdC90YvRhSBsaVxuICAgICAgICAgICAgICAgICAgICAkKCcubWVudS1kcm9wZG93bl9faXRlbScpLnJlbW92ZUNsYXNzKGFjdGl2ZUNsYXNzKTtcbiAgICAgICAgICAgICAgICAgICAgJHBhcmVudC5hZGRDbGFzcyhhY3RpdmVDbGFzcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCQod2luZG93KS53aWR0aCgpID4gNDgwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAvL9Ch0LTQstC40LPQsNC10Lwg0LrQvtC90YLQtdC90YJcbiAgICAgICAgICAgICAgICAgICAgICAgICR3cmFwcGVyLmFkZENsYXNzKCdtZW51LW9wZW4nKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIG1lbnUuX2Nsb3NlKGUpO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKFxuICAgICAgICAgICAgICAgICAgICAvL9CV0YHQu9C4INGC0LDRgNCz0LXRgiDQutC90L7QutCwINCe0YLQvNC10L3QuNGC0Ywg0L/RgNC+0YHRgtC+INC30LDQutGA0YvQstCw0LXQvCDQvNC10L3RjlxuICAgICAgICAgICAgICAgICAgICAkdGFyZ2V0Lmhhc0NsYXNzKCdtZW51LWRyb3Bkb3duX19saW5rJykgJiZcbiAgICAgICAgICAgICAgICAgICAgJHRhcmdldC5oYXNDbGFzcygnbWVudS1kcm9wZG93bl9fbGluay0tYWJvcnQnKVxuICAgICAgICAgICAgICAgICkge1xuICAgICAgICAgICAgICAgICAgICBtZW51Ll9jbG9zZShlKTtcbiAgICAgICAgICAgICAgICAgICAgLy8gZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAvL9CV0YHQu9C4INGC0LDRgNCz0LXRgiDQndCVINGB0YHRi9C70LrQsCwg0L/RgNC+0LLQtdGA0Y/QtdC8INC90LAg0L3QsNC70LjRh9C40LUg0LDQutGC0LjQstC90L7Qs9C+INC60LvQsNGB0YHQsCDRgyDQtNGA0L7Qv9C00LDRg9C90LBcbiAgICAgICAgICAgICAgICAgICAgaWYgKCQodGhpcykuaGFzQ2xhc3MoZHJvcGRvd25BY3RpdmVDbGFzcykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoZHJvcGRvd25BY3RpdmVDbGFzcyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkd3JhcHBlci5yZW1vdmVDbGFzcygnbWVudS1vcGVuJyk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkbWVudUl0ZW1Ecm9wZG93bi5yZW1vdmVDbGFzcyhkcm9wZG93bkFjdGl2ZUNsYXNzKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoZHJvcGRvd25BY3RpdmVDbGFzcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICgkKHdpbmRvdykud2lkdGgoKSA+IDQ4MCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICR3cmFwcGVyLmFkZENsYXNzKCdtZW51LW9wZW4nKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGJ0bkZsb2F0LmZhZGVPdXQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkbWVudU92ZWxheS5hZGRDbGFzcygnaXMtdmlzaWJsZScpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoISQodGhpcykuaGFzQ2xhc3MoQUNUSVZFX0NMQVNTKSkge1xuICAgICAgICAgICAgICAgICRtZW51SXRlbS5yZW1vdmVDbGFzcyhBQ1RJVkVfQ0xBU1MpO1xuICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoQUNUSVZFX0NMQVNTKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgJCgnLmpzLW1vYmlsZS1uYXYtLWNsb3NlJykub24oJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgbWVudS5fY2xvc2UoZSk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8v0JjQstC10L3RgiDQutC70LjQutCwINC/0L4g0LDQsNC60L7QtNC10L7QvdGDINCy0L3Rg9GC0YDQuCDQvNC10L3RjlxuICAgICAgICAkKGRvY3VtZW50KVxuICAgICAgICAgICAgLmZpbmQoJy5qcy1tb2JpbGUtbmF2JylcbiAgICAgICAgICAgIC5maW5kKCcubW9iaWxlLW5hdl9faXRlbScpXG4gICAgICAgICAgICAub24oJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgIGlmICghJCh0aGlzKS5oYXNDbGFzcygnYmItYWNjb3JkZW9uX19pdGVtJykpIHtcbiAgICAgICAgICAgICAgICAgICAgbWVudS5fY2xvc2UoZSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSlcbiAgICAgICAgICAgIC5lbmQoKVxuICAgICAgICAgICAgLmZpbmQoJy5iYi1hY2NvcmRlb25fX2NvbnRlbnQgYScpXG4gICAgICAgICAgICAub24oJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgIG1lbnUuX2Nsb3NlKGUpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgLy/Ql9Cw0LrRgNCy0LDQtdC8INC80LXQvdGOINC/0L4g0LrQu9GO0LrRgyDQvdCwINC+0LLQtdGA0LvRjdC5XG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcub3ZlcmxheS0tbWVudScsIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgIG1lbnUuX2Nsb3NlKGUpO1xuICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy/Ql9Cw0LrRgNCy0LDQtdC8INC80LXQvdGOINC/0L4g0LrQu9GO0LrRgyDQvdCwINC+0LLQtdGA0LvRjdC5XG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtbWVudS1vdmVybGF5JywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgbWVudS5fY2xvc2UoZSk7XG4gICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICB9KTtcbiAgICB9O1xuXG4gICAgbWVudS5tZW51SXRlbURyb3Bkb3duRXZlbnQgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5qcy1tZW51LWl0ZW0tZHJvcGRvd24nLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICBpZiAoJCh0aGlzKS5oYXNDbGFzcyhkcm9wZG93bkFjdGl2ZUNsYXNzKSkge1xuICAgICAgICAgICAgICAgICRtZW51SXRlbURyb3Bkb3duLnJlbW92ZUNsYXNzKGRyb3Bkb3duQWN0aXZlQ2xhc3MpO1xuICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoZHJvcGRvd25BY3RpdmVDbGFzcyk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoZHJvcGRvd25BY3RpdmVDbGFzcyk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICB9KTtcblxuICAgICAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnLmpzLW1lbnUtaXRlbS1kcm9wZG93biAubWVudV9fbGluaycsIGZ1bmN0aW9uKFxuICAgICAgICAgICAgZVxuICAgICAgICApIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgfSk7XG4gICAgfTtcblxuICAgIG1lbnUuX29wZW4gPSBmdW5jdGlvbigpIHtcbiAgICAgICAgaWYgKCEkKGRvY3VtZW50KS5maW5kKCcuanNDcm1CbHVyRXZlbnRTdG9wJykpIHtcbiAgICAgICAgICAgICQoZG9jdW1lbnQpXG4gICAgICAgICAgICAgICAgLmZpbmQoJ2lucHV0JylcbiAgICAgICAgICAgICAgICAuYmx1cigpO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKCQod2luZG93KS53aWR0aCgpID4gNDgwKSB7XG4gICAgICAgICAgICAkaGFtYnVyZ2VyQ3JtLmFkZENsYXNzKCdvbicpO1xuXG4gICAgICAgICAgICBpZiAoJHdyYXBwZXIuaGFzQ2xhc3MoJ3BhZ2UtY2FiaW5ldCcpKSB7XG4gICAgICAgICAgICAgICAgJG1lbnUuYWRkQ2xhc3MoJ2lzLW9wZW4nKTtcbiAgICAgICAgICAgICAgICAkKGRvY3VtZW50KVxuICAgICAgICAgICAgICAgICAgICAuZmluZCgnLmhlYWRlcicpXG4gICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnaXMtbW92aW5nJyk7XG4gICAgICAgICAgICAgICAgJHdyYXBwZXIuYWRkQ2xhc3MoJ21lbnUtb3BlbicpO1xuICAgICAgICAgICAgICAgICRtZW51SXRlbURyb3Bkb3duLnJlbW92ZUNsYXNzKCdpcy1hY3RpdmUnKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJHdyYXBwZXIuYWRkQ2xhc3MoJ21vYmlsZS1uYXYtLW9wZW4nKTtcbiAgICAgICAgICAgICAgICAkb3ZlcmxheS5hZGRDbGFzcygnaXMtdmlzaWJsZScpLmFkZENsYXNzKCdvdmVybGF5LS1tZW51Jyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAkaGFtYnVyZ2VyLmFkZENsYXNzKCdvbicpO1xuICAgICAgICAgICAgJHdyYXBwZXIuYWRkQ2xhc3MoJ21vYmlsZS1uYXYtLW9wZW4nKTtcbiAgICAgICAgICAgICRvdmVybGF5LmFkZENsYXNzKCdpcy12aXNpYmxlJykuYWRkQ2xhc3MoJ292ZXJsYXktLW1lbnUnKTtcbiAgICAgICAgICAgICRodG1sLmFkZENsYXNzKCdpcy1maXhlZCcpO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKCR3cmFwcGVyLmhhc0NsYXNzKCdwYWdlLW9uZXBhZ2UnKSkge1xuICAgICAgICAgICAgJGhhbWJ1cmdlci5hZGRDbGFzcygnb24nKTtcbiAgICAgICAgICAgICR3cmFwcGVyLmFkZENsYXNzKCdtb2JpbGUtbmF2LS1vcGVuJyk7XG4gICAgICAgICAgICAkb3ZlcmxheS5hZGRDbGFzcygnaXMtdmlzaWJsZScpLmFkZENsYXNzKCdvdmVybGF5LS1tZW51Jyk7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgbWVudS5fY2xvc2UgPSBmdW5jdGlvbihlKSB7XG4gICAgICAgICRoYW1idXJnZXIucmVtb3ZlQ2xhc3MoJ29uJyk7XG4gICAgICAgICRoYW1idXJnZXJDcm0ucmVtb3ZlQ2xhc3MoJ29uJyk7XG4gICAgICAgICRtZW51LnJlbW92ZUNsYXNzKCdpcy1vcGVuJyk7XG4gICAgICAgICRtZW51SXRlbURyb3Bkb3duLnJlbW92ZUNsYXNzKGRyb3Bkb3duQWN0aXZlQ2xhc3MpO1xuICAgICAgICAkKGRvY3VtZW50KVxuICAgICAgICAgICAgLmZpbmQoJy5oZWFkZXInKVxuICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCdpcy1tb3ZpbmcnKVxuICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCdpcy1vcGVuJyk7XG4gICAgICAgICRidG5GbG9hdC5mYWRlSW4oKTtcbiAgICAgICAgJHdyYXBwZXIucmVtb3ZlQ2xhc3MoJ21vYmlsZS1uYXYtLW9wZW4nKTtcbiAgICAgICAgJGh0bWwucmVtb3ZlQ2xhc3MoJ2lzLWZpeGVkJyk7XG5cbiAgICAgICAgbGV0IHRhcmdldCA9ICQoZS50YXJnZXQpO1xuICAgICAgICBpZiAodGFyZ2V0LmlzKCcuanMtaGFtYnVyZ2VyJykgfHwgdGFyZ2V0LmlzKCcuanMtbWVudS1pdGVtLWRyb3Bkb3duJykpIHtcbiAgICAgICAgICAgICR3cmFwcGVyLnJlbW92ZUNsYXNzKCdtZW51LW9wZW4nKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgJCgnLm92ZXJsYXktLW1lbnUnKS5yZW1vdmVDbGFzcygnaXMtdmlzaWJsZScpO1xuICAgICAgICB9LCAyMDApO1xuXG4gICAgICAgIGlmICgkKHdpbmRvdykud2lkdGgoKSA8IDQ4MCkge1xuICAgICAgICAgICAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAgICAgJG1lbnVPdmVsYXkucmVtb3ZlQ2xhc3MoJ2lzLXZpc2libGUnKTtcbiAgICAgICAgICAgIH0sIDEwMCk7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgcmV0dXJuIG1lbnU7XG59KSgpO1xuXG5cbiQoZnVuY3Rpb24oKSB7XG4gICAgTWVudS5pbml0KCk7XG5cbiAgICAvL1BhZ2VzXG4gICAgQ2F0YWxvZy5pbml0KCk7XG4gICAgQ2FyZC5pbml0KCk7XG59KTtcblxuLyoqXG4gKiBNYWluXG4gKlxuICogQGF1dGhvciBBbnRvbiBVc3Rpbm9mZiA8YS5hLnVzdGlub2ZmQGdtYWlsLmNvbT5cbiAqL1xuY29uc3QgTWFpbiA9IHtcbiAgICBpbml0KCkge30sXG59O1xuXG4vKipcbiAqIENhdGFsb2dcbiAqXG4gKiBAYXV0aG9yIEFudG9uIFVzdGlub2ZmIDxhLmEudXN0aW5vZmZAZ21haWwuY29tPlxuICovXG5jb25zdCBDYXRhbG9nID0ge1xuICAgIGluaXQ6IGZ1bmN0aW9uKCkge1xuICAgICAgICB0aGlzLm1hcFRvZ2dsZSgpO1xuICAgICAgICAvLyB0aGlzLmJ0bkZpbHRlck9wZW4oKTtcbiAgICAgICAgdGhpcy5idG5TaG93Q2F0YWxvZygpO1xuICAgICAgICB0aGlzLmJ0blNob3dNYXAoKTtcbiAgICAgICAgLy8gdGhpcy5zdGlja3lGaWx0ZXIoKTtcbiAgICAgICAgdGhpcy5maWx0ZXJDYXRlZ29yeSgpO1xuICAgICAgICB0aGlzLm1vdmVCbG9ja3MoKTtcbiAgICAgICAgdGhpcy5pZlBhZ2VDYXRhbG9nKCk7XG4gICAgfSxcbiAgICAvL0NhdGFsb2cgbWFwIFRvZ2dsZVxuICAgIG1hcFRvZ2dsZTogZnVuY3Rpb24oKSB7XG4gICAgICAgICQoJy5qcy1jYXRhbG9nLS1zaG93Jykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdpcy1hY3RpdmUnKTtcbiAgICAgICAgICAgICQoJy5qcy1jYXRhbG9nLW1hcC0tc2hvdycpLnJlbW92ZUNsYXNzKCdpcy1hY3RpdmUnKTtcbiAgICAgICAgfSk7XG4gICAgICAgICQoJy5qcy1jYXRhbG9nLW1hcC0tc2hvdycpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnaXMtYWN0aXZlJyk7XG4gICAgICAgICAgICAkKCcuanMtY2F0YWxvZy0tc2hvdycpLnJlbW92ZUNsYXNzKCdpcy1hY3RpdmUnKTtcbiAgICAgICAgfSk7XG4gICAgfSxcbiAgICAvL0J0biBzaG93IGNhdGFsb2dcbiAgICBidG5TaG93Q2F0YWxvZzogZnVuY3Rpb24oKSB7XG4gICAgICAgICQoJy5qcy1zaG93LS1saXN0Jykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAkKCcuanMtY2F0YWxvZy1tYXAnKS5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgICAgICAgICAgJCgnLmNhdGFsb2ctY29udGVudF9faW5uZXInKS5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgICAgICAgICAgJCgnLmpzLWNhdGFsb2ctbWFwJykucmVtb3ZlQ2xhc3MoJ2lzLWNoZWNrZWQnKTtcbiAgICAgICAgICAgICQodGhpcylcbiAgICAgICAgICAgICAgICAucGFyZW50KClcbiAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ2lzLWFjdGl2ZScpO1xuICAgICAgICB9KTtcbiAgICB9LFxuICAgIC8vQnRuIHNob3cgbWFwIC0gaGlkZSBjYXRhbG9nXG4gICAgYnRuU2hvd01hcDogZnVuY3Rpb24oKSB7XG4gICAgICAgICQoJy5qcy1zaG93LS1tYXAnKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICQoJy5qcy1jYXRhbG9nLW1hcCcpLmNzcygnZGlzcGxheScsICdibG9jaycpO1xuICAgICAgICAgICAgJCgnLmNhdGFsb2ctY29udGVudF9faW5uZXInKS5jc3MoJ2Rpc3BsYXknLCAnbm9uZScpO1xuICAgICAgICAgICAgJCgnLmpzLXN0aWt5LWJsb2NrJykucmVtb3ZlQXR0cignc3R5bGUnKTtcbiAgICAgICAgICAgICQoJy5qcy1jYXRhbG9nLW1hcCcpLmFkZENsYXNzKCdpcy1jaGVja2VkJyk7XG4gICAgICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAgICAgLnBhcmVudCgpXG4gICAgICAgICAgICAgICAgLmFkZENsYXNzKCdpcy1hY3RpdmUnKTtcbiAgICAgICAgfSk7XG4gICAgfSxcbiAgICAvL2ZpbHRlciBjYXRlZ29yeVxuICAgIGZpbHRlckNhdGVnb3J5OiBmdW5jdGlvbigpIHtcbiAgICAgICAgaWYgKCQod2luZG93KS53aWR0aCgpID4gNzY4KSB7XG4gICAgICAgICAgICAkKCcuanMtY2F0ZWdvcnknKVxuICAgICAgICAgICAgICAgIC5maW5kKCcuY2F0ZWdvcnlfX2xpbmsnKVxuICAgICAgICAgICAgICAgIC5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAgICAgLnBhcmVudCgpXG4gICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2lzLXNlbGVjdGVkJyk7XG4gICAgICAgICAgICAgICAgICAgICQoJy5qcy1jYXRlZ29yeScpXG4gICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2lzLWNoZWNrZWQnKVxuICAgICAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy5jYXRlZ29yeV9fbGluaycpXG4gICAgICAgICAgICAgICAgICAgICAgICAubm90KHRoaXMpXG4gICAgICAgICAgICAgICAgICAgICAgICAucGFyZW50KClcbiAgICAgICAgICAgICAgICAgICAgICAgIC5jc3MoJ2Rpc3BsYXknLCAnbm9uZScpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgJCgnLmpzLWNhdGVnb3J5LS1yZXNldCcpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgLnBhcmVudCgpXG4gICAgICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnaXMtc2VsZWN0ZWQnKVxuICAgICAgICAgICAgICAgICAgICAuY2xvc2VzdCgnLmpzLWNhdGVnb3J5JylcbiAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCdpcy1jaGVja2VkJyk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAuY2xvc2VzdCgnLmpzLWNhdGVnb3J5JylcbiAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy5jYXRlZ29yeV9faXRlbScpXG4gICAgICAgICAgICAgICAgICAgIC5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAkKCcuanMtY2F0ZWdvcnknKVxuICAgICAgICAgICAgICAgIC5maW5kKCcuY2F0ZWdvcnlfX2xpbmsnKVxuICAgICAgICAgICAgICAgIC5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKFxuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5wYXJlbnQoKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5oYXNDbGFzcygnaXMtc2VsZWN0ZWQnKVxuICAgICAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAucGFyZW50KClcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ2lzLXNlbGVjdGVkJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcuanMtY2F0ZWdvcnknKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnaXMtY2hlY2tlZCcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy5jYXRlZ29yeV9fbGluaycpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLnBhcmVudCgpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUF0dHIoJ3N0eWxlJyk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLnBhcmVudCgpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmFkZENsYXNzKCdpcy1zZWxlY3RlZCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnLmpzLWNhdGVnb3J5JylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2lzLWNoZWNrZWQnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5maW5kKCcuY2F0ZWdvcnlfX2xpbmsnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5ub3QodGhpcylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAucGFyZW50KClcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuY3NzKCdkaXNwbGF5JywgJ25vbmUnKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgfSxcbiAgICAvL01vdmUgYmxvY2sgaW4gbWVkaWEgc2NyZWVuXG4gICAgbW92ZUJsb2NrczogZnVuY3Rpb24oKSB7XG4gICAgICAgIGlmICgkKHdpbmRvdykud2lkdGgoKSA8PSA0ODApIHtcbiAgICAgICAgICAgICQoJy5qcy12aWV3LXRvZ2dsZScpLmluc2VydEJlZm9yZSgnLmNhdGFsb2dfX2lubmVyJyk7XG4gICAgICAgIH1cbiAgICB9LFxuICAgIC8vSWYgcGFnZSBjYXRhbG9nIGZpbHRlciB0cmFuc2Zvcm0gYWNjb3JkZW9uXG4gICAgaWZQYWdlQ2F0YWxvZzogZnVuY3Rpb24oKSB7XG4gICAgICAgIGlmICgkd3JhcHBlci5oYXNDbGFzcygncGFnZS1jYXRhbG9nJykpIHtcbiAgICAgICAgICAgICRoZWFkZXIuYWRkQ2xhc3MoJ2hlYWRlci0tZml4ZWQnKTtcbiAgICAgICAgICAgIC8vICRtYWluLmNzcygncGFkZGluZy10b3AnLCAkKCcuaGVhZGVyJykub3V0ZXJIZWlnaHQoKSk7XG4gICAgICAgICAgICBpZiAoJHdpbmRvdy53aWR0aCgpIDw9IDc2OCkge1xuICAgICAgICAgICAgICAgICQoJy5jYXRhbG9nLWZpbHRlcl9fYm9keScpLmFkZENsYXNzKFxuICAgICAgICAgICAgICAgICAgICAnYmItYWNjb3JkZW9uIGJiLWFjY29yZGVvbi0tY3VzdG9tIGJiLWFjY29yZGVvbi0tb3RoZXIganMtYmItYWNjb3JkZW9uJ1xuICAgICAgICAgICAgICAgICk7XG4gICAgICAgICAgICAgICAgJCgnLmpzLWNhdGFsb2ctZmlsdGVyLWl0ZW0nKS5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2JiLWFjY29yZGVvbl9faXRlbScpXG4gICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnLmNhdGFsb2ctZmlsdGVyX190aXRsZScpXG4gICAgICAgICAgICAgICAgICAgICAgICAubm90KCcuY2F0YWxvZy1maWx0ZXJfX3RpdGxlX2NhdGVnb3J5JylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnYmItYWNjb3JkZW9uX190aXRsZScpO1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnLmNhdGFsb2ctZmlsdGVyX19jb250ZW50JylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnYmItYWNjb3JkZW9uX19jb250ZW50JylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5zbGlkZVVwKCk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgJCgnLmpzLWNhdGFsb2ctZmlsdGVyLWl0ZW06bHQoMiknKVxuICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2lzLW9wZW4nKVxuICAgICAgICAgICAgICAgICAgICAuZmluZCgnLmJiLWFjY29yZGVvbl9fY29udGVudCcpXG4gICAgICAgICAgICAgICAgICAgIC5zbGlkZURvd24oKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0sXG59O1xuXG4vKipcbiAqIENhcmRcbiAqXG4gKiBAYXV0aG9yIEFudG9uIFVzdGlub2ZmIDxhLmEudXN0aW5vZmZAZ21haWwuY29tPlxuICovXG5jb25zdCBDYXJkID0ge1xuICAgIGluaXQoKSB7XG4gICAgICAgIHRoaXMuc2xpZGVyKCk7XG4gICAgICAgIHRoaXMuc2Nyb2xsc3B5KCk7XG4gICAgICAgIHRoaXMuY2FyZFN0aWNreSgpO1xuXG4gICAgICAgIGlmICgkKHdpbmRvdykud2lkdGgoKSA8PSA3NjgpIHtcbiAgICAgICAgICAgIENhcmQucmVxdWVzdFRvZ2dsZSgpO1xuICAgICAgICAgICAgQ2FyZC5yZXF1ZXN0QmxvY2tNb3ZlSXRlbXMoKTtcbiAgICAgICAgICAgICR3aW5kb3cucmVzaXplKENhcmQucmVxdWVzdEJsb2NrTW92ZUl0ZW1zKTtcbiAgICAgICAgfVxuICAgIH0sXG5cbiAgICAvL0NhcmQgU2xpZGVyXG4gICAgc2xpZGVyKCkge1xuICAgICAgICBsZXQgJGNhcmRTbGlkZXIgPSAkKCcuanMtY2FyZC1zbGlkZXInKTtcbiAgICAgICAgaWYgKCRjYXJkU2xpZGVyLmxlbmd0aCkge1xuICAgICAgICAgICAgJGNhcmRTbGlkZXIuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBsZXQgX3RoaXMgPSAkKHRoaXMpO1xuICAgICAgICAgICAgICAgIGxldCAkc2xpZGVzID0gX3RoaXMuZmluZCgnLmJiLXNsaWRlcl9fc2xpZGVzJyk7XG4gICAgICAgICAgICAgICAgbGV0ICRzbGlkZXJEb3RzID0gJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAuZmluZCgnLmJiLXNsaWRlcl9fZG90cycpXG4gICAgICAgICAgICAgICAgICAgIC5oaWRlKCk7XG4gICAgICAgICAgICAgICAgY29uc3Qgc2V0dGluZ3MgPSB7XG4gICAgICAgICAgICAgICAgICAgIG5leHRBcnJvdzogJy5iYi1zbGlkZXJfX2Fycm93LS1uZXh0JyxcbiAgICAgICAgICAgICAgICAgICAgcHJldkFycm93OiAnLmJiLXNsaWRlcl9fYXJyb3ctLXByZXYnLFxuICAgICAgICAgICAgICAgICAgICBzcGVlZDogNDAwLFxuICAgICAgICAgICAgICAgICAgICBpbmZpbml0ZTogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgIHNsaWRlc1RvU2hvdzogNCxcbiAgICAgICAgICAgICAgICAgICAgc2xpZGVzVG9TY3JvbGw6IDEsXG4gICAgICAgICAgICAgICAgICAgIGFycm93czogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgZG90czogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgIGxhenlMb2FkOiAnb25kZW1hbmQnLFxuXG4gICAgICAgICAgICAgICAgICAgIHJlc3BvbnNpdmU6IFtcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVha3BvaW50OiAxMjAxLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNldHRpbmdzOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNsaWRlc1RvU2hvdzogMyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVha3BvaW50OiA3NjksXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2V0dGluZ3M6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2xpZGVzVG9TaG93OiAyLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzbGlkZXNUb1Njcm9sbDogMSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVha3BvaW50OiA0ODEsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2V0dGluZ3M6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2xpZGVzVG9TaG93OiAxLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzbGlkZXNUb1Njcm9sbDogMSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgXSxcbiAgICAgICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAgICAgaWYgKCR3aW5kb3cud2lkdGgoKSA8PSA0ODApIHtcbiAgICAgICAgICAgICAgICAgICAgJHNsaWRlckRvdHMuc2hvdygpO1xuXG4gICAgICAgICAgICAgICAgICAgIF90aGlzXG4gICAgICAgICAgICAgICAgICAgICAgICAub24oJ2luaXQnLCBmdW5jdGlvbihldmVudCwgc2xpY2spIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkc2xpZGVyRG90cy5wcmVwZW5kKFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBcIjxzcGFuIGNsYXNzPSdiYi1zbGlkZXJfX3BhZ2VyIGJiLXNsaWRlcl9fcGFnZXItLW5vdyc+MTwvc3Bhbj5cIiArXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnLydcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRzbGlkZXJEb3RzLmFwcGVuZChcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgXCI8c3BhbiBjbGFzcz0nYmItc2xpZGVyX19wYWdlciBiYi1zbGlkZXJfX3BhZ2VyLS10b3RhbCc+XCIgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2xpY2suc2xpZGVDb3VudCArXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnPC9zcGFuPidcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAgICAgICAgIC5vbignYWZ0ZXJDaGFuZ2UnLCBmdW5jdGlvbihcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBldmVudCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzbGljayxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjdXJyZW50U2xpZGUsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbmV4dFNsaWRlXG4gICAgICAgICAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgaSA9IChjdXJyZW50U2xpZGUgPyBjdXJyZW50U2xpZGUgOiAwKSArIDE7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgX3RoaXMuZmluZCgnLmJiLXNsaWRlcl9fcGFnZXItLW5vdycpLmh0bWwoaSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAkc2xpZGVzLm5vdCgnLnNsaWNrLWluaXRpYWxpemVkJykuc2xpY2soc2V0dGluZ3MpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9LFxuXG4gICAgLy9DYXJkIHJlcXVlc3Qgc2hvdyAvIGhpZGVcbiAgICByZXF1ZXN0VG9nZ2xlKCkge1xuICAgICAgICBsZXQgY2FyZEluZm9SZXF1ZXN0ID0gJCgnLmNhcmQtaW5mb19fcmVxdWVzdCcpO1xuXG4gICAgICAgICQoJy5qcy1jYXJkLXJlcXVlc3QtLXNob3cnKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGlmIChjYXJkSW5mb1JlcXVlc3QuaGFzQ2xhc3MoJ2lzLW9wZW4nKSkge1xuICAgICAgICAgICAgICAgICRodG1sLnJlbW92ZUF0dHIoJ3N0eWxlJyk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGNhcmRJbmZvUmVxdWVzdC5hZGRDbGFzcygnaXMtb3BlbicpO1xuICAgICAgICAgICAgICAgICRodG1sLmNzcygnb3ZlcmZsb3cnLCAnaGlkZGVuJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH0pO1xuICAgICAgICAkKCcuanMtY2FyZC1yZXF1ZXN0LS1oaWRlJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBpZiAoY2FyZEluZm9SZXF1ZXN0Lmhhc0NsYXNzKCdpcy1vcGVuJykpIHtcbiAgICAgICAgICAgICAgICBjYXJkSW5mb1JlcXVlc3QucmVtb3ZlQ2xhc3MoJ2lzLW9wZW4nKTtcbiAgICAgICAgICAgICAgICAkaHRtbC5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLy9Nb3ZlIGJsb2NrcyB3aGVuIHdpbmRvdyB3aWR0aCA8IDc2OFxuICAgIHJlcXVlc3RCbG9ja01vdmVJdGVtcygpIHtcbiAgICAgICAgJCgnLmpzLWNhcmQtdGl0bGUnKS5pbnNlcnRBZnRlcignLmNhcmQtZ2FsbGFyeV9fd3JhcCcpO1xuICAgICAgICAkKCcuanMtY2FyZC1hYm91dCcpLmluc2VydEJlZm9yZSgnLmNhcmQtYWRyZXNzJyk7XG4gICAgICAgICQoJy5jYXJkLWluZm9fX2lubmVyJykuaW5zZXJ0QWZ0ZXIoJy5jYXJkLWFkcmVzcycpO1xuXG4gICAgICAgICQoJy5jYXJkLWluZm9fX3JlcXVlc3QnKS53cmFwSW5uZXIoXG4gICAgICAgICAgICAnPGRpdiBjbGFzcz1cImNhcmQtaW5mb19fcmVxdWVzdF9pbm5lclwiPidcbiAgICAgICAgKTtcbiAgICAgICAgJCgnLmNhcmQtaW5mb19faGVhZGVyLS1tb2JpbGUnKS5pbnNlcnRCZWZvcmUoXG4gICAgICAgICAgICAnLmNhcmQtaW5mb19fcmVxdWVzdF9pbm5lcidcbiAgICAgICAgKTtcbiAgICAgICAgJCgnLmpzLWNhcmQtaW5mby1jYXRlZ29yeScpLnByZXBlbmRUbygnLmNhcmQtaW5mb19fcmVxdWVzdF9pbm5lcicpO1xuICAgICAgICAkKCcuanMtbW92ZS1jYXJkLXBvbGljeScpLmFwcGVuZFRvKCcuY2FyZC1yZXF1ZXN0LWZvcm0nKTtcbiAgICB9LFxuXG4gICAgLy9DYXJkIFNjcm9sbHNweVxuICAgIHNjcm9sbHNweSgpIHtcbiAgICAgICAgY29uc3QgJHNjcm9sbGluZ9Chb250YWluZXIgPSAkKCcuanMtc2Nyb2xsc3B5Jyk7XG4gICAgICAgIGxldCBvZmZzZXQ7XG5cbiAgICAgICAgaWYgKCRzY3JvbGxpbmfQoW9udGFpbmVyLmxlbmd0aCkge1xuICAgICAgICAgICAgJHdpbmRvdy53aWR0aCgpID4gNDgwID8gKG9mZnNldCA9IC0xMDApIDogKG9mZnNldCA9IC02MCk7XG4gICAgICAgICAgICBjb25zb2xlLmxvZygnLS0tIG9mZnNldCcsIG9mZnNldCk7XG5cbiAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgICAgICRzY3JvbGxpbmfQoW9udGFpbmVyLnNjcm9sbHNweSh7IG9mZnNldDogb2Zmc2V0IH0pO1xuICAgICAgICAgICAgfSwgMTAwMCk7XG4gICAgICAgIH1cbiAgICB9LFxuXG4gICAgY2FyZFN0aWNreSgpIHtcbiAgICAgICAgaWYgKCQoJy5qcy1jYXJkLWZpeGVkJykubGVuZ3RoKSB7XG4gICAgICAgICAgICBsZXQgc3RpY2t5QmxvY2sgPSAkKCcuanMtY2FyZC1zdGlja3knKTtcbiAgICAgICAgICAgIGxldCBzdGlja3lCbG9ja09mZnNldCA9IHN0aWNreUJsb2NrLm9mZnNldCgpLnRvcDtcbiAgICAgICAgICAgIGxldCBmaXhlZEJsb2NrID0gJCgnLmpzLWNhcmQtZml4ZWQnKTtcbiAgICAgICAgICAgIGxldCBmaXhlZEJsb2NrT2Zmc2V0ID0gZml4ZWRCbG9jay5vZmZzZXQoKS50b3A7XG5cbiAgICAgICAgICAgIGxldCBjYXJkQ29udGVudCA9ICQoJy5qcy1jYXJkLWNvbnRlbnQtZml4ZWQnKTtcbiAgICAgICAgICAgIGxldCBjYXJkTWVudSA9ICRkb2N1bWVudC5maW5kKCcuanMtY2FyZC1tZW51Jyk7XG5cbiAgICAgICAgICAgIGxldCBjYXJkTWVudUNsb25lID0gJCgnPGRpdiBjbGFzcz1cImNhcmQtbWVudV9fY2xvbmVcIj4nKVxuICAgICAgICAgICAgICAgIC5jc3MoJ2hlaWdodCcsICQoJy5qcy1jYXJkLW1lbnUnKS5vdXRlckhlaWdodCh0cnVlKSlcbiAgICAgICAgICAgICAgICAuaW5zZXJ0QWZ0ZXIoY2FyZE1lbnUpXG4gICAgICAgICAgICAgICAgLmhpZGUoKTtcblxuICAgICAgICAgICAgbGV0IGNhcmRNZW51T2Zmc2V0ID0gY2FyZE1lbnUub2Zmc2V0KCkudG9wO1xuXG4gICAgICAgICAgICBjb25zdCBjYXJkTWVudUZpeGVkU3R5bGUgPSB7XG4gICAgICAgICAgICAgICAgcG9zaXRpb246ICdmaXhlZCcsXG4gICAgICAgICAgICAgICAgdG9wOiAwLFxuICAgICAgICAgICAgICAgIGxlZnQ6IDAsXG4gICAgICAgICAgICAgICAgcmlnaHQ6IDAsXG4gICAgICAgICAgICAgICAgekluZGV4OiA5OSxcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIGlmIChcbiAgICAgICAgICAgICAgICBzdGlja3lCbG9jay5sZW5ndGggPiAwICYmXG4gICAgICAgICAgICAgICAgZml4ZWRCbG9jay5sZW5ndGggPiAwICYmXG4gICAgICAgICAgICAgICAgc3RpY2t5QmxvY2suaGVpZ2h0KCkgPCBjYXJkQ29udGVudC5oZWlnaHQoKSAmJlxuICAgICAgICAgICAgICAgICQod2luZG93KS53aWR0aCgpID4gNzY4XG4gICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICBmaXhDYXJkVXNlckluZm8oKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgZnVuY3Rpb24gZml4Q2FyZFVzZXJJbmZvKCkge1xuICAgICAgICAgICAgICAgICR3aW5kb3cuc2Nyb2xsKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICB2YXIgc2Nyb2xsID0gJCh0aGlzKS5zY3JvbGxUb3AoKTtcbiAgICAgICAgICAgICAgICAgICAgaWYgKFxuICAgICAgICAgICAgICAgICAgICAgICAgc2Nyb2xsID49IHN0aWNreUJsb2NrT2Zmc2V0ICYmXG4gICAgICAgICAgICAgICAgICAgICAgICBzY3JvbGwgPFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZpeGVkQmxvY2sub3V0ZXJIZWlnaHQodHJ1ZSkgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBmaXhlZEJsb2NrT2Zmc2V0IC1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc3RpY2t5QmxvY2sub3V0ZXJIZWlnaHQoKVxuICAgICAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHN0aWNreUJsb2NrLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcG9zaXRpb246ICdmaXhlZCcsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdG9wOiAtMSArICdweCcsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgd2lkdGg6IDM3NSArICdweCcsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYm90dG9tOiAnYXV0bycsXG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmIChcbiAgICAgICAgICAgICAgICAgICAgICAgIHNjcm9sbCA+PSBzdGlja3lCbG9ja09mZnNldCAmJlxuICAgICAgICAgICAgICAgICAgICAgICAgc2Nyb2xsID5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBmaXhlZEJsb2NrLm91dGVySGVpZ2h0KHRydWUpICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZml4ZWRCbG9ja09mZnNldCAtXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN0aWNreUJsb2NrLm91dGVySGVpZ2h0KCkgLVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAzMFxuICAgICAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHN0aWNreUJsb2NrLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcG9zaXRpb246ICdhYnNvbHV0ZScsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdG9wOiAnYXV0bycsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYm90dG9tOiAwLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHdpZHRoOiAzNzUgKyAncHgnLFxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzdGlja3lCbG9jay5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChjYXJkTWVudS5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICBjYXJkTWVudUZpeGVkKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGZ1bmN0aW9uIGNhcmRNZW51Rml4ZWQoKSB7XG4gICAgICAgICAgICAgICAgJCh3aW5kb3cpLm9uKCdzY3JvbGwgdG91Y2htb3ZlJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCBzY3JvbGwgPSAkKHRoaXMpLnNjcm9sbFRvcCgpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChzY3JvbGwgPj0gY2FyZE1lbnVPZmZzZXQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhcmRNZW51Q2xvbmUuc2hvdygpO1xuICAgICAgICAgICAgICAgICAgICAgICAgY2FyZE1lbnUuY3NzKGNhcmRNZW51Rml4ZWRTdHlsZSkuYWRkQ2xhc3MoJ2lzLXN0aWNreScpO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgY2FyZE1lbnVDbG9uZS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBjYXJkTWVudS5yZW1vdmVBdHRyKCdzdHlsZScpLnJlbW92ZUNsYXNzKCdpcy1zdGlja3knKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfSxcbn07XG5cbiJdfQ==
