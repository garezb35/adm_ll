    <!-- ========== App Menu ========== -->
    <div class="app-menu navbar-menu">
        <!-- LOGO -->
        <div class="navbar-brand-box">
            <a href="index" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
                </span>
                <span class="logo-lg">
                    <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="" height="22">
                </span>
            </a>
            <a href="index" class="logo logo-light">
                <span class="logo-sm">
                    <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
                </span>
                <span class="logo-lg">
                    <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="22">
                </span>
            </a>
            <button type="button" class="btn btn-sm p-0 fs-3xl header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                <i class="ri-record-circle-line"></i>
            </button>
        </div>
        <div id="scrollbar">
            <div class="container-fluid">

                <div id="two-column-menu">
                </div>
                <ul class="navbar-nav" id="navbar-nav">
                    <li class="menu-title"><span>@lang('translation.casino')</span></li>
                    <li class="nav-item">
                        <a href="{{route('betting_list')}}" class="nav-link menu-link"> <i class="ph-dice-five"></i> <span data-key="t-betting/list">@lang('translation.mini-betting')</span> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('liveBetHistory')}}" class="nav-link menu-link"> <i class="ph-dice-five"></i> <span data-key="t-betHistory">@lang('translation.live-betting')</span> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('gameslist')}}" class="nav-link menu-link JQ_POPUPW"> <i class="ph-faders-horizontal"></i> <span data-key="t-gamesettings">@lang('translation.game-settings')</span> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('partner_tree')}}" class="nav-link menu-link"> <i class="ph-users"></i> <span data-key="t-partner/tree">@lang('translation.user-management')</span> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('dailyStatistics')}}" class="nav-link menu-link"> <i class="ph-presentation"></i> <span data-key="t-daily">@lang('translation.daily-statistics')</span> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('stagedStatistics')}}" class="nav-link menu-link"> <i class="ph-chart-bar"></i> <span data-key="t-staged">@lang('translation.staged-statistics')</span> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('chexdetails')}}" class="nav-link menu-link"> <i class="ph-trend-up"></i> <span data-key="t-chexdetails">@lang('translation.ch-ex-details')</span> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('board_list')}}" class="nav-link menu-link"> <i class="ph-microsoft-teams-logo"></i> <span data-key="t-board/index">@lang('translation.board-management')</span> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('message_list')}}" class="nav-link menu-link"> <i class="ph-paper-plane-right"></i> <span data-key="t-message/index">@lang('translation.site-mail')</span> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('siteSetting')}}" class="nav-link menu-link JQ_POPUPW"> <i class="ph-gear"></i> <span data-key="t-site/setting">@lang('translation.site-settings')</span> </a>
                    </li>
                </ul>
            </div>
            <!-- Sidebar -->
        </div>

        <div class="sidebar-background"></div>
    </div>
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>
