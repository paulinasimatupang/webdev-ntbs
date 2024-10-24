<div class="side-content-wrap">
    <div class="sidebar-left open rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
        <ul class="navigation-left">
            <li class="nav-item {{ request()->is('dashboard/*') ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{route('dashboard_version_13')}}">
                <img class="img_bintang_sidebar" src="{{asset('/assets/images/icon_bintang/dashboard.png')}}" alt="">
                    <span class="nav-text">Dashboard</span>
                </a>
                <div class="triangle"></div>
            </li>
         
            <li class="nav-item {{ request()->is('extrakits/*') ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{route('dashboard_version_6')}}">
                 
                    <img class="img_bintang_sidebar" src="{{asset('/assets/images/icon_bintang/transaction.png')}}"alt="">
                    <span class="nav-text">Transaksi</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('extrakits/*') ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{route('dashboard_version_7')}}">
                 
                    <img class="img_bintang_sidebar" src="{{asset('/assets/images/icon_bintang/revenue.png')}}" alt="">
                    <span class="nav-text">Revenue</span>
                </a>
                <div class="triangle"></div>
            </li>
           
          
        </ul>
    </div>

    <div class="sidebar-left-secondary rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
        <!-- Submenu Dashboards -->
        <ul class="childNav" data-parent="Transaksi">
            <li class="nav-item ">
                <a class="{{ Route::currentRouteName()=='dashboard_version_1' ? 'open' : '' }}"
                    href="{{route('dashboard_version_1')}}">
                    <i class="nav-icon i-Clock-3"></i>
                    <span class="item-name">Distributor Utama</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('dashboard_version_2')}}"
                    class="{{ Route::currentRouteName()=='dashboard_version_2' ? 'open' : '' }}">
                    <i class="nav-icon i-Clock-4"></i>
                    <span class="item-name">Distributor</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='dashboard_version_3' ? 'open' : '' }}"
                    href="{{route('dashboard_version_3')}}">
                    <i class="nav-icon i-Over-Time"></i>
                    <span class="item-name">Agen</span>
                </a>
            </li>
           
        </ul>
        <ul class="childNav" data-parent="forms">

            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='forms-basic' ? 'open' : '' }}" href="{{route('forms-basic')}}">
                    <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                    <span class="item-name">Basic Elements</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='basic-action-bar' ? 'open' : '' }}"
                    href="{{route('basic-action-bar')}}">
                    <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                    <span class="item-name">Basic action bar </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='form-layouts' ? 'open' : '' }}"
                    href="{{route('form-layouts')}}">
                    <i class="nav-icon i-Split-Vertical"></i>
                    <span class="item-name">Form Layouts</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='multi-column-forms' ? 'open' : '' }}"
                    href="{{route('multi-column-forms')}}">
                    <i class="nav-icon i-Split-Vertical"></i>
                    <span class="item-name">Multi column forms</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='form-input-group' ? 'open' : '' }}"
                    href="{{route('form-input-group')}}">
                    <i class="nav-icon i-Receipt-4"></i>
                    <span class="item-name">Input Groups</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='form-validation' ? 'open' : '' }}"
                    href="{{route('form-validation')}}">
                    <i class="nav-icon i-Close-Window"></i>
                    <span class="item-name">Form Validation</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='smartWizard' ? 'open' : '' }}" href="{{route('smartWizard')}}">
                    <i class="nav-icon i-Width-Window"></i>
                    <span class="item-name">Smart Wizard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='tagInput' ? 'open' : '' }}" href="{{route('tagInput')}}">
                    <i class="nav-icon i-Tag-2"></i>
                    <span class="item-name">Tag Input</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='form-editor' ? 'open' : '' }}" href="{{route('form-editor')}}">
                    <i class="nav-icon i-Pen-2"></i>
                    <span class="item-name">Rich Editor</span>
                </a>
            </li>
        </ul>
        <ul class="childNav" data-parent="widgets">
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='widget-card' ? 'open' : '' }}" href="{{route('widget-card')}}">
                    <i class="nav-icon i-Receipt-4"></i>
                    <span class="item-name">widget card</span>
                </a>
            </li>
            <li class="nav-item">


                <a class="{{ Route::currentRouteName()=='widget-statistics' ? 'open' : '' }}"
                    href="{{route('widget-statistics')}}">
                    <i class="nav-icon i-Receipt-4"></i>
                    <span class="item-name">widget statistics</span>
                </a>
            </li>

            <li class="nav-item">


                <a class="{{ Route::currentRouteName()=='widget-list' ? 'open' : '' }}" href="{{route('widget-list')}}">
                    <i class="nav-icon i-Receipt-4"></i>
                    <span class="item-name">Widget List <span class="ml-2 badge badge-pill badge-danger">
                            New</span></span>
                </a>
            </li>

            <li class="nav-item">


                <a class="{{ Route::currentRouteName()=='widget-app' ? 'open' : '' }}" href="{{route('widget-app')}}">
                    <i class="nav-icon i-Receipt-4"></i>
                    <span class="item-name">Widget App <span class="ml-2 badge badge-pill badge-danger"> New</span>
                    </span>
                </a>
            </li>
            <li class="nav-item">


                <a class="{{ Route::currentRouteName()=='widget-weather-app' ? 'open' : '' }}"
                    href="{{route('widget-weather-app')}}">
                    <i class="nav-icon i-Receipt-4"></i>
                    <span class="item-name"> Weather App <span class="ml-2 badge badge-pill badge-danger"> New</span>
                    </span>
                </a>
            </li>

        </ul>

        <ul class="childNav" data-parent="charts">
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='echarts' ? 'open' : '' }}" href="{{route('echarts')}}">
                    <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                    <span class="item-name">echarts</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='chartjs' ? 'open' : '' }}" href="{{route('chartjs')}}">
                    <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                    <span class="item-name">ChartJs</span>
                </a>
            </li>
            <li class="nav-item dropdown-sidemenu">
                <a>
                    <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                    <span class="item-name">Apex Charts</span>
                    <i class="dd-arrow i-Arrow-Down"></i>
                </a>
                <ul class="submenu">
                    <li><a class="{{ Route::currentRouteName()=='apexAreaCharts' ? 'open' : '' }}"
                            href="{{route('apexAreaCharts')}}">Area Charts</a></li>
                    <li><a class="{{ Route::currentRouteName()=='apexBarCharts' ? 'open' : '' }}"
                            href="{{route('apexBarCharts')}}">Bar Charts</a></li>
                    <li><a class="{{ Route::currentRouteName()=='apexBubbleCharts' ? 'open' : '' }}"
                            href="{{route('apexBubbleCharts')}}">Bubble Charts</a></li>
                    <li><a class="{{ Route::currentRouteName()=='apexColumnCharts' ? 'open' : '' }}"
                            href="{{route('apexColumnCharts')}}">Column Charts</a></li>
                    <li><a class="{{ Route::currentRouteName()=='apexCandleStickCharts' ? 'open' : '' }}"
                            href="{{route('apexCandleStickCharts')}}">CandleStick Charts</a></li>
                    <li><a class="{{ Route::currentRouteName()=='apexLineCharts' ? 'open' : '' }}"
                            href="{{route('apexLineCharts')}}">Line Charts</a></li>
                    <li><a class="{{ Route::currentRouteName()=='apexMixCharts' ? 'open' : '' }}"
                            href="{{route('apexMixCharts')}}">Mix Charts</a></li>
                    <li><a class="{{ Route::currentRouteName()=='apexPieDonutCharts' ? 'open' : '' }}"
                            href="{{route('apexPieDonutCharts')}}">PieDonut Charts</a></li>
                    <li><a class="{{ Route::currentRouteName()=='apexRadarCharts' ? 'open' : '' }}"
                            href="{{route('apexRadarCharts')}}">Radar Charts</a></li>
                    <li><a class="{{ Route::currentRouteName()=='apexRadialBarCharts' ? 'open' : '' }}"
                            href="{{route('apexRadialBarCharts')}}">RadialBar Charts</a></li>
                    <li><a class="{{ Route::currentRouteName()=='apexScatterCharts' ? 'open' : '' }}"
                            href="{{route('apexScatterCharts')}}">Scatter Charts</a></li>
                    <li><a class="{{ Route::currentRouteName()=='apexSparklineCharts' ? 'open' : '' }}"
                            href="{{route('apexSparklineCharts')}}">Sparkline Charts</a></li>

                </ul>
            </li>





        </ul>

        <ul class="childNav" data-parent="apps">
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='invoice' ? 'open' : '' }}" href="{{route('invoice')}}">
                    <i class="nav-icon i-Add-File"></i>
                    <span class="item-name">Invoice</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='inbox' ? 'open' : '' }}" href="{{route('inbox')}}">
                    <i class="nav-icon i-Email"></i>
                    <span class="item-name">Inbox</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='chat' ? 'open' : '' }}" href="{{route('chat')}}">
                    <i class="nav-icon i-Speach-Bubble-3"></i>
                    <span class="item-name">Chat</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='calendar' ? 'open' : '' }}" href="{{route('calendar')}}">
                    <i class="nav-icon i-Calendar-4"></i>
                    <span class="item-name">Calendar</span>
                </a>
            </li>
            <li class="nav-item dropdown-sidemenu">
                <a>
                    <i class="nav-icon i-Receipt"></i>
                    <span class="item-name">Task Manager <span
                            class=" ml-2 badge badge-pill badge-danger">New</span></span>
                    <i class="dd-arrow i-Arrow-Down"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a class="{{ Route::currentRouteName()=='task-manager' ? 'open' : '' }}"
                            href="{{route('task-manager')}}">
                            <i class="nav-icon i-Receipt"></i>
                            <span class="item-name">Task manager</span>
                        </a>
                    </li>
                    <li>
                        <a class="{{ Route::currentRouteName()=='task-manager-list' ? 'open' : '' }}"
                            href="{{route('task-manager-list')}}">
                            <i class="nav-icon i-Receipt-4"></i>
                            <span class="item-name">Task manager list</span>
                        </a>
                    </li>
                    <li>
                        <a class="{{ Route::currentRouteName()=='toDo' ? 'open' : '' }}" href="{{route('toDo')}}">
                            <i class="nav-icon i-Receipt-4"></i>
                            <span class="item-name">Minimal ToDo</span>
                        </a>
                    </li>
                    <li></li>
                </ul>
            </li>

            <li class="nav-item dropdown-sidemenu">
                <a>
                    <i class="nav-icon i-Cash-Register"></i>
                    <span class="item-name">Ecommerce <span
                            class=" ml-2 badge badge-pill badge-danger">New</span></span>
                    <i class="dd-arrow i-Arrow-Down"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a class="{{ Route::currentRouteName()=='ecommerce-products' ? 'open' : '' }}"
                            href="{{route('ecommerce-products')}}">
                            <i class="nav-icon i-Shop-2"></i>
                            <span class="item-name">Products</span>
                        </a>
                    </li>


                    <li>
                        <a class="{{ Route::currentRouteName()=='ecommerce-product-details' ? 'open' : '' }}"
                            href="{{route('ecommerce-product-details')}}">
                            <i class="nav-icon i-Tag-2"></i>
                            <span class="item-name">Product Details</span>
                        </a>
                    </li>
                    <li>
                        <a class="{{ Route::currentRouteName()=='ecommerce-cart' ? 'open' : '' }}"
                            href="{{route('ecommerce-cart')}}">
                            <i class="nav-icon i-Add-Cart"></i>
                            <span class="item-name">Cart</span>
                        </a>
                    </li>

                    <li>
                        <a class="{{ Route::currentRouteName()=='ecommerce-checkout' ? 'open' : '' }}"
                            href="{{route('ecommerce-checkout')}}">
                            <i class="nav-icon i-Cash-register-2"></i>
                            <span class="item-name">Checkout</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item dropdown-sidemenu">
                <a>
                    <i class="nav-icon i-Business-ManWoman"></i>
                    <span class="item-name">Contacts<span class=" ml-2 badge badge-pill badge-danger">New</span></span>
                    <i class="dd-arrow i-Arrow-Down"></i>
                </a>
                <ul class="submenu">

                    <li>
                        <a class="{{ Route::currentRouteName()=='contact-list-table' ? 'open' : '' }}"
                            href="{{route('contact-list-table')}}">
                            <i class="nav-icon i-Business-Mens"></i>
                            <span class="item-name">Contact Table
                                {{-- <span  class="ml-2 badge badge-pill badge-danger">New</span> --}}
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="{{ Route::currentRouteName()=='contacts-lists' ? 'open' : '' }}"
                            href="{{route('contacts-lists')}}">
                            <i class="nav-icon i-Business-Mens"></i>
                            <span class="item-name">Contact Lists</span>
                        </a>
                    </li>
                    <li>
                        <a class="{{ Route::currentRouteName()=='contacts-grid' ? 'open' : '' }}"
                            href="{{route('contacts-grid')}}">
                            <i class="nav-icon i-Conference"></i>
                            <span class="item-name">Contact Grid</span>
                        </a>
                    </li>
                    <li>
                        <a class="{{ Route::currentRouteName()=='contact-details' ? 'open' : '' }}"
                            href="{{route('contact-details')}}">
                            <i class="nav-icon i-Find-User"></i>
                            <span class="item-name">Contact Details</span>
                        </a>
                    </li>



                </ul>
            </li>


        </ul>
        <ul class="childNav" data-parent="extrakits">

            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='dropDown' ? 'open' : '' }}" href="{{route('dropDown')}}">
                    <i class="nav-icon i-Arrow-Down-in-Circle"></i>
                    <span class="item-name">Dropdown</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='imageCroper' ? 'open' : '' }}" href="{{route('imageCroper')}}">
                    <i class="nav-icon i-Crop-2"></i>
                    <span class="item-name">Image Cropper</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='loader' ? 'open' : '' }}" href="{{route('loader')}}">
                    <i class="nav-icon i-Loading-3"></i>
                    <span class="item-name">Loaders</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='laddaButton' ? 'open' : '' }}" href="{{route('laddaButton')}}">
                    <i class="nav-icon i-Loading-2"></i>
                    <span class="item-name">Ladda Buttons</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='toastr' ? 'open' : '' }}" href="{{route('toastr')}}">
                    <i class="nav-icon i-Bell"></i>
                    <span class="item-name">Toastr</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='sweetAlert' ? 'open' : '' }}" href="{{route('sweetAlert')}}">
                    <i class="nav-icon i-Approved-Window"></i>
                    <span class="item-name">Sweet Alerts</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='tour' ? 'open' : '' }}" href="{{route('tour')}}">
                    <i class="nav-icon i-Plane"></i>
                    <span class="item-name">User Tour</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='upload' ? 'open' : '' }}" href="{{route('upload')}}">
                    <i class="nav-icon i-Data-Upload"></i>
                    <span class="item-name">Upload</span>
                </a>
            </li>
        </ul>
        <ul class="childNav" data-parent="uikits">
           
        </ul>
        <ul class="childNav" data-parent="sessions">
            <li class="nav-item">
                <a href="{{route('signIn')}}">
                    <i class="nav-icon i-Checked-User"></i>
                    <span class="item-name">Sign in</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('signUp')}}">
                    <i class="nav-icon i-Add-User"></i>
                    <span class="item-name">Sign up</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('forgot')}}">
                    <i class="nav-icon i-Find-User"></i>
                    <span class="item-name">Forgot</span>
                </a>
            </li>
        </ul>
        <ul class="childNav" data-parent="others">
            <li class="nav-item">
                <a href="{{route('notFound')}}">
                    <i class="nav-icon i-Error-404-Window"></i>
                    <span class="item-name">Not Found</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='pricing-table' ? 'open' : '' }}"
                    href="{{route('pricing-table')}}">
                    <i class="nav-icon i-Billing"></i>
                    <span class="item-name">Pricing Table <span
                            class="ml-2 badge badge-pill badge-danger">New</span></span>
                </a>
            </li>

            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='search-result' ? 'open' : '' }}"
                    href="{{route('search-result')}}">
                    <i class="nav-icon i-File-Search"></i>
                    <span class="item-name">Search Result <span class="badge badge-pill badge-danger">New</span></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='user-profile' ? 'open' : '' }}"
                    href="{{route('user-profile')}}">
                    <i class="nav-icon i-Male"></i>
                    <span class="item-name">User Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='faq' ? 'open' : '' }}" href="{{route('faq')}}" class="open">
                    <i class="nav-icon i-File-Horizontal"></i>
                    <span class="item-name">faq</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='starter' ? 'open' : '' }}" href="{{route('starter')}}"
                    class="open">
                    <i class="nav-icon i-File-Horizontal"></i>
                    <span class="item-name">Blank Page</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-overlay"></div>
</div>
<!--=============== Left side End ================-->