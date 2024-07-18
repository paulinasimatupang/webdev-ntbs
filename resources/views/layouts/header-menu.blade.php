    <div class="main-header">
            <div class="logo_bintang_header">
                <img src="{{asset('assets/images/icon_bintang/selada-transparant-final.png')}}" alt="">
            </div>

            <div class="menu-toggle">
                <div></div>
                        <div></div>
                <div></div>
            </div>


            <div style="margin: auto"></div>

            <div class="header-part-right">
                <!-- Full screen toggle -->
                <!-- <i class="i-Full-Screen header-icon d-none d-sm-inline-block" data-fullscreen></i> -->
              
                <!-- Notificaiton -->
              
                <!-- Notificaiton End -->

                <!-- User avatar dropdown -->
                <div class="dropdown">
                    <div  class="user col align-self-end">
                        <span style="font-size:24px;" class="nav-icon i-Administrator cursor-pointer" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></span>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            <div class="dropdown-header">
                                <i class="i-Lock-User mr-1"></i> {{ Auth::user()->fullname }}
                            </div>
                            <!-- <a class="dropdown-item" href="{{route('user-profile')}}">Ubah Profile</a> -->
                           
                            <a class="dropdown-item" href="{{route('logout')}}">Logout</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- header top menu end -->
