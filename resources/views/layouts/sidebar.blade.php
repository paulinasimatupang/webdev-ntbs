<style type="text/css">
    #b1,
    #b2,
    #b3 {
        display: none;
    }

    .nav-item.active {
        position: relative;
        z-index: 1; 
    }

    .nav-item.active::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 5px; 
        z-index: -1; 
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
    }
</style>


@php
    $sidebarService = new \App\Services\SidebarService();
    $features = $sidebarService->getSidebarItems();
@endphp

<div class="side-content-wrap">
    {{-- @php if(session()->get('user')->role_id == 1) echo 'id="hide_menu_admin"'; @endphp --}}
    <div class="sidebar-left open rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
        <ul class="navigation-left">
            @if(isset($features['dashboard']))
                <li class="nav-item {{ (request()->is('landing') || request()->is('landing/*')) ? 'active' : '' }}">
                    <a class="nav-item-hold" href="{{route('landing')}}">
                        <img class="img_bintang_sidebar" src="{{asset('/assets/images/sidebar_icon/dashboard.png')}}"
                            alt="">
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
            @endif
            @if(isset($features['transaction']))
                <li class="nav-item {{ (request()->is('transaction') || request()->is('transaction/*')) ? 'active' : '' }}">
                    <a class="nav-item-hold" href="{{route('transaction')}}">
                        <img class="img_bintang_sidebar" src="{{asset('/assets/images/sidebar_icon/transaction.png')}}"
                            alt="">
                        <span class="nav-text">Transaksi</span>
                    </a>
                </li>
            @endif
            @if(isset($features['terminal']))
                        <li class="nav-item {{ (request()->is('terminal') || request()->is('terminal/*')) ? 'active' : '' }}" @php
                            if (session()->get('user')->role_id == 2)
                            echo 'id="b1"'; @endphp>
                            <a class="nav-item-hold" href="{{route('terminal')}}">

                                <img class="img_bintang_sidebar" src="{{asset('/assets/images/sidebar_icon/terminal.png')}}" alt="">
                                <span class="nav-text">Terminal</span>
                            </a>
                        </li>
            @endif
            @if(isset($features['agen']))
                <li class="nav-item {{ (request()->is('agen') || request()->is('agen/*')) ? 'active' : '' }}">
                    <a class="nav-item-hold" href="{{route('agen')}}">

                        <img class="img_bintang_sidebar" src="{{asset('/assets/images/sidebar_icon/agent.png')}}" alt="">
                        <span class="nav-text">Agen</span>
                    </a>
                </li>
            @endif
            @if(isset($features['nasabah']))
                <li class="nav-item {{ (request()->is('nasabah') || request()->is('nasabah/*')) ? 'active' : '' }}">
                    <a class="nav-item-hold" href="{{route('nasabah')}}">

                        <img class="img_bintang_sidebar" src="{{asset('/assets/images/sidebar_icon/nasabah.png')}}" alt="">
                        <span class="nav-text">Nasabah</span>
                    </a>
                </li>
            @endif
            @if(isset($features['masterdata']))
                <li class="nav-item {{ (request()->is('masterdata') || request()->is('masterdata/*')) || (request()->is('fee') || request()->is('*/fee/*')) || 
                (request()->is('persen_fee') || request()->is('persen_fee/*')) || (request()->is('roles') || request()->is('roles/*')) || (request()->is('users') || 
                request()->is('users/*')) || (request()->is('branch') || request()->is('branch/*')) || (request()->is('assesment') || request()->is('assesment/*'))
                ? 'active' : '' }}">
                    <a class="nav-item-hold" href="{{route('masterdata')}}">

                        <img class="img_bintang_sidebar" src="{{asset('/assets/images/sidebar_icon/masterdata.png')}}"
                            alt="">
                        <span class="nav-text">Master Data</span>
                    </a>
                </li>
            @endif
            @if(isset($features['pengaduan']))
                        <li class="nav-item {{ (request()->is('pengaduan') || request()->is('pengaduan/*')) ? 'active' : '' }}" @php
                            if (session()->get('user')->role_id == 2)
                        echo 'id="b1"'; @endphp>
                            <a class="nav-item-hold" href="{{route('pengaduan')}}">

                                <img class="img_bintang_sidebar" src="{{asset('/assets/images/sidebar_icon/pengaduan.png')}}" alt="">
                                <span class="nav-text">Pengaduan</span>
                            </a>
                        </li>
            @endif
            @if(isset($features['audit']))
                        <li class="nav-item {{ (request()->is('audit') || request()->is('audit/*')) ? 'active' : '' }}" @php
                            if (session()->get('user')->role_id == 2)
                        echo 'id="b1"'; @endphp>
                            <a class="nav-item-hold" href="{{route('audit')}}">

                                <img class="img_bintang_sidebar" src="{{asset('/assets/images/sidebar_icon/audit.png')}}" alt="">
                                <span class="nav-text">Audit</span>
                            </a>
                        </li>
            @endif
            @if(isset($features['message log']))
                        <li class="nav-item {{ (request()->is('message') || request()->is('message/*')) ? 'active' : '' }}" @php
                            if (session()->get('user')->role_id == 2)
                        echo 'id="b1"'; @endphp>
                            <a class="nav-item-hold" href="{{route('message_log')}}">

                                <img class="img_bintang_sidebar" src="{{asset('/assets/images/sidebar_icon/log.png')}}" alt="">
                                <span class="nav-text">Message Log</span>
                            </a>
                        </li>
            @endif
            @if(isset($features['biller']))
                        <li class="nav-item {{ (request()->is('biller') || request()->is('biller/*')) || (request()->is('parameter') || request()->is('parameter/*'))
                        || (request()->is('produk') || request()->is('produk/*')) || (request()->is('sub-produk') || request()->is('sub-produk/*'))? 'active' : '' }}" @php
                            if (session()->get('user')->role_id == 2)
                        echo 'id="b1"'; @endphp>
                            <a class="nav-item-hold" href="{{route('biller')}}">

                                <img class="img_bintang_sidebar" src="{{asset('/assets/images/sidebar_icon/biller.png')}}" alt="">
                                <span class="nav-text">Biller</span>
                            </a>
                        </li>
            @endif
            <!-- <li class="nav-item {{ request()->is('extrakits/*') ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{route('dashboard_version_11')}}">

                    <img class="img_bintang_sidebar" src="{{asset('/assets/images/sidebar_icon/version.png')}}" alt="">
                    <span class="nav-text">Version</span>
                </a>
                <div class="triangle"></div>
            </li> -->

        </ul>
    </div>

    <div class="sidebar-left-secondary rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
        <!-- Submenu Dashboards -->
        <ul class="childNav" data-parent="Transaksi">
            <li class="nav-item ">
                <a class="{{ Route::currentRouteName() == 'dashboard_version_1' ? 'open' : '' }}"
                    href="{{route('dashboard_version_1')}}">
                    <i class="nav-icon i-Clock-3"></i>
                    <span class="item-name">Distributor Utama</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('dashboard_version_2')}}"
                    class="{{ Route::currentRouteName() == 'dashboard_version_2' ? 'open' : '' }}">
                    <i class="nav-icon i-Clock-4"></i>
                    <span class="item-name">Distributor</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName() == 'dashboard_version_3' ? 'open' : '' }}"
                    href="{{route('dashboard_version_3')}}">
                    <i class="nav-icon i-Over-Time"></i>
                    <span class="item-name">Agen</span>
                </a>
            </li>

        </ul>
    </div>
    <div class="sidebar-overlay"></div>
</div>

<!--=============== Left side End ================-->