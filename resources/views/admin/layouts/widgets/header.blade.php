<header id="topnav">
    <div class="topbar-main">
        <div class="container">
            <!-- LOGO -->
            <div class="topbar-left">
                <a href="{{route('index_main')}}" class="logo"><span>Beauty<span>Box</span></span></a>
            </div>
            {{--<!-- End Logo container-->--}}
            @if ($currentUser->hasRoleOld(\Beauty\Modules\Common\Models\RoleN::SITE_ADMIN))
                <div class="menu-extras">
                    <ul class="nav navbar-nav navbar-right pull-right">
                        <li class="right-bar-toggle">
                            <a class="btn btn-info waves-effect" href="{{ route('admin_courses',[],false) }}" style="padding: 3px">{{ generateNewCourses() }}</a>
                        </li>
                        <li class="right-bar-toggle">
                            <a class="btn btn-purple waves-effect" href="{{ route('admin_combo_categories_moderation',[],false) }}" style="padding: 3px">{{ generateCustomCategoriesModeration() }}</a>
                        </li>
                        <li class="right-bar-toggle">
                            <a class="btn btn-inverse waves-effect" href="{{ route('admin_combo_services_moderation',[],false) }}" style="padding: 3px">{{ generateCustomServicesModeration() }}</a>
                        </li>
                    </ul>
                    <div class="menu-item">
                        <!-- Mobile menu toggle-->
                        <a class="navbar-toggle" style="width: 40px">
                            <div class="lines">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </a>
                        <!-- End mobile menu toggle-->
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="navbar-custom">
        <div class="container">
            <div id="navigation">
                <ul class="navigation-menu">
                    @if ($currentUser->hasRoleOld(\Beauty\Modules\Common\Models\RoleN::SITE_ADMIN))
                        <li>
                            <a href="{{ route('admin_dashboard', [] ,false) }}"><i class="zmdi zmdi-view-dashboard"></i>
                                <span>Главная</span>
                            </a>
                        </li>
                    @endif
                    @if (count($oMenu) > 0)
                        @foreach ($oMenu as $menu)
                            @if ($menu->parentID == 0 && count($menu->children) > 0)
                                <li class="has-submenu">
                                    <a href="#"><i class="zmdi {{ $menu->icon }}"></i>
                                        <span>{{ $menu->name }}</span>
                                    </a>
                                    <ul class="submenu megamenu">
                                        <li>
                                            <ul>
                                                @foreach ($menu->children as $child)
                                                    <li>
                                                        <a href="{{ $child->route ? route($child->route) : ($child->url ? url($child->url) : '#') }}">{{ $child->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            @elseif ($menu->parentID == 0)
                                <li>
                                    <a href="#"><i class="zmdi {{ $menu->icon }}"></i>
                                        <span>{{ $menu->name }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </div>
</header>
