<!-- Preloader -->
<div class="preloader flex-column justify-content-center align-items-center">

</div>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="dropdown-divider"></div>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="/dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Nora Silvester
                                <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">The subject goes here</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
            </div>
        </li>
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <label style="color:black">{{trans('default.user') ?? '使用者'}} {{auth('session')->user()->name}}</label>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/admin/user/logout" role="button">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">


    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">

            </div>
            <div class="info">
                <a href="/admin/index/dashboard" class="d-block">{{trans('default.leftbox.tittle') ?? '入口網站後台控制'}}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                @if(authPermission('seo-index'))
                    <li class="nav-item">
                        <a href="/admin/seo/index" class="nav-link {{$seo_keyword_active ?? ''}}">
                            <i class="nav-icon fab fa-chrome"></i>
                            <p>
                            {{trans('default.leftbox.seo') ?? 'SEO 關鍵字管理'}}
                            </p>
                        </a>
                    </li>
                @endif

                @if(authPermission('advertisement-index'))
                <li class="nav-item">
                    <a href="/admin/advertisement/index" class="nav-link {{$advertisement_active ?? ''}}">
                        <i class="nav-icon fas fa-ad"></i>
                        <p>
                        {{trans('default.leftbox.advertisement') ?? '廣告管理'}}
                        </p>
                    </a>
                </li>
                @endif

                @if(authPermission('advertisementcount-index'))
                    <li class="nav-item">
                        <a href="/admin/advertisement_count/index" class="nav-link {{$advertisement_count_active ?? ''}}">
                            <i class="nav-icon fas fa-mountain"></i>
                            <p>
                            {{trans('default.leftbox.advertisement_count') ?? '廣告圖表'}}
                            </p>
                        </a>
                    </li>
                @endif

                @if(authPermission('advertisementcount-list'))
                    <li class="nav-item">
                        <a href="/admin/advertisement_count/list" class="nav-link {{$advertisement_count_list_active ?? ''}}">
                            <i class="nav-icon fas fa-list"></i>
                            <p>
                                {{trans('default.leftbox.advertisement_count_list') ?? '廣告點擊統計詳細資料'}}
                            </p>
                        </a>
                    </li>
                @endif

                @if(authPermission('manager-index'))
                    <li class="nav-item">
                        <a href="/admin/manager/index" class="nav-link {{$user_active ?? ''}}">
                            <i class="nav-icon far fa-user"></i>
                            <p>
                            {{trans('default.leftbox.manager') ?? '使用者管理'}}
                            </p>
                        </a>
                    </li>
                @endif

                @if(authPermission('role-index'))
                    <li class="nav-item">
                        <a href="/admin/role/index" class="nav-link {{$role_active ?? ''}}">
                            <i class="nav-icon fas fa-user-tag"></i>
                            <p>
                            {{trans('default.leftbox.role') ?? '角色管理'}}
                            </p>
                        </a>
                    </li>
                @endif

                @if(authPermission('site-index'))
                <li class="nav-item">
                    <a href="/admin/site/index" class="nav-link {{$site_active ?? ''}}">
                        <i class="nav-icon fas fa-sitemap"></i>
                        <p>
                        {{trans('default.leftbox.site') ?? '多站管理'}}
                        </p>
                    </a>
                </li>
                @endif

                @if(authPermission('usersite-index'))
                <li class="nav-item">
                    <a href="/admin/user_site/index" class="nav-link {{$user_site_active ?? ''}}">
                        <i class="nav-icon fas fa-project-diagram"></i>
                        <p>
                        {{trans('default.leftbox.user_site') ?? '用戶對應多站管理'}}
                        </p>
                    </a>
                </li>
                @endif
                @if(authPermission('newsticker-index'))
                <li class="nav-item">
                    <a href="/admin/news_ticker/index" class="nav-link {{$news_ticker_active ?? ''}}">
                        <i class="nav-icon fas fa-newspaper"></i>
                        <p>
                        {{trans('default.leftbox.news_ticker') ?? '跑馬燈管理'}}
                        </p>
                    </a>
                </li>
                @endif
                @if(authPermission('visitoractivity-index'))
                <li class="nav-item">
                    <a href="/admin/visitor_activity/index" class="nav-link {{$visitor_activity_active ?? ''}}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                        {{trans('default.leftbox.visitor_activity') ?? '用戶活躍數圖表'}}
                        </p>
                    </a>
                </li>
                @endif
                @if(authPermission('visitoractivity-list'))
                    <li class="nav-item">
                        <a href="/admin/visitor_activity/list" class="nav-link {{$visitor_activity_active ?? ''}}">
                            <i class="nav-icon fas fa-list-alt"></i>
                            <p>
                                {{trans('default.leftbox.visitor_activity_list') ?? '用戶活躍數統計資料'}}
                            </p>
                        </a>
                    </li>
                @endif
                @if(authPermission('icon-index'))
                <li class="nav-item">
                    <a href="/admin/icon/index" class="nav-link {{$icon_active ?? ''}}">
                        <i class="nav-icon fas fa-sign-in-alt"></i>
                        <p>
                        {{trans('default.leftbox.icon') ?? '入口圖標管理'}}
                        </p>
                    </a>
                </li>
                @endif
                @if(authPermission('iconcount-index'))
                <li class="nav-item">
                    <a href="/admin/icon_count/index" class="nav-link {{$icon_count_active ?? ''}}">
                        <i class="nav-icon fas fa-icons"></i>
                        <p>
                        {{trans('default.leftbox.icon_count') ?? '入口圖標圖表'}}
                        </p>
                    </a>
                </li>
                @endif
                @if(authPermission('iconcount-list'))
                    <li class="nav-item">
                        <a href="/admin/icon_count/list" class="nav-link {{$icon_count_list_active ?? ''}}">
                            <i class="nav-icon fas fa-th-list"></i>
                            <p>
                                {{trans('default.leftbox.icon_count_list') ?? '入口圖標統計'}}
                            </p>
                        </a>
                    </li>
                @endif
                @if(authPermission('share-index'))
                <li class="nav-item">
                    <a href="/admin/share/index" class="nav-link {{$share_active ?? ''}}">
                        <i class="nav-icon fas fa-share"></i>
                        <p>
                        {{trans('default.leftbox.share') ?? '分享功能管理'}}
                        </p>
                    </a>
                </li>
                @endif
                @if(authPermission('sharecount-index'))
                <li class="nav-item">
                    <a href="/admin/share_count/index" class="nav-link {{$share_count_active ?? ''}}">
                        <i class="nav-icon fas fa-chart-area"></i>
                        <p>
                        {{trans('default.leftbox.share_count') ?? '分享功能圖表'}}
                        </p>
                    </a>
                </li>
                @endif
                @if(authPermission('retentionrate-list'))
                    <li class="nav-item">
                        <a href="/admin/retention_rate/list" class="nav-link {{$retention_rate_active ?? ''}}">
                            <i class="nav-icon fas fa-save"></i>
                            <p>
                                {{trans('default.leftbox.retentionrate') ?? '用戶留存率'}}
                            </p>
                        </a>
                    </li>
                @endif





            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

